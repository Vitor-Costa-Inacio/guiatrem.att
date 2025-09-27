/**
 * Sistema Principal da Aplicação
 */

// Configuração da API
const API_BASE_URL = '../../src/api/';
const AUTH_BASE_URL = '../../src/auth/';

// Variáveis globais
let usuarioLogado = null;

document.addEventListener('DOMContentLoaded', function() {
    // Verificar autenticação ao carregar a página
    verificarAutenticacao();
    
    // Inicializar funcionalidades específicas da página
    const currentPage = getCurrentPage();
    initPage(currentPage);
    
    // Configurar menu de navegação
    setupNavigation();
});

/**
 * Verificar se o usuário está autenticado
 */
async function verificarAutenticacao() {
    try {
        const response = await fetch(AUTH_BASE_URL + 'check_session.php');
        const data = await response.json();

        if (data.success && data.logado) {
            usuarioLogado = data.usuario;
            updateUserInfo();
        } else {
            // Redirecionar para login se não estiver autenticado
            window.location.href = 'login.html';
        }
    } catch (error) {
        console.error('Erro ao verificar autenticação:', error);
        window.location.href = 'login.html';
    }
}

/**
 * Atualizar informações do usuário na interface
 */
function updateUserInfo() {
    if (usuarioLogado) {
        // Atualizar nome do usuário no menu
        const userNameElements = document.querySelectorAll('.user-name');
        userNameElements.forEach(element => {
            element.textContent = usuarioLogado.nome;
        });

        // Atualizar e-mail do usuário
        const userEmailElements = document.querySelectorAll('.user-email');
        userEmailElements.forEach(element => {
            element.textContent = usuarioLogado.email;
        });
    }
}

/**
 * Obter página atual
 */
function getCurrentPage() {
    const path = window.location.pathname;
    const page = path.substring(path.lastIndexOf('/') + 1);
    return page.replace('.html', '');
}

/**
 * Inicializar funcionalidades específicas da página
 */
function initPage(page) {
    switch (page) {
        case 'dashboard':
            initDashboard();
            break;
        case 'monitoramento':
            initMonitoramento();
            break;
        case 'tecnico':
            initTecnico();
            break;
        case 'historico':
            initHistorico();
            break;
        case 'gestao':
            initGestao();
            break;
        case 'notificacao':
            initNotificacao();
            break;
        case 'relatorio':
            initRelatorio();
            break;
    }
}

/**
 * Configurar navegação e logout
 */
function setupNavigation() {
    // Configurar botão de logout
    const logoutButtons = document.querySelectorAll('.logout-btn, #logoutBtn');
    logoutButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            realizarLogout();
        });
    });

    // Marcar página atual no menu
    const currentPage = getCurrentPage();
    const menuItems = document.querySelectorAll('.nav-link');
    menuItems.forEach(item => {
        if (item.getAttribute('href').includes(currentPage)) {
            item.classList.add('active');
        }
    });
}

/**
 * Realizar logout
 */
async function realizarLogout() {
    try {
        const response = await fetch(AUTH_BASE_URL + 'logout.php', {
            method: 'POST'
        });

        const data = await response.json();
        
        // Redirecionar para login independente da resposta
        window.location.href = 'login.html';
    } catch (error) {
        console.error('Erro no logout:', error);
        window.location.href = 'login.html';
    }
}

/**
 * Fazer requisição autenticada para API
 */
async function apiRequest(endpoint, options = {}) {
    try {
        const response = await fetch(API_BASE_URL + endpoint, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });

        const data = await response.json();

        // Se não autorizado, redirecionar para login
        if (response.status === 401) {
            window.location.href = 'login.html';
            return null;
        }

        return data;
    } catch (error) {
        console.error('Erro na requisição:', error);
        throw error;
    }
}

/**
 * Mostrar loading
 */
function showLoading(element) {
    if (element) {
        element.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Carregando...</div>';
    }
}

/**
 * Mostrar erro
 */
function showError(element, message) {
    if (element) {
        element.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
            </div>
        `;
    }
}

/**
 * Formatar data
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
}

/**
 * Formatar data e hora
 */
function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('pt-BR');
}

/**
 * Inicializar Dashboard
 */
async function initDashboard() {
    try {
        const data = await apiRequest('dashboard.php');
        
        if (data && data.success) {
            updateDashboardStats(data.data.estatisticas_manutencao);
            updateTrensStatus(data.data.trens_por_status);
            updateManutencaoRecentes(data.data.manutencoes_recentes);
            updateAlertas(data.data.alertas);
        }
    } catch (error) {
        console.error('Erro ao carregar dashboard:', error);
    }
}

/**
 * Atualizar estatísticas do dashboard
 */
function updateDashboardStats(stats) {
    if (stats) {
        document.getElementById('totalManutencoes').textContent = stats.total || 0;
        document.getElementById('emAndamento').textContent = stats.em_andamento || 0;
        document.getElementById('concluidas').textContent = stats.concluidas || 0;
        document.getElementById('preventivas').textContent = stats.preventivas || 0;
    }
}

/**
 * Atualizar status dos trens
 */
function updateTrensStatus(trensStatus) {
    const container = document.getElementById('trensStatus');
    if (container && trensStatus) {
        let html = '';
        trensStatus.forEach(linha => {
            const porcentagem = linha.total_trens > 0 ? 
                Math.round((linha.operando / linha.total_trens) * 100) : 0;
            
            html += `
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">${linha.linha_trem}</h6>
                            <div class="progress mb-2">
                                <div class="progress-bar ${porcentagem >= 80 ? 'bg-success' : porcentagem >= 50 ? 'bg-warning' : 'bg-danger'}" 
                                     style="width: ${porcentagem}%"></div>
                            </div>
                            <small class="text-muted">
                                ${linha.operando}/${linha.total_trens} trens operando (${porcentagem}%)
                            </small>
                        </div>
                    </div>
                </div>
            `;
        });
        container.innerHTML = html;
    }
}

/**
 * Atualizar manutenções recentes
 */
function updateManutencaoRecentes(manutencoes) {
    const container = document.getElementById('manutencoesRecentes');
    if (container && manutencoes) {
        let html = '';
        manutencoes.forEach(manutencao => {
            const statusClass = manutencao.status === 'Concluída' ? 'success' : 
                               manutencao.status === 'Em andamento' ? 'warning' : 'secondary';
            
            html += `
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">${manutencao.descricao}</h6>
                        <small class="text-muted">${formatDate(manutencao.data)}</small>
                    </div>
                    <p class="mb-1">${manutencao.linha_trem} - ${manutencao.tipo}</p>
                    <small class="badge bg-${statusClass}">${manutencao.status}</small>
                </div>
            `;
        });
        container.innerHTML = html;
    }
}

/**
 * Atualizar alertas
 */
function updateAlertas(alertas) {
    const container = document.getElementById('alertas');
    if (container && alertas) {
        let html = '';
        alertas.forEach(alerta => {
            const iconClass = alerta.tipo === 'warning' ? 'fa-exclamation-triangle text-warning' : 
                             alerta.tipo === 'error' ? 'fa-times-circle text-danger' : 
                             'fa-info-circle text-info';
            
            html += `
                <div class="alert alert-light border-start border-3 border-${alerta.tipo === 'warning' ? 'warning' : alerta.tipo === 'error' ? 'danger' : 'info'}">
                    <i class="fas ${iconClass} me-2"></i>
                    ${alerta.mensagem}
                    <small class="text-muted d-block mt-1">${alerta.tempo}</small>
                </div>
            `;
        });
        container.innerHTML = html;
    }
}

/**
 * Inicializar outras páginas (placeholder)
 */
function initMonitoramento() {
    console.log('Inicializando página de monitoramento');
}

function initTecnico() {
    console.log('Inicializando página de técnico');
}

function initHistorico() {
    console.log('Inicializando página de histórico');
}

function initGestao() {
    console.log('Inicializando página de gestão');
}

function initNotificacao() {
    console.log('Inicializando página de notificação');
}

function initRelatorio() {
    console.log('Inicializando página de relatório');
}

