document.addEventListener('DOMContentLoaded', function() {
    // Elementos do DOM
    const loginButton = document.getElementById('loginButton');
    const loginInput = document.getElementById('login');
    const passwordInput = document.getElementById('password');
    const errorMessage = document.getElementById('errorMessage');
    
    // Credenciais válidas
    const validCredentials = {
        login: 'admin123',
        password: 'admin123'
    };
    
    // Função para verificar login
    function checkLogin() {
        const login = loginInput.value.trim();
        const password = passwordInput.value.trim();
        
        // Verifica se os campos estão vazios
        if (!login || !password) {
            showError('Por favor, preencha todos os campos.');
            return;
        }
        
        // Verifica as credenciais
        if (login === validCredentials.login && password === validCredentials.password) {
            // Login bem-sucedido - redireciona para a página do técnico
            window.location.href = '../html/dashboard.html';
        } else {
            showError('Login ou senha incorretos. Tente novamente.');
        }
    }
    
    // Função para mostrar mensagem de erro
    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.remove('d-none');
        
        // Esconde a mensagem após 5 segundos
        setTimeout(() => {
            errorMessage.classList.add('d-none');
        }, 5000);
    }

     // Event listeners
    loginButton.addEventListener('click', checkLogin);
    
    // Permite login ao pressionar Enter
    passwordInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            checkLogin();
        }
    });
});