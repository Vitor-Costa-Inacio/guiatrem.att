<?php
/**
 * API do Dashboard
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

        // Obter estatísticas de manutenção
        $stats_manutencao = $manutencao->obterEstatisticas();

        // Obter contagem de trens por status
        $stmt_trens = $trem->contarPorStatus();
        $trens_status = array();
        while ($row = $stmt_trens->fetch(PDO::FETCH_ASSOC)) {
            $trens_status[] = $row;
        }

        // Obter manutenções recentes
        $stmt_recentes = $manutencao->obterRecentes(5);
        $manutencoes_recentes = array();
        while ($row = $stmt_recentes->fetch(PDO::FETCH_ASSOC)) {
            $manutencoes_recentes[] = array(
                'id' => $row['id_manutencao'],
                'data' => $row['data_manutencao'],
                'tipo' => $row['tipo_manutencao'],
                'descricao' => $row['descricao_manutencao'],
                'status' => $row['status_manutencao'],
                'linha_trem' => $row['linha_trem'],
                'tecnico' => $row['nome_usuario'],
                'data_criacao' => $row['data_criacao']
            );
        }

        // Dados simulados para localização da frota (pode ser implementado com dados reais)
        $localizacao_frota = array(
            'realizando_viagem' => 80,
            'estacao' => 10,
            'patio' => 4,
            'oficina' => 6
        );

        // Alertas simulados (pode ser implementado com lógica real)
        $alertas = array(
            array(
                'tipo' => 'info',
                'mensagem' => 'Trem 001 - Linha Amarela apresentando desgaste nas rodas.',
                'tempo' => '20 min'
            ),
            array(
                'tipo' => 'warning',
                'mensagem' => 'Trem 004 - Linha Azul apresentando luzes fracas dentro do vagão.',
                'tempo' => '1 hora'
            )
        );

        // Preparar resposta
        $dados_dashboard = array(
            'estatisticas_manutencao' => $stats_manutencao,
            'trens_por_status' => $trens_status,
            'manutencoes_recentes' => $manutencoes_recentes,
            'localizacao_frota' => $localizacao_frota,
            'alertas' => $alertas,
            'usuario' => obterDadosUsuario()
        );

        responderJSON(true, "Dados do dashboard obtidos com sucesso.", $dados_dashboard);

    } catch (Exception $e) {
        responderJSON(false, "Erro ao obter dados do dashboard: " . $e->getMessage(), null, 500);
    }

} else {
    responderJSON(false, "Método não permitido.", null, 405);
}
?>

