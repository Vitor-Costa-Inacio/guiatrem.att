<?php
// Incluir configurações
$config_path = dirname(dirname(__FILE__)) . '/../config/config.php';
$database_path = dirname(dirname(__FILE__)) . '/../config/database.php';

if (!file_exists($config_path) || !file_exists($database_path)) {
    die("Arquivos de configuração não encontrados. Verifique a instalação.");
}

include_once $config_path;
include_once $database_path;

try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Verificar se há mensagens de sucesso/erro
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../public/css/style.css">
    <style>
        /* Estilos básicos para funcionamento */
        .message { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .btn { padding: 8px 15px; margin: 5px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-edit { background: #ffc107; color: black; }
        .btn-delete { background: #dc3545; color: white; }
        .btn-primary { background: #007bff; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚆 Sistema de Gerenciamento de Trens</h1>
        
        <?php if($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="crud-section">
            <h2>Cadastrar Novo Trem</h2>
            <form action="create.php" method="POST" id="createForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="linha">Linha:</label>
                        <input type="text" id="linha" name="linha" required placeholder="Ex: Linha 1 - Verde">
                    </div>
                    <div class="form-group">
                        <label for="numero_trem">Número do Trem:</label>
                        <input type="text" id="numero_trem" name="numero_trem" required placeholder="Ex: T-001">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="modelo">Modelo:</label>
                        <input type="text" id="modelo" name="modelo" required placeholder="Ex: Modelo MX5000">
                    </div>
                    <div class="form-group">
                        <label for="capacidade">Capacidade:</label>
                        <input type="number" id="capacidade" name="capacidade" required min="1" placeholder="Ex: 1500">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="status_trem">Status:</label>
                        <select id="status_trem" name="status_trem" required>
                            <option value="ativo">Ativo</option>
                            <option value="manutencao">Manutenção</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="data_ultima_manutencao">Data da Última Manutenção:</label>
                        <input type="date" id="data_ultima_manutencao" name="data_ultima_manutencao">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Cadastrar Trem</button>
            </form>
        </div>

        <div class="crud-section">
            <h2>Frota de Trens</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Linha</th>
                            <th>Número</th>
                            <th>Modelo</th>
                            <th>Capacidade</th>
                            <th>Status</th>
                            <th>Última Manutenção</th>
                            <th>Data Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $query = "SELECT * FROM trens ORDER BY id_trem DESC";
                            $stmt = $db->prepare($query);
                            $stmt->execute();
                            
                            if($stmt->rowCount() > 0) {
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $status_class = '';
                                    switch($row['status_trem']) {
                                        case 'ativo': $status_class = 'status-ativo'; break;
                                        case 'manutencao': $status_class = 'status-manutencao'; break;
                                        case 'inativo': $status_class = 'status-inativo'; break;
                                    }
                                    
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['id_trem']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['linha']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['numero_trem']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['modelo']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['capacidade']) . " passageiros</td>";
                                    echo "<td><span class='status-badge " . $status_class . "'>" . ucfirst(htmlspecialchars($row['status_trem'])) . "</span></td>";
                                    echo "<td>" . ($row['data_ultima_manutencao'] ? date('d/m/Y', strtotime($row['data_ultima_manutencao'])) : 'N/A') . "</td>";
                                    echo "<td>" . date('d/m/Y H:i', strtotime($row['data_criacao'])) . "</td>";
                                    echo "<td class='actions'>";
                                    echo "<button class='btn btn-edit' onclick='openEditModal(" . json_encode($row) . ")'>Editar</button>";
                                    echo "<button class='btn btn-delete' onclick='confirmDelete(" . $row['id_trem'] . ")'>Excluir</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>Nenhum trem cadastrado</td></tr>";
                            }
                        } catch (Exception $e) {
                            echo "<tr><td colspan='9'>Erro ao carregar dados: " . $e->getMessage() . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para Edição -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Editar Trem</h2>
            <form id="editForm" action="update.php" method="POST">
                <input type="hidden" id="edit_id_trem" name="id_trem">
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_linha">Linha:</label>
                        <input type="text" id="edit_linha" name="linha" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_numero_trem">Número do Trem:</label>
                        <input type="text" id="edit_numero_trem" name="numero_trem" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_modelo">Modelo:</label>
                        <input type="text" id="edit_modelo" name="modelo" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_capacidade">Capacidade:</label>
                        <input type="number" id="edit_capacidade" name="capacidade" required min="1">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_status_trem">Status:</label>
                        <select id="edit_status_trem" name="status_trem" required>
                            <option value="ativo">Ativo</option>
                            <option value="manutencao">Manutenção</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_data_ultima_manutencao">Data da Última Manutenção:</label>
                        <input type="date" id="edit_data_ultima_manutencao" name="data_ultima_manutencao">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Atualizar Trem</button>
            </form>
        </div>
    </div>

    <script src="../public/js/script.js"></script>
    <script>
        // JavaScript básico para funcionamento
        const editModal = document.getElementById('editModal');
        const closeBtn = document.querySelector('.close');

        function openEditModal(trem) {
            document.getElementById('edit_id_trem').value = trem.id_trem;
            document.getElementById('edit_linha').value = trem.linha;
            document.getElementById('edit_numero_trem').value = trem.numero_trem;
            document.getElementById('edit_modelo').value = trem.modelo;
            document.getElementById('edit_capacidade').value = trem.capacidade;
            document.getElementById('edit_status_trem').value = trem.status_trem;
            
            if(trem.data_ultima_manutencao) {
                const data = new Date(trem.data_ultima_manutencao);
                const formattedDate = data.toISOString().split('T')[0];
                document.getElementById('edit_data_ultima_manutencao').value = formattedDate;
            } else {
                document.getElementById('edit_data_ultima_manutencao').value = '';
            }
            
            editModal.style.display = 'block';
        }

        closeBtn.onclick = function() {
            editModal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == editModal) {
                editModal.style.display = 'none';
            }
        }

        function confirmDelete(id) {
            if (confirm('Tem certeza que deseja excluir este trem?\nEsta ação não pode ser desfeita.')) {
                window.location.href = 'delete.php?id=' + id;
            }
        }
    </script>
</body>
</html>