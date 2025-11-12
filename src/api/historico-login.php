<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/database.php';
require_once '../models/HistoricoLogin.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$database = new Database();
$db = $database->getConnection();
$historico = new HistoricoLogin($db);

// Simulação de usuário logado (em produção, usar sessão/JWT)
$usuario_id = 1;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    try {
        // Buscar estatísticas
        $estatisticas = $historico->buscarEstatisticas($usuario_id);

        // Buscar histórico
        $stmt = $historico->buscarPorUsuario($usuario_id);
        $logins = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $logins[] = [
                'id' => $row['id'],
                'data_hora' => $row['data_hora'],
                'dispositivo' => $row['dispositivo'],
                'navegador' => $row['navegador'],
                'sistema_operacional' => $row['sistema_operacional'],
                'localizacao' => $row['localizacao'],
                'endereco_ip' => $row['endereco_ip'],
                'status' => $row['status'],
                'user_agent' => $row['user_agent']
            ];
        }

        echo json_encode([
            'sucesso' => true,
            'estatisticas' => $estatisticas,
            'historico' => $logins
        ]);

    } catch (Exception $e) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
    }
}
?>