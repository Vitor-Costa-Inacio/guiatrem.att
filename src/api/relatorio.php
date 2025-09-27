<?php
/**
 * API de Relatórios
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
        $tipo_relatorio = $_GET['tipo'] ?? 'geral';
        
        switch ($tipo_relatorio) {
            case 'geral':
                $dados = gerarRelatorioGeral($db);
                break;
                
            case 'frota':
                $dados = gerarRelatorioFrota($db);
                break;
                
            case 'incidentes':
                $dados = gerarRelatorioIncidentes($db);
                break;
                
            case 'custos':
                $dados = gerarRelatorioCustos($db);
                break;
                
            default:
                responderJSON(false, "Tipo de relatório não reconhecido.", null, 400);
        }

        responderJSON(true, "Relatório gerado com sucesso.", $dados);

    } catch (Exception $e) {
        responderJSON(false, "Erro ao gerar relatório: " . $e->getMessage(), null, 500);
    }

} else {
    responderJSON(false, "Método não permitido.", null, 405);
}

/**
 * Gera relatório geral integrado
 */
function gerarRelatorioGeral($db) {
    // Estatísticas gerais
    $query_geral = "SELECT 
                      (SELECT COUNT(*) FROM trem) as total_trens,
                      (SELECT COUNT(*) FROM manutencao) as total_manutencoes,
                      (SELECT COUNT(*) FROM manutencao WHERE status_manutencao = 'Concluída') as manutencoes_concluidas,
                      (SELECT COUNT(*) FROM manutencao WHERE status_manutencao = 'Em andamento') as manutencoes_andamento,
                      (SELECT COUNT(*) FROM manutencao WHERE tipo_manutencao = 'Preventiva') as manutencoes_preventivas,
                      (SELECT COUNT(*) FROM manutencao WHERE tipo_manutencao = 'Corretiva') as manutencoes_corretivas";

    $stmt = $db->prepare($query_geral);
    $stmt->execute();
    $estatisticas_gerais = $stmt->fetch(PDO::FETCH_ASSOC);

    // Desempenho por linha
    $query_linhas = "SELECT 
                       t.linha_trem,
                       COUNT(t.id_trem) as total_trens,
                       COUNT(m.id_manutencao) as total_manutencoes,
                       AVG(CASE WHEN m.status_manutencao = 'Concluída' 
                           THEN DATEDIFF(m.data_atualizacao, m.data_criacao) END) as tempo_medio_manutencao
                     FROM trem t
                     LEFT JOIN manutencao m ON t.id_trem = m.fk_trem
                     GROUP BY t.linha_trem
                     ORDER BY t.linha_trem";

    $stmt = $db->prepare($query_linhas);
    $stmt->execute();
    $desempenho_linhas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tendências mensais (últimos 12 meses)
    $query_tendencias = "SELECT 
                           DATE_FORMAT(data_manutencao, '%Y-%m') as mes,
                           COUNT(*) as total_manutencoes,
                           COUNT(CASE WHEN tipo_manutencao = 'Preventiva' THEN 1 END) as preventivas,
                           COUNT(CASE WHEN tipo_manutencao = 'Corretiva' THEN 1 END) as corretivas
                         FROM manutencao 
                         WHERE data_manutencao >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                         GROUP BY DATE_FORMAT(data_manutencao, '%Y-%m')
                         ORDER BY mes";

    $stmt = $db->prepare($query_tendencias);
    $stmt->execute();
    $tendencias_mensais = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array(
        'tipo' => 'geral',
        'estatisticas_gerais' => $estatisticas_gerais,
        'desempenho_linhas' => $desempenho_linhas,
        'tendencias_mensais' => $tendencias_mensais,
        'data_geracao' => date('Y-m-d H:i:s')
    );
}

/**
 * Gera relatório da frota em operação
 */
function gerarRelatorioFrota($db) {
    // Status atual da frota
    $query_status = "SELECT 
                       t.linha_trem,
                       COUNT(t.id_trem) as total_trens,
                       COUNT(CASE WHEN m.status_manutencao = 'Em andamento' THEN 1 END) as em_manutencao,
                       COUNT(CASE WHEN m.status_manutencao IS NULL OR m.status_manutencao != 'Em andamento' THEN 1 END) as operando
                     FROM trem t
                     LEFT JOIN manutencao m ON t.id_trem = m.fk_trem AND m.status_manutencao = 'Em andamento'
                     GROUP BY t.linha_trem
                     ORDER BY t.linha_trem";

    $stmt = $db->prepare($query_status);
    $stmt->execute();
    $status_frota = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Disponibilidade por linha (últimos 30 dias)
    $query_disponibilidade = "SELECT 
                                t.linha_trem,
                                COUNT(DISTINCT DATE(m.data_manutencao)) as dias_manutencao,
                                30 as dias_periodo,
                                ROUND((30 - COUNT(DISTINCT DATE(m.data_manutencao))) / 30 * 100, 2) as disponibilidade_pct
                              FROM trem t
                              LEFT JOIN manutencao m ON t.id_trem = m.fk_trem 
                                AND m.data_manutencao >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                              GROUP BY t.linha_trem";

    $stmt = $db->prepare($query_disponibilidade);
    $stmt->execute();
    $disponibilidade = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array(
        'tipo' => 'frota',
        'status_frota' => $status_frota,
        'disponibilidade' => $disponibilidade,
        'data_geracao' => date('Y-m-d H:i:s')
    );
}

/**
 * Gera relatório de incidentes registrados
 */
function gerarRelatorioIncidentes($db) {
    // Incidentes por tipo (manutenções corretivas)
    $query_incidentes = "SELECT 
                           t.linha_trem,
                           COUNT(*) as total_incidentes,
                           COUNT(CASE WHEN m.status_manutencao = 'Concluída' THEN 1 END) as resolvidos,
                           COUNT(CASE WHEN m.status_manutencao != 'Concluída' THEN 1 END) as pendentes,
                           AVG(CASE WHEN m.status_manutencao = 'Concluída' 
                               THEN DATEDIFF(m.data_atualizacao, m.data_criacao) END) as tempo_medio_resolucao
                         FROM manutencao m
                         JOIN trem t ON m.fk_trem = t.id_trem
                         WHERE m.tipo_manutencao = 'Corretiva'
                         GROUP BY t.linha_trem
                         ORDER BY total_incidentes DESC";

    $stmt = $db->prepare($query_incidentes);
    $stmt->execute();
    $incidentes_por_linha = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Incidentes por mês
    $query_mensal = "SELECT 
                       DATE_FORMAT(data_criacao, '%Y-%m') as mes,
                       COUNT(*) as total_incidentes,
                       COUNT(CASE WHEN status_manutencao = 'Concluída' THEN 1 END) as resolvidos
                     FROM manutencao 
                     WHERE tipo_manutencao = 'Corretiva'
                       AND data_criacao >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                     GROUP BY DATE_FORMAT(data_criacao, '%Y-%m')
                     ORDER BY mes";

    $stmt = $db->prepare($query_mensal);
    $stmt->execute();
    $incidentes_mensais = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Principais tipos de problemas
    $query_problemas = "SELECT 
                          descricao_manutencao,
                          COUNT(*) as ocorrencias
                        FROM manutencao 
                        WHERE tipo_manutencao = 'Corretiva'
                        GROUP BY descricao_manutencao
                        ORDER BY ocorrencias DESC
                        LIMIT 10";

    $stmt = $db->prepare($query_problemas);
    $stmt->execute();
    $principais_problemas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array(
        'tipo' => 'incidentes',
        'incidentes_por_linha' => $incidentes_por_linha,
        'incidentes_mensais' => $incidentes_mensais,
        'principais_problemas' => $principais_problemas,
        'data_geracao' => date('Y-m-d H:i:s')
    );
}

/**
 * Gera relatório de análise de custos e eficiência
 */
function gerarRelatorioCustos($db) {
    // Simulação de custos (em um sistema real, haveria uma tabela de custos)
    $custo_medio_preventiva = 1500.00;
    $custo_medio_corretiva = 3500.00;

    // Análise de custos por tipo
    $query_custos = "SELECT 
                       tipo_manutencao,
                       COUNT(*) as quantidade,
                       CASE 
                         WHEN tipo_manutencao = 'Preventiva' THEN COUNT(*) * {$custo_medio_preventiva}
                         WHEN tipo_manutencao = 'Corretiva' THEN COUNT(*) * {$custo_medio_corretiva}
                       END as custo_estimado
                     FROM manutencao 
                     WHERE status_manutencao = 'Concluída'
                     GROUP BY tipo_manutencao";

    $stmt = $db->prepare($query_custos);
    $stmt->execute();
    $custos_por_tipo = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Custos por linha
    $query_custos_linha = "SELECT 
                             t.linha_trem,
                             COUNT(CASE WHEN m.tipo_manutencao = 'Preventiva' THEN 1 END) as preventivas,
                             COUNT(CASE WHEN m.tipo_manutencao = 'Corretiva' THEN 1 END) as corretivas,
                             (COUNT(CASE WHEN m.tipo_manutencao = 'Preventiva' THEN 1 END) * {$custo_medio_preventiva} +
                              COUNT(CASE WHEN m.tipo_manutencao = 'Corretiva' THEN 1 END) * {$custo_medio_corretiva}) as custo_total
                           FROM trem t
                           LEFT JOIN manutencao m ON t.id_trem = m.fk_trem AND m.status_manutencao = 'Concluída'
                           GROUP BY t.linha_trem
                           ORDER BY custo_total DESC";

    $stmt = $db->prepare($query_custos_linha);
    $stmt->execute();
    $custos_por_linha = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Eficiência (relação preventiva/corretiva)
    $total_preventivas = 0;
    $total_corretivas = 0;
    
    foreach ($custos_por_tipo as $tipo) {
        if ($tipo['tipo_manutencao'] == 'Preventiva') {
            $total_preventivas = $tipo['quantidade'];
        } elseif ($tipo['tipo_manutencao'] == 'Corretiva') {
            $total_corretivas = $tipo['quantidade'];
        }
    }

    $eficiencia = array(
        'total_preventivas' => $total_preventivas,
        'total_corretivas' => $total_corretivas,
        'relacao_preventiva_corretiva' => $total_corretivas > 0 ? round($total_preventivas / $total_corretivas, 2) : 0,
        'economia_estimada' => ($total_corretivas * ($custo_medio_corretiva - $custo_medio_preventiva))
    );

    return array(
        'tipo' => 'custos',
        'custos_por_tipo' => $custos_por_tipo,
        'custos_por_linha' => $custos_por_linha,
        'eficiencia' => $eficiencia,
        'parametros' => array(
            'custo_medio_preventiva' => $custo_medio_preventiva,
            'custo_medio_corretiva' => $custo_medio_corretiva
        ),
        'data_geracao' => date('Y-m-d H:i:s')
    );
}
?>

