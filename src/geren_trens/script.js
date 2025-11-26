// Modal de edição
const editModal = document.getElementById('editModal');
const closeBtn = document.querySelector('.close');

function openEditModal(trem) {
    document.getElementById('edit_id_trem').value = trem.id_trem;
    document.getElementById('edit_linha').value = trem.linha;
    document.getElementById('edit_numero_trem').value = trem.numero_trem;
    document.getElementById('edit_modelo').value = trem.modelo;
    document.getElementById('edit_capacidade').value = trem.capacidade;
    document.getElementById('edit_status_trem').value = trem.status_trem;
    
    // Formatar data para o input date (YYYY-MM-DD)
    if(trem.data_ultima_manutencao) {
        const data = new Date(trem.data_ultima_manutencao);
        const formattedDate = data.toISOString().split('T')[0];
        document.getElementById('edit_data_ultima_manutencao').value = formattedDate;
    } else {
        document.getElementById('edit_data_ultima_manutencao').value = '';
    }
    
    editModal.style.display = 'block';
}

// Fechar modal ao clicar no X
closeBtn.onclick = function() {
    editModal.style.display = 'none';
}

// Fechar modal ao clicar fora dele
window.onclick = function(event) {
    if (event.target == editModal) {
        editModal.style.display = 'none';
    }
}

// Confirmar exclusão
function confirmDelete(id) {
    if (confirm('Tem certeza que deseja excluir este trem?\nEsta ação não pode ser desfeita.')) {
        window.location.href = 'includes/delete.php?id=' + id;
    }
}

// Validação do formulário de criação
document.getElementById('createForm').addEventListener('submit', function(e) {
    const linha = document.getElementById('linha').value.trim();
    const numero_trem = document.getElementById('numero_trem').value.trim();
    const modelo = document.getElementById('modelo').value.trim();
    const capacidade = document.getElementById('capacidade').value;
    
    if (!linha || !numero_trem || !modelo || !capacidade) {
        e.preventDefault();
        showAlert('Por favor, preencha todos os campos obrigatórios.', 'error');
        return;
    }
    
    if (capacidade < 1) {
        e.preventDefault();
        showAlert('A capacidade deve ser maior que zero.', 'error');
        return;
    }
});

// Validação do formulário de edição
document.getElementById('editForm').addEventListener('submit', function(e) {
    const linha = document.getElementById('edit_linha').value.trim();
    const numero_trem = document.getElementById('edit_numero_trem').value.trim();
    const modelo = document.getElementById('edit_modelo').value.trim();
    const capacidade = document.getElementById('edit_capacidade').value;
    
    if (!linha || !numero_trem || !modelo || !capacidade) {
        e.preventDefault();
        showAlert('Por favor, preencha todos os campos obrigatórios.', 'error');
        return;
    }
    
    if (capacidade < 1) {
        e.preventDefault();
        showAlert('A capacidade deve ser maior que zero.', 'error');
        return;
    }
});

// Função para mostrar alertas temporários
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `message ${type}`;
    alertDiv.textContent = message;
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '1001';
    alertDiv.style.maxWidth = '300px';
    
    document.body.appendChild(alertDiv);
    
    setTimeout(function() {
        alertDiv.style.opacity = '0';
        alertDiv.style.transition = 'opacity 0.5s';
        setTimeout(function() {
            document.body.removeChild(alertDiv);
        }, 500);
    }, 3000);
}

// Mostrar mensagens de confirmação automaticamente
document.addEventListener('DOMContentLoaded', function() {
    const message = document.querySelector('.message');
    if (message) {
        setTimeout(function() {
            message.style.opacity = '0';
            message.style.transition = 'opacity 0.5s';
            setTimeout(function() {
                message.style.display = 'none';
            }, 500);
        }, 5000);
    }
    
    // Adicionar máscara para número do trem
    const numeroTremInput = document.getElementById('numero_trem');
    if (numeroTremInput) {
        numeroTremInput.addEventListener('input', function(e) {
            this.value = this.value.toUpperCase();
        });
    }
});

// Animações para os cards
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.crud-section');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 200);
    });
});