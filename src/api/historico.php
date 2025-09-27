<?php
/**
 * API de Histórico de Manutenção
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

        // Obter parâmetros de filtro
        $filtros = array(
            'linha' => $_GET['linha'] ?? '',
            'trem_id' => $_GET['trem_id'] ?? '',
            'tipo' => $_GET['tipo'] ?? '',
            'data_inicio' => $_GET['data_inicio'] ?? '',
            'data_fim' => $_GET['data_fim'] ?? '',
            'status' => 'Concluída' // Apenas manutenções concluídas no histórico
        );

        // Se não há filtro de data, mostrar últimos 6 meses
        if (empty($filtros['data_inicio'])) {
            $filtros['data_inicio'] = date('Y-m-d', strtotime('-6 months'));
        }

        // Listar manutenções do histórico
        $stmt_historico = $manutencao->listar($filtros);
        $historico = array();
        
        while ($row = $stmt_historico->fetch(PDO::FETCH_ASSOC)) {
            $historico[] = array(
                'id' => $row['id_manutencao'],
                'data' => $row['data_manutencao'],
                'tipo' => $row['tipo_manutencao'],
                'descricao' => $row['descricao_manutencao'],
                'observacao' => $row['observacao_manutencao'],
                'status' => $row['status_manutencao'],
                'trem_id' => $row['fk_trem'],
                'linha_trem' => $row['linha_trem'],
                'tecnico' => $row['nome_usuario'],
                'data_criacao' => $row['data_criacao'],
                'data_atualizacao' => $row['data_atualizacao']
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

        // Obter estatísticas do histórico
        $query_stats = "SELECT 
                          COUNT(*) as total_concluidas,
                          COUNT(CASE WHEN tipo_manutencao = 'Preventiva' THEN 1 END) as preventivas,
                          COUNT(CASE WHEN tipo_manutencao = 'Corretiva' THEN 1 END) as corretivas,
                          AVG(DATEDIFF(data_atualizacao, data_criacao)) as tempo_medio_dias
                        FROM manutencao 
                        WHERE status_manutencao = 'Concluída'";

        if (!empty($filtros['data_inicio'])) {
            $query_stats .= " AND data_manutencao >= '" . $filtros['data_inicio'] . "'";
        }
        if (!empty($filtros['data_fim'])) {
            $query_stats .= " AND data_manutencao <= '" . $filtros['data_fim'] . "'";
        }

        $stmt_stats = $db->prepare($query_stats);
        $stmt_stats->execute();
        $estatisticas = $stmt_stats->fetch(PDO::FETCH_ASSOC);

        // Obter manutenções por mês (últimos 12 meses)
        $query_mensal = "SELECT 
                           DATE_FORMAT(data_manutencao, '%Y-%m') as mes,
                           COUNT(*) as total,
                           COUNT(CASE WHEN tipo_manutencao = 'Preventiva' THEN 1 END) as preventivas,
                           COUNT(CASE WHEN tipo_manutencao = 'Corretiva' THEN 1 END) as corretivas
                         FROM manutencao 
                         WHERE status_manutencao = 'Concluída' 
                           AND data_manutencao >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                         GROUP BY DATE_FORMAT(data_manutencao, '%Y-%m')
                         ORDER BY mes DESC";

        $stmt_mensal = $db->prepare($query_mensal);
        $stmt_mensal->execute();
        $dados_mensais = array();
        
        while ($row = $stmt_mensal->fetch(PDO::FETCH_ASSOC)) {
            $dados_mensais[] = $row;
        }

        // Preparar resposta
        $dados = array(
            'historico' => $historico,
            'trens' => $trens,
            'estatisticas' => $estatisticas,
            'dados_mensais' => $dados_mensais,
            'filtros_aplicados' => $filtros
        );

        responderJSON(true, "Histórico de manutenções obtido com sucesso.", $dados);

    } catch (Exception $e) {
        responderJSON(false, "Erro ao obter histórico: " . $e->getMessage(), null, 500);
    }

} else {
    responderJSON(false, "Método não permitido.", null, 405);
}
?>

