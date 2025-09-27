/**
 * Sistema de Autenticação - Frontend
 */

// Configuração da API
const API_BASE_URL = '../../src/auth/';

// Elementos do DOM
let loginForm, cadastroForm;

document.addEventListener('DOMContentLoaded', function() {
    // Verificar se estamos na página de login ou cadastro
    loginForm = document.getElementById('loginForm');
    cadastroForm = document.getElementById('cadastroForm');

    if (loginForm) {
        initLogin();
    }

    if (cadastroForm) {
        initCadastro();
    }

    // Verificar se o usuário já está logado
    verificarSessao();
});

/**
 * Inicializar funcionalidades de login
 */
function initLogin() {
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        realizarLogin();
    });

    // Permitir login ao pressionar Enter
    document.getElementById('senha').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            realizarLogin();
        }
    });
}

/**
 * Inicializar funcionalidades de cadastro
 */
function initCadastro() {
    const nomeInput = document.getElementById('nome');
    const emailInput = document.getElementById('email');
    const senhaInput = document.getElementById('senha');
    const confirmarSenhaInput = document.getElementById('confirmar_senha');
    const togglePassword = document.getElementById('togglePassword');
    const passwordStrength = document.getElementById('passwordStrength');

    // Toggle mostrar/ocultar senha
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = senhaInput.getAttribute('type') === 'password' ? 'text' : 'password';
            senhaInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }

    // Validação de e-mail em tempo real
    if (emailInput) {
        emailInput.addEventListener('input', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email === '') {
                this.classList.remove('is-valid', 'is-invalid');
            } else if (emailRegex.test(email)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
                this.nextElementSibling.textContent = 'Por favor, digite um e-mail válido.';
            }
        });
    }

    // Validação de força da senha
    if (senhaInput && passwordStrength) {
        senhaInput.addEventListener('input', function() {
            const senha = this.value;
            const strength = calculatePasswordStrength(senha);
            
            passwordStrength.className = 'password-strength';
            if (senha.length > 0) {
                if (strength < 3) {
                    passwordStrength.classList.add('strength-weak');
                    passwordStrength.style.width = '33%';
                } else if (strength < 5) {
                    passwordStrength.classList.add('strength-medium');
                    passwordStrength.style.width = '66%';
                } else {
                    passwordStrength.classList.add('strength-strong');
                    passwordStrength.style.width = '100%';
                }
            } else {
                passwordStrength.style.width = '0%';
            }
        });
    }

    // Validação de confirmação de senha
    if (confirmarSenhaInput) {
        confirmarSenhaInput.addEventListener('input', function() {
            if (this.value !== senhaInput.value) {
                this.classList.add('is-invalid');
                this.nextElementSibling.textContent = 'As senhas não coincidem.';
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                this.nextElementSibling.textContent = '';
            }
        });
    }

    // Submissão do formulário de cadastro
    cadastroForm.addEventListener('submit', function(e) {
        e.preventDefault();
        realizarCadastro();
    });
}

/**
 * Realizar login
 */
async function realizarLogin() {
    const email = document.getElementById('email').value.trim();
    const senha = document.getElementById('senha').value.trim();

    // Validar campos
    if (!email || !senha) {
        mostrarErro('Por favor, preencha todos os campos.');
        return;
    }

    try {
        mostrarCarregamento(true);

        const response = await fetch(API_BASE_URL + 'login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                email: email,
                senha: senha
            })
        });

        const data = await response.json();

        if (data.success) {
            mostrarSucesso(data.message);
            
            // Redirecionar após 1 segundo
            setTimeout(() => {
                window.location.href = data.redirect || 'dashboard.html';
            }, 1000);
        } else {
            mostrarErro(data.message);
        }

    } catch (error) {
        console.error('Erro no login:', error);
        mostrarErro('Erro de conexão. Tente novamente.');
    } finally {
        mostrarCarregamento(false);
    }
}

/**
 * Realizar cadastro
 */
async function realizarCadastro() {
    const nome = document.getElementById('nome').value.trim();
    const email = document.getElementById('email').value.trim();
    const senha = document.getElementById('senha').value.trim();
    const confirmarSenha = document.getElementById('confirmar_senha').value.trim();

    // Validações
    if (!validarCadastro(nome, email, senha, confirmarSenha)) {
        return;
    }

    try {
        mostrarCarregamento(true);

        const response = await fetch(API_BASE_URL + 'register.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                nome: nome,
                email: email,
                senha: senha,
                confirmar_senha: confirmarSenha
            })
        });

        const data = await response.json();

        if (data.success) {
            mostrarSucesso(data.message);
            
            // Limpar formulário
            cadastroForm.reset();
            document.getElementById('passwordStrength').style.width = '0%';
            
            // Redirecionar após 2 segundos
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 2000);
        } else {
            mostrarErro(data.message);
        }

    } catch (error) {
        console.error('Erro no cadastro:', error);
        mostrarErro('Erro de conexão. Tente novamente.');
    } finally {
        mostrarCarregamento(false);
    }
}

/**
 * Validar dados do cadastro
 */
function validarCadastro(nome, email, senha, confirmarSenha) {
    let isValid = true;

    // Validar nome
    if (nome.length < 2) {
        mostrarErroField(document.getElementById('nome'), 'Nome deve ter pelo menos 2 caracteres.');
        isValid = false;
    } else {
        limparErroField(document.getElementById('nome'));
    }

    // Validar e-mail
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        mostrarErroField(document.getElementById('email'), 'Por favor, digite um e-mail válido.');
        isValid = false;
    } else {
        limparErroField(document.getElementById('email'));
    }

    // Validar senha
    if (senha.length < 8) {
        mostrarErroField(document.getElementById('senha'), 'Senha deve ter pelo menos 8 caracteres.');
        isValid = false;
    } else if (!/(?=.*[a-zA-Z])(?=.*\d)/.test(senha)) {
        mostrarErroField(document.getElementById('senha'), 'Senha deve conter pelo menos uma letra e um número.');
        isValid = false;
    } else {
        limparErroField(document.getElementById('senha'));
    }

    // Validar confirmação de senha
    if (confirmarSenha !== senha) {
        mostrarErroField(document.getElementById('confirmar_senha'), 'As senhas não coincidem.');
        isValid = false;
    } else {
        limparErroField(document.getElementById('confirmar_senha'));
    }

    return isValid;
}

/**
 * Verificar se o usuário está logado
 */
async function verificarSessao() {
    try {
        const response = await fetch(API_BASE_URL + 'check_session.php');
        const data = await response.json();

        if (data.success && data.logado) {
            // Se estiver na página de login ou cadastro, redirecionar para dashboard
            const currentPage = window.location.pathname;
            if (currentPage.includes('login.html') || currentPage.includes('cadastro.html') || currentPage.includes('index.html')) {
                window.location.href = 'dashboard.html';
            }
        }
    } catch (error) {
        console.error('Erro ao verificar sessão:', error);
    }
}

/**
 * Realizar logout
 */
async function logout() {
    try {
        const response = await fetch(API_BASE_URL + 'logout.php', {
            method: 'POST'
        });

        const data = await response.json();

        if (data.success) {
            window.location.href = 'login.html';
        }
    } catch (error) {
        console.error('Erro no logout:', error);
        // Mesmo com erro, redirecionar para login
        window.location.href = 'login.html';
    }
}

/**
 * Calcular força da senha
 */
function calculatePasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    return strength;
}

/**
 * Mostrar erro em campo específico
 */
function mostrarErroField(field, message) {
    field.classList.add('is-invalid');
    const feedback = field.parentNode.nextElementSibling;
    if (feedback && feedback.classList.contains('invalid-feedback')) {
        feedback.textContent = message;
    }
}

/**
 * Limpar erro de campo específico
 */
function limparErroField(field) {
    field.classList.remove('is-invalid');
    const feedback = field.parentNode.nextElementSibling;
    if (feedback && feedback.classList.contains('invalid-feedback')) {
        feedback.textContent = '';
    }
}

/**
 * Mostrar mensagem de erro
 */
function mostrarErro(message) {
    const errorDiv = document.getElementById('errorMessage') || document.getElementById('mensagem');
    if (errorDiv) {
        errorDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
            </div>
        `;
        errorDiv.classList.remove('d-none');
    }
}

/**
 * Mostrar mensagem de sucesso
 */
function mostrarSucesso(message) {
    const successDiv = document.getElementById('successMessage') || document.getElementById('mensagem');
    if (successDiv) {
        successDiv.innerHTML = `
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                ${message}
            </div>
        `;
        successDiv.classList.remove('d-none');
    }
}

/**
 * Mostrar/ocultar indicador de carregamento
 */
function mostrarCarregamento(show) {
    const submitBtn = document.querySelector('button[type="submit"]');
    if (submitBtn) {
        if (show) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processando...';
        } else {
            submitBtn.disabled = false;
            if (loginForm) {
                submitBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>CONECTAR';
            } else if (cadastroForm) {
                submitBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i>CADASTRAR';
            }
        }
    }
}

