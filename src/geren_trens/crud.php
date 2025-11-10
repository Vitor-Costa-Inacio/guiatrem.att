<?php
// Configuração do Banco de Dados
class Database {
    private $host = "localhost";
    private $db_name = "gerenciamento_trem";
    private $username = "root";
    private $password = "root";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            die("Erro de conexão: " . $exception->getMessage());
        }
        return $this->conn;
    }
}

// Inicializar conexão
$database = new Database();
$db = $database->getConnection();

// Variáveis para mensagens
$message = '';
$error = '';

// PROCESSAR OPERAÇÕES CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CREATE - Adicionar Trem
    if (isset($_POST['add_train'])) {
        try {
            $query = "INSERT INTO trens (numero_trem, modelo, linha, capacidade, status_trem, data_ultima_manutencao) 
                     VALUES (:numero_trem, :modelo, :linha, :capacidade, :status_trem, :data_ultima_manutencao)";
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(':numero_trem', $_POST['numero_trem']);
            $stmt->bindParam(':modelo', $_POST['modelo']);
            $stmt->bindParam(':linha', $_POST['linha']);
            $stmt->bindParam(':capacidade', $_POST['capacidade']);
            $stmt->bindParam(':status_trem', $_POST['status_trem']);
            $stmt->bindParam(':data_ultima_manutencao', $_POST['data_ultima_manutencao']);
            
            if ($stmt->execute()) {
                $message = "Trem adicionado com sucesso!";
            }
        } catch (PDOException $e) {
            $error = "Erro ao adicionar trem: " . $e->getMessage();
        }
    }
    
    // UPDATE - Editar Trem
    if (isset($_POST['edit_train'])) {
        try {
            $query = "UPDATE trens SET 
                     numero_trem = :numero_trem,
                     modelo = :modelo,
                     linha = :linha,
                     capacidade = :capacidade,
                     status_trem = :status_trem,
                     data_ultima_manutencao = :data_ultima_manutencao
                     WHERE id_trem = :id_trem";
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(':id_trem', $_POST['id_trem']);
            $stmt->bindParam(':numero_trem', $_POST['numero_trem']);
            $stmt->bindParam(':modelo', $_POST['modelo']);
            $stmt->bindParam(':linha', $_POST['linha']);
            $stmt->bindParam(':capacidade', $_POST['capacidade']);
            $stmt->bindParam(':status_trem', $_POST['status_trem']);
            $stmt->bindParam(':data_ultima_manutencao', $_POST['data_ultima_manutencao']);
            
            if ($stmt->execute()) {
                $message = "Trem atualizado com sucesso!";
            }
        } catch (PDOException $e) {
            $error = "Erro ao atualizar trem: " . $e->getMessage();
        }
    }
    
    // CREATE - Solicitar Manutenção
    if (isset($_POST['solicitar_manutencao'])) {
        try {
            $query = "INSERT INTO manutencoes (id_trem, tipo_manutencao, prioridade, servico_solicitado, status_manutencao, data_prevista) 
                     VALUES (:id_trem, :tipo_manutencao, :prioridade, :servico_solicitado, 'nao_iniciada', :data_prevista)";
            $stmt = $db->prepare($query);
            
            $stmt->bindParam(':id_trem', $_POST['trem']);
            $stmt->bindParam(':tipo_manutencao', $_POST['tipo_manutencao']);
            $stmt->bindParam(':prioridade', $_POST['prioridade']);
            $stmt->bindParam(':servico_solicitado', $_POST['servico']);
            $stmt->bindParam(':data_prevista', $_POST['data_prevista']);
            
            if ($stmt->execute()) {
                $message = "Manutenção solicitada com sucesso!";
            }
        } catch (PDOException $e) {
            $error = "Erro ao solicitar manutenção: " . $e->getMessage();
        }
    }
}

// DELETE - Excluir Trem
if (isset($_GET['delete_id'])) {
    try {
        $query = "DELETE FROM trens WHERE id_trem = :id_trem";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_trem', $_GET['delete_id']);
        
        if ($stmt->execute()) {
            $message = "Trem excluído com sucesso!";
        }
    } catch (PDOException $e) {
        $error = "Erro ao excluir trem: " . $e->getMessage();
    }
}

// Buscar dados para edição
$edit_trem = null;
if (isset($_GET['edit_id'])) {
    try {
        $query = "SELECT * FROM trens WHERE id_trem = :id_trem";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_trem', $_GET['edit_id']);
        $stmt->execute();
        $edit_trem = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Erro ao carregar dados do trem: " . $e->getMessage();
    }
}

// Buscar todos os trens
try {
    $query = "SELECT * FROM trens ORDER BY id_trem DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $trens = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erro ao carregar trens: " . $e->getMessage();
}

// Buscar manutenções
try {
    $query_manutencoes = "SELECT m.*, t.numero_trem, t.linha 
                         FROM manutencoes m 
                         JOIN trens t ON m.id_trem = t.id_trem 
                         ORDER BY m.data_prevista ASC";
    $stmt_manutencoes = $db->prepare($query_manutencoes);
    $stmt_manutencoes->execute();
    $manutencoes = $stmt_manutencoes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erro ao carregar manutenções: " . $e->getMessage();
}

// Buscar estatísticas
try {
    // Estatísticas de trens
    $query_stats = "SELECT 
        COUNT(*) as total_trens,
        SUM(CASE WHEN status_trem = 'ativo' THEN 1 ELSE 0 END) as ativos,
        SUM(CASE WHEN status_trem = 'manutencao' THEN 1 ELSE 0 END) as em_manutencao,
        SUM(CASE WHEN status_trem = 'inativo' THEN 1 ELSE 0 END) as inativos
        FROM trens";
    $stmt_stats = $db->prepare($query_stats);
    $stmt_stats->execute();
    $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);
    
    // Estatísticas de manutenções
    $query_manut_stats = "SELECT 
        COUNT(*) as total_manutencoes,
        SUM(CASE WHEN tipo_manutencao = 'preventiva' THEN 1 ELSE 0 END) as preventivas,
        SUM(CASE WHEN tipo_manutencao = 'corretiva' THEN 1 ELSE 0 END) as corretivas,
        SUM(CASE WHEN status_manutencao = 'em_andamento' THEN 1 ELSE 0 END) as em_andamento
        FROM manutencoes";
    $stmt_manut_stats = $db->prepare($query_manut_stats);
    $stmt_manut_stats->execute();
    $manut_stats = $stmt_manut_stats->fetch(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Erro ao carregar estatísticas: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoramento de Manutenção - CRUD</title>
    <link rel="stylesheet" href="../css/monitoramento.css">
    <link rel="stylesheet" href="../css/notificacao.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <style>
        .crud-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .table-actions {
            white-space: nowrap;
        }
        .btn-action {
            margin-right: 5px;
        }
        .alert-message {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1050;
            min-width: 300px;
        }
        .modal-header {
            background: #0d6efd;
            color: white;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar bg-primary-emphasis fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand text-white d-flex gap-2" href="dashboard.html">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="30" fill="currentColor"
                        class="bi bi-train-freight-front" viewBox="0 0 16 16">
                        <path
                            d="M5.065.158A1.5 1.5 0 0 1 5.736 0h4.528a1.5 1.5 0 0 1 .67.158l3.237 1.618a1.5 1.5 0 0 1 .83 1.342V13.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 13.5V3.118a1.5 1.5 0 0 1 .828-1.342zM2 9.372V13.5A1.5 1.5 0 0 0 3.5 15h4V8h-.853a.5.5 0 0 0-.144.021zM8.5 15h4a1.5 1.5 0 0 0 1.5-1.5V9.372l-4.503-1.35A.5.5 0 0 0 9.353 8H8.5zM14 8.328v-5.21a.5.5 0 0 0-.276-.447l-3.236-1.618A.5.5 0 0 0 10.264 1H5.736a.5.5 0 0 0-.223.053L2.277 2.67A.5.5 0 0 0 2 3.118v5.21l1-.3V5a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v3.028zm-2-.6V5H8.5v2h.853a1.5 1.5 0 0 1 .431.063zM7.5 7V5H4v2.728l2.216-.665A1.5 1.5 0 0 1 6.646 7zm-1-5a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm-3 8a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1m9 0a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1M5 13a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                    </svg> Guia Trem
                </a>

                <div class="d-flex">
                    <div class="d-flex align-items-center">
                        <button class="navbar-toggler navbar-dark text-white" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Menu Lateral (mantido igual) -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header bg-primary-infa text-white gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="30" fill="currentColor"
                        class="bi bi-train-freight-front" viewBox="0 0 16 16">
                        <path
                            d="M5.065.158A1.5 1.5 0 0 1 5.736 0h4.528a1.5 1.5 0 0 1 .67.158l3.237 1.618a1.5 1.5 0 0 1 .83 1.342V13.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 13.5V3.118a1.5 1.5 0 0 1 .828-1.342zM2 9.372V13.5A1.5 1.5 0 0 0 3.5 15h4V8h-.853a.5.5 0 0 0-.144.021zM8.5 15h4a1.5 1.5 0 0 0 1.5-1.5V9.372l-4.503-1.35A.5.5 0 0 0 9.353 8H8.5zM14 8.328v-5.21a.5.5 0 0 0-.276-.447l-3.236-1.618A.5.5 0 0 0 10.264 1H5.736a.5.5 0 0 0-.223.053L2.277 2.67A.5.5 0 0 0 2 3.118v5.21l1-.3V5a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v3.028zm-2-.6V5H8.5v2h.853a1.5 1.5 0 0 1 .431.063zM7.5 7V5H4v2.728l2.216-.665A1.5 1.5 0 0 1 6.646 7zm-1-5a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm-3 8a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1m9 0a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1M5 13a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                    </svg>
                    <h5 class="offcanvas-title pg-1" id="offcanvasNavbarLabel">Guia Trem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>

                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                        <!-- Menu items mantidos iguais -->
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <li class="nav-item list-unstyled ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-house-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z" />
                                        <path
                                            d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z" />
                                    </svg>
                                </li>
                            </div>
                            <div class="col">
                                <a class="nav-link active" aria-current="page" href="dashboard.html">Início</a>
                            </div>
                        </div>
                        <!-- ... outros itens do menu ... -->
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Mensagens de Alerta -->
    <?php if ($message): ?>
        <div class="alert alert-success alert-message alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-message alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <main class="main-content" style="margin-top: 80px;">
        <div class="container-fluid">
            
            <!-- SEÇÃO CRUD DE Trens -->
            <div class="crud-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Gerenciamento de Trens</h2>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#trainModal">
                        <i class="bi bi-plus-circle"></i> Adicionar Trem
                    </button>
                </div>

                <!-- Tabela de Trens -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Número</th>
                                <th>Modelo</th>
                                <th>Linha</th>
                                <th>Capacidade</th>
                                <th>Status</th>
                                <th>Última Manutenção</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($trens as $trem): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($trem['numero_trem']); ?></strong></td>
                                <td><?php echo htmlspecialchars($trem['modelo']); ?></td>
                                <td>
                                    <span class="badge 
                                        <?php 
                                        switch($trem['linha']) {
                                            case 'Linha Azul': echo 'bg-primary'; break;
                                            case 'Linha Verde': echo 'bg-success'; break;
                                            case 'Linha Vermelha': echo 'bg-danger'; break;
                                            case 'Linha Amarela': echo 'bg-warning'; break;
                                            default: echo 'bg-secondary';
                                        }
                                        ?>">
                                        <?php echo htmlspecialchars($trem['linha']); ?>
                                    </span>
                                </td>
                                <td><?php echo $trem['capacidade']; ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        switch($trem['status_trem']) {
                                            case 'ativo': echo 'success'; break;
                                            case 'manutencao': echo 'warning'; break;
                                            case 'inativo': echo 'danger'; break;
                                            default: echo 'secondary';
                                        }
                                    ?>">
                                        <?php echo ucfirst($trem['status_trem']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($trem['data_ultima_manutencao']): ?>
                                        <?php echo date('d/m/Y', strtotime($trem['data_ultima_manutencao'])); ?>
                                    <?php else: ?>
                                        <span class="text-muted">Nunca</span>
                                    <?php endif; ?>
                                </td>
                                <td class="table-actions">
                                    <button class="btn btn-sm btn-warning btn-action" 
                                            onclick="editTrain(<?php echo $trem['id_trem']; ?>)">
                                        Editar
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-action" 
                                            onclick="confirmDelete(<?php echo $trem['id_trem']; ?>, '<?php echo htmlspecialchars($trem['numero_trem']); ?>')">
                                        Excluir
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- SEÇÃO DE ESTATÍSTICAS -->
            <div class="flex">
                <div class="manutencoes-andamento">
                    <div class="txt">
                        <div class="txt_titulo">
                            <h2>Manutenções</h2>
                            <h3>em andamento</h3>
                        </div>
                        <div class="flex">
                            <div id="preventivas">
                                <h1><?php echo $manut_stats['preventivas'] ?? 0; ?></h1>
                                <hr>
                                <h2>Preventivas</h2>
                            </div>
                            <div id="corretivas">
                                <h1><?php echo $manut_stats['corretivas'] ?? 0; ?></h1>
                                <hr>
                                <h2>Corretivas</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="gasto-mensal">
                    <h2>Total de Trens</h2>
                    <h3>Status Geral</h3>
                    <p><?php echo $stats['total_trens'] ?? 0; ?> trens</p>
                </div>
            </div>

            <!-- MANUTENÇÕES PROGRAMADAS -->
            <div class="manutencoes-programadas">
                <h2>Manutenções Programadas</h2>
                <?php foreach($manutencoes as $manutencao): ?>
                <div class="trem">
                    <div class="linha-nome <?php echo strtolower(explode(' ', $manutencao['linha'])[1]); ?>">
                        <?php echo $manutencao['numero_trem']; ?> - <?php echo $manutencao['linha']; ?>
                    </div>
                    <div class="data"><?php echo date('d/m/Y', strtotime($manutencao['data_prevista'])); ?></div>
                    <div class="status">
                        <span class="status-indicator 
                            <?php 
                            switch($manutencao['status_manutencao']) {
                                case 'concluida': echo 'concluida'; break;
                                case 'em_andamento': echo 'andamento'; break;
                                case 'nao_iniciada': echo 'nao-iniciada'; break;
                            }
                            ?>"></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- FORMULÁRIO DE SOLICITAÇÃO DE MANUTENÇÃO -->
            <form action="" method="POST">
                <div class="solicitar-manutencao">
                    <h2>Solicitar uma Manutenção</h2>

                    <input type="hidden" name="solicitar_manutencao" value="1">

                    <label for="trem">Trem</label>
                    <select class="form-select dropAzul" name="trem" required>
                        <option value="">Selecione...</option>
                        <?php foreach($trens as $trem): ?>
                        <option value="<?php echo $trem['id_trem']; ?>">
                            <?php echo $trem['numero_trem']; ?> - <?php echo $trem['linha']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="tipo_manutencao">Tipo de manutenção</label>
                    <select class="form-select dropAzul" name="tipo_manutencao" required>
                        <option value="">Selecione...</option>
                        <option value="preventiva">Preventiva</option>
                        <option value="corretiva">Corretiva</option>
                    </select>

                    <label for="prioridade">Prioridade</label>
                    <select class="form-select dropAzul" name="prioridade" required>
                        <option value="">Selecione...</option>
                        <option value="A">A (crítico)</option>
                        <option value="B">B (importante)</option>
                        <option value="C">C (menos crítico)</option>
                    </select>

                    <label for="servico">Serviço a ser realizado</label>
                    <input type="text" name="servico" placeholder="Digite o serviço..." required>

                    <label for="data_prevista">Data Prevista</label>
                    <input type="date" name="data_prevista" required>

                    <button type="submit" class="btn btn-primary">Enviar solicitação</button>
                </div>
            </form>
        </div>
    </main>

    <!-- MODAL PARA ADICIONAR/EDITAR TREM -->
    <div class="modal fade" id="trainModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <?php echo $edit_trem ? 'Editar Trem' : 'Adicionar Novo Trem'; ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <?php if ($edit_trem): ?>
                            <input type="hidden" name="id_trem" value="<?php echo $edit_trem['id_trem']; ?>">
                            <input type="hidden" name="edit_train" value="1">
                        <?php else: ?>
                            <input type="hidden" name="add_train" value="1">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="numero_trem" class="form-label">Número do Trem</label>
                            <input type="text" class="form-control" name="numero_trem" 
                                   value="<?php echo $edit_trem ? htmlspecialchars($edit_trem['numero_trem']) : ''; ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="modelo" class="form-label">Modelo</label>
                            <input type="text" class="form-control" name="modelo" 
                                   value="<?php echo $edit_trem ? htmlspecialchars($edit_trem['modelo']) : ''; ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="linha" class="form-label">Linha</label>
                            <select class="form-select" name="linha" required>
                                <option value="">Selecione...</option>
                                <option value="Linha Azul" <?php echo ($edit_trem && $edit_trem['linha'] == 'Linha Azul') ? 'selected' : ''; ?>>Linha Azul</option>
                                <option value="Linha Verde" <?php echo ($edit_trem && $edit_trem['linha'] == 'Linha Verde') ? 'selected' : ''; ?>>Linha Verde</option>
                                <option value="Linha Vermelha" <?php echo ($edit_trem && $edit_trem['linha'] == 'Linha Vermelha') ? 'selected' : ''; ?>>Linha Vermelha</option>
                                <option value="Linha Amarela" <?php echo ($edit_trem && $edit_trem['linha'] == 'Linha Amarela') ? 'selected' : ''; ?>>Linha Amarela</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="capacidade" class="form-label">Capacidade</label>
                            <input type="number" class="form-control" name="capacidade" 
                                   value="<?php echo $edit_trem ? $edit_trem['capacidade'] : ''; ?>" 
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="status_trem" class="form-label">Status</label>
                            <select class="form-select" name="status_trem" required>
                                <option value="ativo" <?php echo ($edit_trem && $edit_trem['status_trem'] == 'ativo') ? 'selected' : ''; ?>>Ativo</option>
                                <option value="manutencao" <?php echo ($edit_trem && $edit_trem['status_trem'] == 'manutencao') ? 'selected' : ''; ?>>Em Manutenção</option>
                                <option value="inativo" <?php echo ($edit_trem && $edit_trem['status_trem'] == 'inativo') ? 'selected' : ''; ?>>Inativo</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="data_ultima_manutencao" class="form-label">Última Manutenção</label>
                            <input type="date" class="form-control" name="data_ultima_manutencao" 
                                   value="<?php echo $edit_trem ? $edit_trem['data_ultima_manutencao'] : ''; ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <?php echo $edit_trem ? 'Atualizar' : 'Adicionar'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../js/monitoramento.js"></script>
    <script src="../js/logout.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
    </script>

    <script>
        // Função para editar trem
        function editTrain(id) {
            window.location.href = '?edit_id=' + id;
        }

        // Função para confirmar exclusão
        function confirmDelete(id, nome) {
            if (confirm('Tem certeza que deseja excluir o trem "' + nome + '"?')) {
                window.location.href = '?delete_id=' + id;
            }
        }

        // Fechar modal e limpar formulário quando modal for fechado
        document.getElementById('trainModal').addEventListener('hidden.bs.modal', function () {
            if (window.location.search.includes('edit_id')) {
                window.location.href = window.location.pathname;
            }
        });

        // Abrir modal automaticamente se estiver editando
        <?php if ($edit_trem): ?>
            document.addEventListener('DOMContentLoaded', function() {
                var modal = new bootstrap.Modal(document.getElementById('trainModal'));
                modal.show();
            });
        <?php endif; ?>

        // Auto-fechar alertas após 5 segundos
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert-message');
            alerts.forEach(function(alert) {
                var bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>