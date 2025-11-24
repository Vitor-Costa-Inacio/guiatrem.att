<?php
/**
 * API para Gerenciamento de Itinerários
 * Arquivo: /src/api/itinerarios.php
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Simulação de banco de dados (em produção, usar MySQL/PostgreSQL)
$itinerarios_file = __DIR__ . '/../data/itinerarios.json';

// Inicializar arquivo de dados se não existir
if (!file_exists($itinerarios_file)) {
    $dados_iniciais = [
        [
            'id' => 1,
            'nome' => 'Itinerário Norte-Sul',
            'descricao' => 'Rota principal conectando as regiões norte e sul',
            'trem_id' => 1,
            'rotas' => [1, 2, 3],
            'status' => 'ativo',
            'created_at' => '2023-01-15 10:00:00',
            'updated_at' => '2023-01-15 10:00:00'
        ],
        [
            'id' => 2,
            'nome' => 'Itinerário Litoral',
            'descricao' => 'Rota turística ao longo do litoral',
            'trem_id' => 2,
            'rotas' => [4, 5],
            'status' => 'ativo',
            'created_at' => '2023-02-20 14:30:00',
            'updated_at' => '2023-02-20 14:30:00'
        ]
    ];
    file_put_contents($itinerarios_file, json_encode($dados_iniciais, JSON_PRETTY_PRINT));
}

// Função para ler itinerários
function lerItinerarios() {
    global $itinerarios_file;
    return json_decode(file_get_contents($itinerarios_file), true);
}

// Função para salvar itinerários
function salvarItinerarios($itinerarios) {
    global $itinerarios_file;
    file_put_contents($itinerarios_file, json_encode($itinerarios, JSON_PRETTY_PRINT));
    return true;
}

// Processar requisição
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_all':
            $itinerarios = lerItinerarios();
            echo json_encode($itinerarios);
            break;

        case 'get':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                throw new Exception('ID do itinerário não especificado');
            }
            
            $itinerarios = lerItinerarios();
            $itinerario = array_filter($itinerarios, function($i) use ($id) {
                return $i['id'] == $id;
            });
            
            if (empty($itinerario)) {
                throw new Exception('Itinerário não encontrado');
            }
            
            echo json_encode(array_values($itinerario)[0]);
            break;

        case 'create':
        case 'update':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                throw new Exception('Dados inválidos');
            }
            
            $itinerarios = lerItinerarios();
            
            if ($action === 'create') {
                // Criar novo itinerário
                $novo_id = max(array_column($itinerarios, 'id')) + 1;
                $input['id'] = $novo_id;
                $input['created_at'] = date('Y-m-d H:i:s');
                $input['updated_at'] = date('Y-m-d H:i:s');
                $itinerarios[] = $input;
            } else {
                // Atualizar itinerário existente
                $encontrado = false;
                foreach ($itinerarios as &$itinerario) {
                    if ($itinerario['id'] == $input['id']) {
                        $itinerario = array_merge($itinerario, $input);
                        $itinerario['updated_at'] = date('Y-m-d H:i:s');
                        $encontrado = true;
                        break;
                    }
                }
                
                if (!$encontrado) {
                    throw new Exception('Itinerário não encontrado para atualização');
                }
            }
            
            salvarItinerarios($itinerarios);
            echo json_encode(['success' => true, 'message' => 'Itinerário salvo com sucesso']);
            break;

        case 'delete':
            $id = $_GET['id'] ?? null;
            if (!$id) {
                throw new Exception('ID do itinerário não especificado');
            }
            
            $itinerarios = lerItinerarios();
            $itinerarios = array_filter($itinerarios, function($i) use ($id) {
                return $i['id'] != $id;
            });
            
            salvarItinerarios(array_values($itinerarios));
            echo json_encode(['success' => true, 'message' => 'Itinerário excluído com sucesso']);
            break;

        default:
            throw new Exception('Ação não reconhecida');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>