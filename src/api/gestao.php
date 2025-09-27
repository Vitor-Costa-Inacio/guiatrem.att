<?php
/**
 * API de Gestão de Rotas
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
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    try {
        // Instanciar modelos
        $trem = new Trem($db);
        $manutencao = new Manutencao($db);

        // Obter informações das linhas
        $query_linhas = "SELECT 
                           t.linha_trem,
                           COUNT(t.id_trem) as total_trens,
                           COUNT(CASE WHEN m.status_manutencao = 'Em andamento' THEN 1 END) as trens_manutencao,
                           COUNT(CASE WHEN m.status_manutencao IS NULL OR m.status_manutencao != 'Em andamento' THEN 1 END) as trens_operando
                         FROM trem t
                         LEFT JOIN manutencao m ON t.id_trem = m.fk_trem AND m.status_manutencao = 'Em andamento'
                         GROUP BY t.linha_trem
                         ORDER BY t.linha_trem";

        $stmt_linhas = $db->prepare($query_linhas);
        $stmt_linhas->execute();
        $linhas = array();
        
        while ($row = $stmt_linhas->fetch(PDO::FETCH_ASSOC)) {
            $status = 'ativa';
            $porcentagem_operacao = 0;
            
            if ($row['total_trens'] > 0) {
                $porcentagem_operacao = ($row['trens_operando'] / $row['total_trens']) * 100;
                
                if ($porcentagem_operacao < 50) {
                    $status = 'critica';
                } elseif ($porcentagem_operacao < 80) {
                    $status = 'atencao';
                }
            }

            $linhas[] = array(
                'nome' => $row['linha_trem'],
                'total_trens' => (int)$row['total_trens'],
                'trens_operando' => (int)$row['trens_operando'],
                'trens_manutencao' => (int)$row['trens_manutencao'],
                'porcentagem_operacao' => round($porcentagem_operacao, 1),
                'status' => $status
            );
        }

        // Obter alertas por linha
        $query_alertas = "SELECT 
                            t.linha_trem,
                            COUNT(CASE WHEN m.status_manutencao = 'Em andamento' AND m.tipo_manutencao = 'Corretiva' THEN 1 END) as alertas_criticos,
                            COUNT(CASE WHEN m.status_manutencao = 'Pendente' THEN 1 END) as alertas_pendentes
                          FROM trem t
                          LEFT JOIN manutencao m ON t.id_trem = m.fk_trem
                          GROUP BY t.linha_trem";

        $stmt_alertas = $db->prepare($query_alertas);
        $stmt_alertas->execute();
        $alertas_por_linha = array();
        
        while ($row = $stmt_alertas->fetch(PDO::FETCH_ASSOC)) {
            $alertas_por_linha[$row['linha_trem']] = array(
                'criticos' => (int)$row['alertas_criticos'],
                'pendentes' => (int)$row['alertas_pendentes']
            );
        }

        // Obter estatísticas gerais
        $estatisticas = $manutencao->obterEstatisticas();
        
        // Adicionar estatísticas de trens
        $query_trens_stats = "SELECT COUNT(*) as total_trens FROM trem";
        $stmt_trens_stats = $db->prepare($query_trens_stats);
        $stmt_trens_stats->execute();
        $trens_stats = $stmt_trens_stats->fetch(PDO::FETCH_ASSOC);

        // Obter manutenções urgentes
        $query_urgentes = "SELECT m.*, t.linha_trem 
                          FROM manutencao m
                          JOIN trem t ON m.fk_trem = t.id_trem
                          WHERE m.status_manutencao = 'Pendente' 
                            AND m.tipo_manutencao = 'Corretiva'
                          ORDER BY m.data_criacao ASC
                          LIMIT 5";

        $stmt_urgentes = $db->prepare($query_urgentes);
        $stmt_urgentes->execute();
        $manutencoes_urgentes = array();
        
        while ($row = $stmt_urgentes->fetch(PDO::FETCH_ASSOC)) {
            $manutencoes_urgentes[] = array(
                'id' => $row['id_manutencao'],
                'descricao' => $row['descricao_manutencao'],
                'linha_trem' => $row['linha_trem'],
                'data_criacao' => $row['data_criacao']
            );
        }

        // Preparar resposta
        $dados = array(
            'linhas' => $linhas,
            'alertas_por_linha' => $alertas_por_linha,
            'estatisticas' => array_merge($estatisticas, $trens_stats),
            'manutencoes_urgentes' => $manutencoes_urgentes
        );

        responderJSON(true, "Dados de gestão de rotas obtidos com sucesso.", $dados);

    } catch (Exception $e) {
        responderJSON(false, "Erro ao obter dados de gestão: " . $e->getMessage(), null, 500);
    }

} else {
    responderJSON(false, "Método não permitido.", null, 405);
}
?>

