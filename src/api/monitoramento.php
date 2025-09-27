<?php
/**
 * API de Monitoramento de Manutenção
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
            // Obter parâmetros de filtro
            $filtros = array(
                'linha' => $_GET['linha'] ?? '',
                'trem_id' => $_GET['trem_id'] ?? '',
                'tipo' => $_GET['tipo'] ?? '',
                'status' => $_GET['status'] ?? '',
                'data_inicio' => $_GET['data_inicio'] ?? '',
                'data_fim' => $_GET['data_fim'] ?? ''
            );

            // Listar manutenções com filtros
            $stmt_manutencoes = $manutencao->listar($filtros);
            $manutencoes = array();
            
            while ($row = $stmt_manutencoes->fetch(PDO::FETCH_ASSOC)) {
                $manutencoes[] = array(
                    'id' => $row['id_manutencao'],
                    'data' => $row['data_manutencao'],
                    'tipo' => $row['tipo_manutencao'],
                    'descricao' => $row['descricao_manutencao'],
                    'observacao' => $row['observacao_manutencao'],
                    'status' => $row['status_manutencao'],
                    'trem_id' => $row['fk_trem'],
                    'linha_trem' => $row['linha_trem'],
                    'tecnico' => $row['nome_usuario'],
                    'data_criacao' => $row['data_criacao']
                );
            }

            // Obter lista de trens para os filtros
            $stmt_trens = $trem->listar();
            $trens = array();
            
            while ($row = $stmt_trens->fetch(PDO::FETCH_ASSOC)) {
                $trens[] = array(
                    'id' => $row['id_trem'],
                    'linha' => $row['linha_trem']
                );
            }

            // Obter estatísticas
            $estatisticas = $manutencao->obterEstatisticas();

            $dados = array(
                'manutencoes' => $manutencoes,
                'trens' => $trens,
                'estatisticas' => $estatisticas
            );

            responderJSON(true, "Dados de monitoramento obtidos com sucesso.", $dados);
            break;

        case 'POST':
            // Criar nova manutenção
            $dados = obterDadosRequisicao();

            // Validar dados obrigatórios
            if (empty($dados['data_manutencao']) || empty($dados['tipo_manutencao']) || 
                empty($dados['descricao_manutencao']) || empty($dados['fk_trem'])) {
                responderJSON(false, "Dados obrigatórios não informados.", null, 400);
            }

            // Definir propriedades da manutenção
            $manutencao->data_manutencao = $dados['data_manutencao'];
            $manutencao->tipo_manutencao = $dados['tipo_manutencao'];
            $manutencao->descricao_manutencao = $dados['descricao_manutencao'];
            $manutencao->observacao_manutencao = $dados['observacao_manutencao'] ?? '';
            $manutencao->status_manutencao = $dados['status_manutencao'] ?? 'Pendente';
            $manutencao->fk_trem = $dados['fk_trem'];
            $manutencao->fk_usuario = $_SESSION['usuario_id'];

            if ($manutencao->criar()) {
                responderJSON(true, "Manutenção criada com sucesso.", null, 201);
            } else {
                responderJSON(false, "Erro ao criar manutenção.", null, 500);
            }
            break;

        case 'PUT':
            // Atualizar status da manutenção
            $dados = obterDadosRequisicao();

            if (empty($dados['id']) || empty($dados['status'])) {
                responderJSON(false, "ID e status são obrigatórios.", null, 400);
            }

            $observacao = $dados['observacao'] ?? null;

            if ($manutencao->atualizarStatus($dados['id'], $dados['status'], $observacao)) {
                responderJSON(true, "Status da manutenção atualizado com sucesso.");
            } else {
                responderJSON(false, "Erro ao atualizar status da manutenção.", null, 500);
            }
            break;

        default:
            responderJSON(false, "Método não permitido.", null, 405);
    }

} catch (Exception $e) {
    responderJSON(false, "Erro no monitoramento: " . $e->getMessage(), null, 500);
}
?>

