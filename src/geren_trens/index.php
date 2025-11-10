<?php
// Configuração do Banco de Dados
class Database
{
    private $host = "localhost";
    private $db_name = "gerenciamento_trem";
    private $username = "root";
    private $password = "root";
    public $conn;

    public function getConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            die("Erro de conexão: " . $exception->getMessage());
        }
        return $this->conn;
    }
}

// Inicializar conexão
$database = new Database();
$db = $database->getConnection();

// Variáveis para feedback e dados do formulário
$message = '';
$message_type = '';
$form_data = [];

// Processar formulário quando enviado
if ($_POST) {
    try {
        // Coletar e sanitizar dados
        $numero_trem = $_POST['numero_trem'] ?? '';
        $modelo = $_POST['modelo'] ?? '';
        $linha = $_POST['linha'] ?? '';
        $capacidade = $_POST['capacidade'] ?? '';
        $status_trem = $_POST['status_trem'] ?? 'ativo';
        $data_ultima_manutencao = $_POST['data_ultima_manutencao'] ?? '';

        // Validações
        $errors = [];

        // Campo obrigatório: número do trem
        if (empty($numero_trem)) {
            $errors[] = "Número do trem é obrigatório";
        } elseif (strlen($numero_trem) > 20) {
            $errors[] = "Número do trem deve ter no máximo 20 caracteres";
        }

        // Campo obrigatório: modelo
        if (empty($modelo)) {
            $errors[] = "Modelo é obrigatório";
        } elseif (strlen($modelo) > 100) {
            $errors[] = "Modelo deve ter no máximo 100 caracteres";
        }

        // Campo obrigatório: linha
        if (empty($linha)) {
            $errors[] = "Linha é obrigatória";
        } elseif (strlen($linha) > 50) {
            $errors[] = "Linha deve ter no máximo 50 caracteres";
        }

        // Campo obrigatório: capacidade
        if (empty($capacidade)) {
            $errors[] = "Capacidade é obrigatória";
        } elseif (!is_numeric($capacidade) || $capacidade <= 0) {
            $errors[] = "Capacidade deve ser um número positivo";
        }

        // Validação de data (se preenchida)
        if (!empty($data_ultima_manutencao)) {
            $date_parts = explode('-', $data_ultima_manutencao);
            if (!checkdate($date_parts[1] ?? 0, $date_parts[2] ?? 0, $date_parts[0] ?? 0)) {
                $errors[] = "Data da última manutenção inválida";
            }
        }

        // Se não há erros, inserir no banco
        if (empty($errors)) {
            $query = "INSERT INTO trens (numero_trem, modelo, linha, capacidade, status_trem, data_ultima_manutencao) 
                      VALUES (:numero_trem, :modelo, :linha, :capacidade, :status_trem, :data_ultima_manutencao)";

            $stmt = $db->prepare($query);

            // Bind parameters
            $stmt->bindParam(":numero_trem", $numero_trem);
            $stmt->bindParam(":modelo", $modelo);
            $stmt->bindParam(":linha", $linha);
            $stmt->bindParam(":capacidade", $capacidade);
            $stmt->bindParam(":status_trem", $status_trem);
            $data_ultima_manutencao = !empty($data_ultima_manutencao) ? $data_ultima_manutencao : null;
            $stmt->bindParam(":data_ultima_manutencao", $data_ultima_manutencao);

            if ($stmt->execute()) {
                $message = "Trem cadastrado com sucesso!";
                $message_type = "success";

                // Limpar formulário após sucesso
                $form_data = [];
            } else {
                $message = "Erro ao cadastrar trem. Tente novamente.";
                $message_type = "danger";
                $form_data = $_POST;
            }
        } else {
            $message = implode("<br>", $errors);
            $message_type = "warning";
            $form_data = $_POST;
        }

    } catch (PDOException $exception) {
        if ($exception->getCode() == 23000) { // Violação de unique constraint
            $message = "Erro: Número do trem já existe no sistema.";
        } else {
            $message = "Erro no banco de dados: " . $exception->getMessage();
        }
        $message_type = "danger";
        $form_data = $_POST;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guia Trem - Cadastro de Trens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content {
            margin-top: 80px;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: calc(100vh - 80px);
        }

        .form-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
        }

        .form-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 25px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }

        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            color: white;
        }

        .btn-cancel {
            background: #6c757d;
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .form-control,
        .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .required-field::after {
            content: " *";
            color: #e74c3c;
        }

        .feedback-message {
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 5px solid;
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar"
                aria-labelledby="offcanvasNavbarLabel">
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
                            <a class="nav-link active" href="index.php">
                                <i class="fas fa-home me-2"></i>Início
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

                        <li class="nav-item">
                            <a class="nav-link" href="lista_trens.php">
                                <i class="fas fa-list me-2"></i>Lista de Trens
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
            <div class="row justify-content-center">
                <div class="col-12 col-lg-10 col-xl-8">
                    <div class="form-container">
                        <h1 class="form-title text-center">
                            <i class="fas fa-train me-2"></i>Cadastro de Trens
                        </h1>

                        <!-- Feedback Messages -->
                        <?php if ($message): ?>
                            <div class="feedback-message alert alert-<?php echo $message_type; ?>">
                                <i class="fas fa-<?php
                                echo $message_type == 'success' ? 'check-circle' :
                                    ($message_type == 'warning' ? 'exclamation-triangle' : 'exclamation-circle');
                                ?> me-2"></i>
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" id="tremForm">
                            <div class="row g-3">
                                <!-- Número do Trem -->
                                <div class="col-md-6">
                                    <label for="numero_trem" class="form-label required-field">Número do Trem</label>
                                    <input type="text" class="form-control" id="numero_trem" name="numero_trem"
                                        value="<?php echo htmlspecialchars($form_data['numero_trem'] ?? ''); ?>"
                                        maxlength="20" required>
                                    <div class="form-text">Identificação única do trem (máx. 20 caracteres)</div>
                                </div>

                                <!-- Modelo -->
                                <div class="col-md-6">
                                    <label for="modelo" class="form-label required-field">Modelo</label>
                                    <input type="text" class="form-control" id="modelo" name="modelo"
                                        value="<?php echo htmlspecialchars($form_data['modelo'] ?? ''); ?>"
                                        maxlength="100" required>
                                    <div class="form-text">Modelo do trem (máx. 100 caracteres)</div>
                                </div>

                                <!-- Linha -->
                                <div class="col-md-6">
                                    <label for="linha" class="form-label required-field">Linha</label>
                                    <input type="text" class="form-control" id="linha" name="linha"
                                        value="<?php echo htmlspecialchars($form_data['linha'] ?? ''); ?>"
                                        maxlength="50" required>
                                    <div class="form-text">Linha de operação (máx. 50 caracteres)</div>
                                </div>

                                <!-- Capacidade -->
                                <div class="col-md-6">
                                    <label for="capacidade" class="form-label required-field">Capacidade</label>
                                    <input type="number" class="form-control" id="capacidade" name="capacidade"
                                        value="<?php echo htmlspecialchars($form_data['capacidade'] ?? ''); ?>" min="1"
                                        max="1000" required>
                                    <div class="form-text">Número máximo de passageiros</div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <label for="status_trem" class="form-label required-field">Status</label>
                                    <select class="form-select" id="status_trem" name="status_trem" required>
                                        <option value="ativo" <?php echo ($form_data['status_trem'] ?? 'ativo') == 'ativo' ? 'selected' : ''; ?>>Ativo</option>
                                        <option value="manutencao" <?php echo ($form_data['status_trem'] ?? '') == 'manutencao' ? 'selected' : ''; ?>>Manutenção</option>
                                        <option value="inativo" <?php echo ($form_data['status_trem'] ?? '') == 'inativo' ? 'selected' : ''; ?>>Inativo</option>
                                    </select>
                                </div>

                                <!-- Data da Última Manutenção -->
                                <div class="col-md-6">
                                    <label for="data_ultima_manutencao" class="form-label">Data da Última
                                        Manutenção</label>
                                    <input type="date" class="form-control" id="data_ultima_manutencao"
                                        name="data_ultima_manutencao"
                                        value="<?php echo htmlspecialchars($form_data['data_ultima_manutencao'] ?? ''); ?>">
                                    <div class="form-text">Deixe em branco se não houver manutenção registrada</div>
                                </div>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="row mt-4">
                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-cancel" onclick="limparFormulario()">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </button>
                                    <button type="submit" class="btn btn-custom">
                                        <i class="fas fa-save me-2"></i>Salvar Trem
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Limpar formulário
        function limparFormulario() {
            if (confirm('Tem certeza que deseja limpar o formulário? Todos os dados não salvos serão perdidos.')) {
                document.getElementById('tremForm').reset();
            }
        }

        // Validação do formulário no front-end
        document.getElementById('tremForm').addEventListener('submit', function (e) {
            let isValid = true;
            const inputs = this.querySelectorAll('input[required], select[required]');

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            // Validação específica para capacidade
            const capacidade = document.getElementById('capacidade');
            if (capacidade.value && (capacidade.value < 1 || capacidade.value > 1000)) {
                isValid = false;
                capacidade.classList.add('is-invalid');
            }

            if (!isValid) {
                e.preventDefault();
                alert('Por favor, preencha todos os campos obrigatórios corretamente.');
            }
        });

        // Logout
        document.getElementById('logoutBtn').addEventListener('click', function () {
            if (confirm('Tem certeza que deseja sair?')) {
                window.location.href = '../login.html';
            }
        });

        // Auto-remove mensagens de feedback após 5 segundos
        setTimeout(() => {
            const alerts = document.querySelectorAll('.feedback-message');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>

</html>