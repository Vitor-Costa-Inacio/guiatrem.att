<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gerenciamento de Sensores</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>
    <?php
    // Conexão com o banco de dados
    $host = 'localhost';
    $dbname = 'sistemas_sensores';
    $username = 'root';
    $password = 'root';
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Erro na conexão: " . $e->getMessage());
    }
    
    // Variáveis para mensagens
    $message = '';
    $message_type = ''; // success, danger, warning
    
    // Processamento do formulário
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verificar se é uma ação de exclusão
        if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id_sensor'])) {
            $id_sensor = $_POST['id_sensor'];
            
            try {
                $stmt = $pdo->prepare("DELETE FROM sensores WHERE id_sensor = ?");
                $stmt->execute([$id_sensor]);
                
                $message = "Sensor excluído com sucesso!";
                $message_type = "success";
            } catch(PDOException $e) {
                $message = "Erro ao excluir sensor: " . $e->getMessage();
                $message_type = "danger";
            }
        } else {
            // Processar o formulário de cadastro/edição
            $nome_sensor = trim($_POST['nome_sensor']);
            $tipo_sensor = trim($_POST['tipo_sensor']);
            $localizacao = trim($_POST['localizacao']);
            $status = $_POST['status'];
            
            // Validações básicas
            $errors = [];
            
            if (empty($nome_sensor)) {
                $errors[] = "O nome do sensor é obrigatório.";
            }
            
            if (empty($tipo_sensor)) {
                $errors[] = "O tipo do sensor é obrigatório.";
            }
            
            if (empty($localizacao)) {
                $errors[] = "A localização é obrigatória.";
            }
            
            if (strlen($nome_sensor) > 100) {
                $errors[] = "O nome do sensor deve ter no máximo 100 caracteres.";
            }
            
            if (strlen($tipo_sensor) > 50) {
                $errors[] = "O tipo do sensor deve ter no máximo 50 caracteres.";
            }
            
            if (strlen($localizacao) > 200) {
                $errors[] = "A localização deve ter no máximo 200 caracteres.";
            }
            
            if (empty($errors)) {
                try {
                    // Verificar se é uma edição (tem ID)
                    if (isset($_POST['id_sensor']) && !empty($_POST['id_sensor'])) {
                        $id_sensor = $_POST['id_sensor'];
                        $stmt = $pdo->prepare("UPDATE sensores SET nome_sensor = ?, tipo_sensor = ?, localizacao = ?, status = ? WHERE id_sensor = ?");
                        $stmt->execute([$nome_sensor, $tipo_sensor, $localizacao, $status, $id_sensor]);
                        
                        $message = "Sensor atualizado com sucesso!";
                        $message_type = "success";
                    } else {
                        // É um novo cadastro
                        $stmt = $pdo->prepare("INSERT INTO sensores (nome_sensor, tipo_sensor, localizacao, status) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$nome_sensor, $tipo_sensor, $localizacao, $status]);
                        
                        $message = "Sensor cadastrado com sucesso!";
                        $message_type = "success";
                    }
                } catch(PDOException $e) {
                    $message = "Erro ao salvar sensor: " . $e->getMessage();
                    $message_type = "danger";
                }
            } else {
                $message = implode("<br>", $errors);
                $message_type = "warning";
            }
        }
    }
    
    // Buscar sensores para exibir na tabela
    try {
        $stmt = $pdo->query("SELECT * FROM sensores ORDER BY id_sensor DESC");
        $sensores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        $sensores = [];
        $message = "Erro ao carregar sensores: " . $e->getMessage();
        $message_type = "danger";
    }
    
    // Verificar se é uma edição (parâmetro na URL)
    $editing = false;
    $sensor_edit = null;
    
    if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
        $id_editar = $_GET['editar'];
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM sensores WHERE id_sensor = ?");
            $stmt->execute([$id_editar]);
            $sensor_edit = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($sensor_edit) {
                $editing = true;
            }
        } catch(PDOException $e) {
            $message = "Erro ao carregar sensor para edição: " . $e->getMessage();
            $message_type = "danger";
        }
    }
    ?>
    
    <div class="container">
        <!-- Mensagens de feedback -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <i class="fas fa-<?php 
                    if ($message_type == 'success') echo 'check-circle';
                    elseif ($message_type == 'danger') echo 'exclamation-circle';
                    else echo 'exclamation-triangle';
                ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Card do formulário -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-<?php echo $editing ? 'edit' : 'plus'; ?>"></i>
                    <?php echo $editing ? 'Editar Sensor' : 'Cadastrar Novo Sensor'; ?>
                </h2>
            </div>
            
            <form method="POST" action="">
                <?php if ($editing): ?>
                    <input type="hidden" name="id_sensor" value="<?php echo $sensor_edit['id_sensor']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="nome_sensor">Nome do Sensor *</label>
                    <input 
                        type="text" 
                        id="nome_sensor" 
                        name="nome_sensor" 
                        class="form-control" 
                        placeholder="Ex: Sensor Temperatura 001"
                        value="<?php echo $editing ? htmlspecialchars($sensor_edit['nome_sensor']) : ''; ?>"
                        maxlength="100"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="tipo_sensor">Tipo de Sensor *</label>
                    <input 
                        type="text" 
                        id="tipo_sensor" 
                        name="tipo_sensor" 
                        class="form-control" 
                        placeholder="Ex: Temperatura, Vibração, Pressão"
                        value="<?php echo $editing ? htmlspecialchars($sensor_edit['tipo_sensor']) : ''; ?>"
                        maxlength="50"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="localizacao">Localização *</label>
                    <input 
                        type="text" 
                        id="localizacao" 
                        name="localizacao" 
                        class="form-control" 
                        placeholder="Ex: Vagão A - Compartimento Motor"
                        value="<?php echo $editing ? htmlspecialchars($sensor_edit['localizacao']) : ''; ?>"
                        maxlength="200"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="ativo" <?php echo ($editing && $sensor_edit['status'] == 'ativo') ? 'selected' : ''; ?>>Ativo</option>
                        <option value="inativo" <?php echo ($editing && $sensor_edit['status'] == 'inativo') ? 'selected' : ''; ?>>Inativo</option>
                    </select>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> 
                        <?php echo $editing ? 'Atualizar Sensor' : 'Cadastrar Sensor'; ?>
                    </button>
                    
                    <?php if ($editing): ?>
                        <a href="index.php" class="btn btn-warning">
                            <i class="fas fa-times"></i> Cancelar Edição
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Card da lista de sensores -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-list"></i>
                    Sensores Cadastrados
                </h2>
            </div>
            
            <?php if (count($sensores) > 0): ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Localização</th>
                                <th>Status</th>
                                <th>Data de Cadastro</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sensores as $sensor): ?>
                                <tr>
                                    <td><?php echo $sensor['id_sensor']; ?></td>
                                    <td><?php echo htmlspecialchars($sensor['nome_sensor']); ?></td>
                                    <td><?php echo htmlspecialchars($sensor['tipo_sensor']); ?></td>
                                    <td><?php echo htmlspecialchars($sensor['localizacao']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $sensor['status']; ?>">
                                            <?php echo ucfirst($sensor['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($sensor['data_cadastro'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="?editar=<?php echo $sensor['id_sensor']; ?>" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                            <form method="POST" action="" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id_sensor" value="<?php echo $sensor['id_sensor']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este sensor?')">
                                                    <i class="fas fa-trash"></i> Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>Nenhum sensor cadastrado. Use o formulário acima para adicionar um novo sensor.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <p>Sistema de Gerenciamento de Sensores &copy; <?php echo date('Y'); ?></p>
        </div>
    </footer>
</body>
</html>