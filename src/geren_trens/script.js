// Confirmar exclusão - já está correto
function confirmDelete(id) {
    if (confirm('Tem certeza que deseja excluir este trem?\nEsta ação não pode ser desfeita.')) {
        window.location.href = 'delete.php?id=' + id;
    }
}