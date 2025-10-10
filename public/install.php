<?php
/**
 * Script de Instalação do Sistema Guia Trem
 */

// Verificar se já foi instalado
if (file_exists('config/.installed')) {
    die('Sistema já foi instalado. Para reinstalar, remova o arquivo config/.installed');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = $_POST['host'] ?? 'localhost';
    $dbname = $_POST['dbname'] ?? 'guiatrem';
    $username = $_POST['username'] ?? 'root';
    $password = $_POST['password'] ?? 'root';
    
    try {
        // Testar conexão
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Criar banco de dados se não existir
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$dbname`");
        
        // Executar script SQL
        $sql = file_get_contents('database/banco.sql');
        $sql = str_replace('CREATE DATABASE IF NOT EXISTS guiatrem;', '', $sql);
        $sql = str_replace('USE guiatrem;', '', $sql);
        
        $statements = explode(';', $sql);
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        // Atualizar arquivo de configuração
        $configContent = file_get_contents('config/database.php');
        $configContent = str_replace("private \$host = 'localhost';", "private \$host = '$host';", $configContent);
        $configContent = str_replace("private \$db_name = 'guiatrem';", "private \$db_name = '$dbname';", $configContent);
        $configContent = str_replace("private \$username = 'root';", "private \$username = '$username';", $configContent);
        $configContent = str_replace("private \$password = '';", "private \$password = '$password';", $configContent);
        
        file_put_contents('config/database.php', $configContent);
        
        // Marcar como instalado
        file_put_contents('config/.installed', date('Y-m-d H:i:s'));
        
        $success = 'Sistema instalado com sucesso! Você pode agora acessar a aplicação.';
        
    } catch (Exception $e) {
        $error = 'Erro na instalação: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - Sistema Guia Trem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h2 class="mb-0">🚊 Sistema Guia Trem</h2>
                        <p class="mb-0">Instalação do Sistema</p>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= $success ?>
                            </div>
                            <div class="text-center">
                                <a href="index.php" class="btn btn-primary">Acessar Sistema</a>
                            </div>
                        <?php else: ?>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?= $error ?>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="host" class="form-label">Host do Banco de Dados</label>
                                    <input type="text" class="form-control" id="host" name="host" 
                                           value="localhost" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="dbname" class="form-label">Nome do Banco de Dados</label>
                                    <input type="text" class="form-control" id="dbname" name="dbname" 
                                           value="guiatrem" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="username" class="form-label">Usuário do Banco</label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="root" required>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="password" class="form-label">Senha do Banco</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        Instalar Sistema
                                    </button>
                                </div>
                            </form>
                            
                            <div class="mt-4">
                                <h6>Requisitos:</h6>
                                <ul class="small text-muted">
                                    <li>PHP 7.4 ou superior</li>
                                    <li>MySQL 5.7 ou superior</li>
                                    <li>Extensões: PDO, PDO_MySQL</li>
                                </ul>
                            </div>
                            
                        <?php endif; ?>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

