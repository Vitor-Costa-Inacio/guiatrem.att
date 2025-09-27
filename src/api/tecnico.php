<?php
/**
 * API de Técnico de Manutenção
 */

// Incluir configurações
require_once '../../config/database.php';

// Configurar CORS
configurarCORS();

// Incluir arquivos necessários
incluirArquivos();
require_once '../models/Trem.php';
require_once '../models/Manutencao.php';

// Inicializar sessão
session_start();

// Verificar se o usuário está logado
include_once '../auth/check_session.php';
if (!verificarSessao()) {
    responderJSON(false, "Acesso negado. Faça login para continuar.", null, 401);
}

// Conectar ao banco de dados
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    responderJSON(false, "Erro de conexão com o banco de dados.", null, 500);
}

// Processar requisição
$metodo = $_SERVER['REQUEST_METHOD'];

try {
    // Instanciar modelos
    $trem = new Trem($db);
    $manutencao = new Manutencao($db);

    switch ($metodo) {
        case 'GET':
            $acao = $_GET['acao'] ?? 'listar';

            switch ($acao) {
                case 'solicitadas':
                    // Obter manutenções solicitadas (pendentes)
                    $filtros = array('status' => 'Pendente');
                    $stmt_solicitadas = $manutencao->listar($filtros);
                    $solicitadas = array();
                    
                    while ($row = $stmt_solicitadas->fetch(PDO::FETCH_ASSOC)) {
                        $solicitadas[] = array(
                            'id' => $row['id_manutencao'],
                            'data' => $row['data_manutencao'],
                            'tipo' => $row['tipo_manutencao'],
                            'descricao' => $row['descricao_manutencao'],
                            'linha_trem' => $row['linha_trem'],
                            'trem_id' => $row['fk_trem'],
                            'data_criacao' => $row['data_criacao']
                        );
                    }

                    responderJSON(true, "Manutenções solicitadas obtidas com sucesso.", $solicitadas);
                    break;

                case 'em_andamento':
                    // Obter manutenções em andamento do técnico atual
                    $filtros = array('status' => 'Em andamento');
                    $stmt_andamento = $manutencao->listar($filtros);
                    $em_andamento = array();
                    
                    while ($row = $stmt_andamento->fetch(PDO::FETCH_ASSOC)) {
                        if ($row['fk_usuario'] == $_SESSION['usuario_id']) {
                            $em_andamento[] = array(
                                'id' => $row['id_manutencao'],
                                'data' => $row['data_manutencao'],
                                'tipo' => $row['tipo_manutencao'],
                                'descricao' => $row['descricao_manutencao'],
                                'observacao' => $row['observacao_manutencao'],
                                'linha_trem' => $row['linha_trem'],
                                'trem_id' => $row['fk_trem'],
                                'data_criacao' => $row['data_criacao']
                            );
                        }
                    }

                    responderJSON(true, "Manutenções em andamento obtidas com sucesso.", $em_andamento);
                    break;

                case 'trens':
                    // Obter lista de trens
                    $stmt_trens = $trem->listar();
                    $trens = array();
                    
                    while ($row = $stmt_trens->fetch(PDO::FETCH_ASSOC)) {
                        $trens[] = array(
                            'id' => $row['id_trem'],
                            'linha' => $row['linha_trem']
                        );
                    }

                    responderJSON(true, "Lista de trens obtida com sucesso.", $trens);
                    break;

                default:
                    // Listar todas as manutenções do técnico
                    $stmt_todas = $manutencao->listar();
                    $todas = array();
                    
                    while ($row = $stmt_todas->fetch(PDO::FETCH_ASSOC)) {
                        if ($row['fk_usuario'] == $_SESSION['usuario_id'] || $_SESSION['usuario_funcao'] == 'admin') {
                            $todas[] = array(
                                'id' => $row['id_manutencao'],
                                'data' => $row['data_manutencao'],
                                'tipo' => $row['tipo_manutencao'],
                                'descricao' => $row['descricao_manutencao'],
                                'observacao' => $row['observacao_manutencao'],
                                'status' => $row['status_manutencao'],
                                'linha_trem' => $row['linha_trem'],
                                'trem_id' => $row['fk_trem'],
                                'tecnico' => $row['nome_usuario']
                            );
                        }
                    }

                    responderJSON(true, "Manutenções obtidas com sucesso.", $todas);
            }
            break;

        case 'POST':
            $acao = $_POST['acao'] ?? $_GET['acao'] ?? 'agendar';
            $dados = obterDadosRequisicao();

            switch ($acao) {
                case 'agendar':
                    // Agendar manutenção
                    if (empty($dados['manutencao_id']) || empty($dados['data']) || empty($dados['servico'])) {
                        responderJSON(false, "Dados obrigatórios não informados.", null, 400);
                    }

                    // Buscar a manutenção solicitada
                    if (!$manutencao->buscarPorId($dados['manutencao_id'])) {
                        responderJSON(false, "Manutenção não encontrada.", null, 404);
                    }

                    // Atualizar a manutenção com os dados do agendamento
                    $observacao = "Agendada para " . $dados['data'] . ". Serviço: " . $dados['servico'];
                    
                    if ($manutencao->atualizarStatus($dados['manutencao_id'], 'Em andamento', $observacao)) {
                        // Atualizar o técnico responsável
                        $query = "UPDATE manutencao SET fk_usuario = ?, data_manutencao = ? WHERE id_manutencao = ?";
                        $stmt = $db->prepare($query);
                        $stmt->execute([$_SESSION['usuario_id'], $dados['data'], $dados['manutencao_id']]);

                        responderJSON(true, "Manutenção agendada com sucesso.");
                    } else {
                        responderJSON(false, "Erro ao agendar manutenção.", null, 500);
                    }
                    break;

                case 'concluir':
                    // Concluir manutenção
                    if (empty($dados['manutencao_id']) || empty($dados['comentario'])) {
                        responderJSON(false, "ID da manutenção e comentário são obrigatórios.", null, 400);
                    }

                    // Verificar se a manutenção pertence ao técnico atual
                    if (!$manutencao->buscarPorId($dados['manutencao_id'])) {
                        responderJSON(false, "Manutenção não encontrada.", null, 404);
                    }

                    if ($manutencao->fk_usuario != $_SESSION['usuario_id'] && $_SESSION['usuario_funcao'] != 'admin') {
                        responderJSON(false, "Você não tem permissão para concluir esta manutenção.", null, 403);
                    }

                    $observacao_final = $manutencao->observacao_manutencao . "\n\nConclusão: " . $dados['comentario'];
                    
                    if ($manutencao->atualizarStatus($dados['manutencao_id'], 'Concluída', $observacao_final)) {
                        responderJSON(true, "Manutenção concluída com sucesso.");
                    } else {
                        responderJSON(false, "Erro ao concluir manutenção.", null, 500);
                    }
                    break;

                default:
                    responderJSON(false, "Ação não reconhecida.", null, 400);
            }
            break;

        default:
            responderJSON(false, "Método não permitido.", null, 405);
    }

} catch (Exception $e) {
    responderJSON(false, "Erro na API de técnico: " . $e->getMessage(), null, 500);
}
?>

