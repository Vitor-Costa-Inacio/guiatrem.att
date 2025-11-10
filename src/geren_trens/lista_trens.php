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

// Buscar todos os trens
try {
    $query = "SELECT * FROM trens ORDER BY data_criacao DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $trens = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erro ao carregar trens: " . $e->getMessage();
}

// Processar exclusão
if (isset($_GET['delete_id'])) {
    try {
        $delete_id = $_GET['delete_id'];
        $query = "DELETE FROM trens WHERE id_trem = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":id", $delete_id);
        
        if ($stmt->execute()) {
            $message = "Trem excluído com sucesso!";
            $message_type = "success";
            // Recarregar a página para atualizar a lista
            echo "<script>setTimeout(() => window.location.href = 'lista_trens.php', 1000)</script>";
        }
    } catch (PDOException $e) {
        $message = "Erro ao excluir trem: " . $e->getMessage();
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guia Trem - Lista de Trens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content {
            margin-top: 80px;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: calc(100vh - 80px);
        }

        .card-custom {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .table-custom {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .status-ativo { color: #28a745; font-weight: 600; }
        .status-manutencao { color: #ffc107; font-weight: 600; }
        .status-inativo { color: #dc3545; font-weight: 600; }

        .btn-action {
            margin: 2px;
            padding: 5px 10px;
        }

        .page-title {
            color: white;
            margin-bottom: 20px;
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar bg-primary fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand text-white d-flex gap-2" href="index.php">
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

            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header bg-primary text-white gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="30" fill="currentColor"
                        class="bi bi-train-freight-front" viewBox="0 0 16 16">
                        <path
                            d="M5.065.158A1.5 1.5 0 0 1 5.736 0h4.528a1.5 1.5 0 0 1 .67.158l3.237 1.618a1.5 1.5 0 0 1 .83 1.342V13.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 13.5V3.118a1.5 1.5 0 0 1 .828-1.342zM2 9.372V13.5A1.5 1.5 0 0 0 3.5 15h4V8h-.853a.5.5 0 0 0-.144.021zM8.5 15h4a1.5 1.5 0 0 0 1.5-1.5V9.372l-4.503-1.35A.5.5 0 0 0 9.353 8H8.5zM14 8.328v-5.21a.5.5 0 0 0-.276-.447l-3.236-1.618A.5.5 0 0 0 10.264 1H5.736a.5.5 0 0 0-.223.053L2.277 2.67A.5.5 0 0 0 2 3.118v5.21l1-.3V5a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v3.028zm-2-.6V5H8.5v2h.853a1.5 1.5 0 0 1 .431.063zM7.5 7V5H4v2.728l2.216-.665A1.5 1.5 0 0 1 6.646 7zm-1-5a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm-3 8a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1m9 0a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1M5 13a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                    </svg>
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Guia Trem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>

                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-home me-2"></i>Início
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="lista_trens.php">
                                <i class="fas fa-list me-2"></i>Lista de Trens
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="monitoramento.php">
                                <i class="fas fa-tools me-2"></i>Monitoramento
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="relatorio.html">
                                <i class="fas fa-chart-bar me-2"></i>Relatórios
                            </a>
                        </li>
                        <div class="mt-auto text-center py-3">
                            <button class="btn btn-outline-danger logout-btn w-75" type="button" id="logoutBtn">
                                <i class="fas fa-sign-out-alt me-2"></i>Sair
                            </button>
                        </div>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="page-title text-center">
                        <i class="fas fa-train me-2"></i>Lista de Trens Cadastrados
                    </h1>

                    <!-- Feedback Messages -->
                    <?php if (isset($message)): ?>
                        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                            <i class="fas fa-<?php 
                                echo $message_type == 'success' ? 'check-circle' : 
                                     ($message_type == 'warning' ? 'exclamation-triangle' : 'exclamation-circle'); 
                            ?> me-2"></i>
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="card-custom">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="mb-0">
                                <i class="fas fa-table me-2"></i>Todos os Trens
                            </h2>
                            <a href="index.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Novo Trem
                            </a>
                        </div>

                        <?php if (empty($trens)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-train fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Nenhum trem cadastrado</h4>
                                <p class="text-muted">Clique em "Novo Trem" para cadastrar o primeiro trem.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-custom">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Número do Trem</th>
                                            <th>Modelo</th>
                                            <th>Linha</th>
                                            <th>Capacidade</th>
                                            <th>Status</th>
                                            <th>Última Manutenção</th>
                                            <th>Data de Criação</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($trens as $trem): ?>
                                            <tr>
                                                <td><?php echo $trem['id_trem']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($trem['numero_trem']); ?></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($trem['modelo']); ?></td>
                                                <td><?php echo htmlspecialchars($trem['linha']); ?></td>
                                                <td><?php echo $trem['capacidade']; ?> passageiros</td>
                                                <td>
                                                    <?php 
                                                        $status_class = 'status-' . $trem['status_trem'];
                                                        $status_icon = $trem['status_trem'] == 'ativo' ? 'check-circle' : 
                                                                      ($trem['status_trem'] == 'manutencao' ? 'tools' : 'times-circle');
                                                    ?>
                                                    <span class="<?php echo $status_class; ?>">
                                                        <i class="fas fa-<?php echo $status_icon; ?> me-1"></i>
                                                        <?php echo ucfirst($trem['status_trem']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo $trem['data_ultima_manutencao'] ? 
                                                        date('d/m/Y', strtotime($trem['data_ultima_manutencao'])) : 
                                                        'N/A'; ?>
                                                </td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($trem['data_criacao'])); ?></td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="editar_trem.php?id=<?php echo $trem['id_trem']; ?>" 
                                                           class="btn btn-warning btn-sm btn-action" 
                                                           title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-danger btn-sm btn-action" 
                                                                title="Excluir"
                                                                onclick="confirmarExclusao(<?php echo $trem['id_trem']; ?>, '<?php echo htmlspecialchars($trem['numero_trem']); ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3 text-muted">
                                <small>Total de <?php echo count($trens); ?> trem(ens) cadastrado(s)</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmarExclusao(id, numeroTrem) {
            if (confirm(`Tem certeza que deseja excluir o trem "${numeroTrem}"?\n\nEsta ação não pode ser desfeita.`)) {
                window.location.href = `lista_trens.php?delete_id=${id}`;
            }
        }

        // Logout
        document.getElementById('logoutBtn').addEventListener('click', function() {
            if (confirm('Tem certeza que deseja sair?')) {
                window.location.href = '../login.html';
            }
        });

        // Auto-remove alerts após 5 segundos
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>