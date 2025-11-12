<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config/database.php';
require_once '../models/Usuario.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

// Simulação de usuário logado (em produção, usar sessão/JWT)
$usuario_id = 1;

try {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // Buscar dados do perfil
        if ($usuario->buscarPerfilCompleto($usuario_id)) {
            echo json_encode([
                'sucesso' => true,
                'dados' => [
                    'nome_completo' => $usuario->nome_usuario,
                    'email' => $usuario->email_usuario,
                    'telefone' => $usuario->telefone,
                    'departamento' => $usuario->departamento,
                    'cargo' => $usuario->cargo,
                    'data_admissao' => $usuario->data_admissao,
                    'endereco' => $usuario->endereco,
                    'complemento' => $usuario->complemento,
                    'cidade' => $usuario->cidade,
                    'estado' => $usuario->estado,
                    'foto_perfil' => $usuario->foto_perfil,
                    'notificacoes_email' => $usuario->notificacoes_email,
                    'modo_escuro' => $usuario->modo_escuro,
                    'atualizacao_automatica' => $usuario->atualizacao_automatica,
                    'idioma' => $usuario->idioma,
                    'autenticacao_dois_fatores' => $usuario->autenticacao_dois_fatores,
                    'conta_verificada' => $usuario->conta_verificada
                ]
            ]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não encontrado']);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Atualizar perfil
        $dados = json_decode(file_get_contents('php://input'), true);
        
        $usuario->id_usuario = $usuario_id;
        $usuario->nome_usuario = $dados['nome_completo'] ?? '';
        $usuario->telefone = $dados['telefone'] ?? '';
        $usuario->departamento = $dados['departamento'] ?? '';
        $usuario->cargo = $dados['cargo'] ?? '';
        $usuario->data_admissao = $dados['data_admissao'] ?? '';
        $usuario->endereco = $dados['endereco'] ?? '';
        $usuario->complemento = $dados['complemento'] ?? '';
        $usuario->cidade = $dados['cidade'] ?? '';
        $usuario->estado = $dados['estado'] ?? '';
        $usuario->foto_perfil = $dados['foto_perfil'] ?? 'default-avatar.png';

        if ($usuario->atualizarPerfil()) {
            echo json_encode(['sucesso' => true, 'mensagem' => 'Perfil atualizado com sucesso']);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar perfil']);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        // Atualizar preferências
        $dados = json_decode(file_get_contents('php://input'), true);
        
        $usuario->id_usuario = $usuario_id;
        $usuario->notificacoes_email = $dados['notificacoes_email'] ?? false;
        $usuario->modo_escuro = $dados['modo_escuro'] ?? false;
        $usuario->atualizacao_automatica = $dados['atualizacao_automatica'] ?? false;
        $usuario->idioma = $dados['idioma'] ?? 'pt-BR';
        $usuario->autenticacao_dois_fatores = $dados['autenticacao_dois_fatores'] ?? false;

        if ($usuario->atualizarPreferencias()) {
            echo json_encode(['sucesso' => true, 'mensagem' => 'Preferências atualizadas com sucesso']);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar preferências']);
        }
    }
} catch (Exception $e) {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro: ' . $e->getMessage()]);
}
?>