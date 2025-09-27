<?php
/**
 * API de Notificações
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
        $manutencao = new Manutencao($db);

        // Gerar notificações baseadas em eventos do sistema
        $notificacoes = array();

        // 1. Novas manutenções solicitadas (últimas 24 horas)
        $query_novas = "SELECT m.*, t.linha_trem 
                       FROM manutencao m
                       JOIN trem t ON m.fk_trem = t.id_trem
                       WHERE m.status_manutencao = 'Pendente' 
                         AND m.data_criacao >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                       ORDER BY m.data_criacao DESC";

        $stmt_novas = $db->prepare($query_novas);
        $stmt_novas->execute();
        
        while ($row = $stmt_novas->fetch(PDO::FETCH_ASSOC)) {
            $tempo_decorrido = calcularTempoDecorrido($row['data_criacao']);
            
            $notificacoes[] = array(
                'id' => 'nova_' . $row['id_manutencao'],
                'tipo' => 'nova_manutencao',
                'icone' => '🔧',
                'titulo' => 'Nova manutenção solicitada',
                'mensagem' => 'Uma nova manutenção foi solicitada para ' . $row['linha_trem'],
                'tempo' => $tempo_decorrido,
                'link' => 'tecnico.html',
                'data' => $row['data_criacao'],
                'lida' => false
            );
        }

        // 2. Manutenções concluídas (últimas 48 horas)
        $query_concluidas = "SELECT m.*, t.linha_trem 
                            FROM manutencao m
                            JOIN trem t ON m.fk_trem = t.id_trem
                            WHERE m.status_manutencao = 'Concluída' 
                              AND m.data_atualizacao >= DATE_SUB(NOW(), INTERVAL 48 HOUR)
                            ORDER BY m.data_atualizacao DESC";

        $stmt_concluidas = $db->prepare($query_concluidas);
        $stmt_concluidas->execute();
        
        while ($row = $stmt_concluidas->fetch(PDO::FETCH_ASSOC)) {
            $tempo_decorrido = calcularTempoDecorrido($row['data_atualizacao']);
            
            $notificacoes[] = array(
                'id' => 'concluida_' . $row['id_manutencao'],
                'tipo' => 'manutencao_concluida',
                'icone' => '✅',
                'titulo' => 'Manutenção concluída',
                'mensagem' => 'A manutenção da ' . $row['linha_trem'] . ' foi concluída',
                'tempo' => $tempo_decorrido,
                'link' => 'historico.html',
                'data' => $row['data_atualizacao'],
                'lida' => false
            );
        }

        // 3. Manutenções iniciadas (últimas 24 horas)
        $query_iniciadas = "SELECT m.*, t.linha_trem 
                           FROM manutencao m
                           JOIN trem t ON m.fk_trem = t.id_trem
                           WHERE m.status_manutencao = 'Em andamento' 
                             AND m.data_atualizacao >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                           ORDER BY m.data_atualizacao DESC";

        $stmt_iniciadas = $db->prepare($query_iniciadas);
        $stmt_iniciadas->execute();
        
        while ($row = $stmt_iniciadas->fetch(PDO::FETCH_ASSOC)) {
            $tempo_decorrido = calcularTempoDecorrido($row['data_atualizacao']);
            
            $notificacoes[] = array(
                'id' => 'iniciada_' . $row['id_manutencao'],
                'tipo' => 'manutencao_iniciada',
                'icone' => '🔧',
                'titulo' => 'Manutenção iniciada',
                'mensagem' => 'A manutenção da ' . $row['linha_trem'] . ' foi iniciada',
                'tempo' => $tempo_decorrido,
                'link' => 'tecnico.html',
                'data' => $row['data_atualizacao'],
                'lida' => false
            );
        }

        // 4. Notificações do sistema (simuladas)
        $notificacoes[] = array(
            'id' => 'sistema_1',
            'tipo' => 'sistema',
            'icone' => '🖥️',
            'titulo' => 'Atualização do sistema',
            'mensagem' => 'Atualização do sistema realizada com sucesso',
            'tempo' => '4 dias',
            'link' => null,
            'data' => date('Y-m-d H:i:s', strtotime('-4 days')),
            'lida' => true
        );

        // Ordenar notificações por data (mais recentes primeiro)
        usort($notificacoes, function($a, $b) {
            return strtotime($b['data']) - strtotime($a['data']);
        });

        // Limitar a 20 notificações mais recentes
        $notificacoes = array_slice($notificacoes, 0, 20);

        // Contar notificações não lidas
        $nao_lidas = count(array_filter($notificacoes, function($n) {
            return !$n['lida'];
        }));

        // Preparar resposta
        $dados = array(
            'notificacoes' => $notificacoes,
            'total' => count($notificacoes),
            'nao_lidas' => $nao_lidas
        );

        responderJSON(true, "Notificações obtidas com sucesso.", $dados);

    } catch (Exception $e) {
        responderJSON(false, "Erro ao obter notificações: " . $e->getMessage(), null, 500);
    }

} else {
    responderJSON(false, "Método não permitido.", null, 405);
}

/**
 * Calcula o tempo decorrido desde uma data
 */
function calcularTempoDecorrido($data) {
    $agora = new DateTime();
    $data_evento = new DateTime($data);
    $diferenca = $agora->diff($data_evento);

    if ($diferenca->days > 0) {
        if ($diferenca->days == 1) {
            return "1 dia";
        } elseif ($diferenca->days < 7) {
            return $diferenca->days . " dias";
        } elseif ($diferenca->days < 30) {
            $semanas = floor($diferenca->days / 7);
            return $semanas == 1 ? "1 sem" : $semanas . " sem";
        } else {
            $meses = floor($diferenca->days / 30);
            return $meses == 1 ? "1 mês" : $meses . " meses";
        }
    } elseif ($diferenca->h > 0) {
        return $diferenca->h == 1 ? "1 hora" : $diferenca->h . " horas";
    } elseif ($diferenca->i > 0) {
        return $diferenca->i . " min";
    } else {
        return "agora";
    }
}
?>

