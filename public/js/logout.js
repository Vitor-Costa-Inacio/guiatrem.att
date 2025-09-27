document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.getElementById('logoutBtn');
    
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Confirmar logout
            if (confirm('Tem certeza que deseja sair do sistema?')) {
                performLogout();
            }
        });
    }
    
    function performLogout() {
        // Mostrar loading no botão
        const originalText = logoutBtn.innerHTML;
        logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saindo...';
        logoutBtn.disabled = true;
        
        // Limpar dados locais primeiro
        clearLocalData();
        
        // Fazer requisição para o servidor
        fetch('../../src/auth/logout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Cache-Control': 'no-cache'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro na resposta do servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Garantir limpeza completa antes do redirecionamento
                setTimeout(() => {
                    window.location.replace('../html/login.html');
                }, 500);
            } else {
                throw new Error(data.message || 'Erro desconhecido');
            }
        })
        .catch(error => {
            console.error('Erro no logout:', error);
            
            // Mesmo com erro, limpar dados locais e redirecionar
            clearLocalData();
            alert('Sessão encerrada. Você será redirecionado para o login.');
            window.location.replace('../html/login.html');
        })
        .finally(() => {
            // Restaurar botão
            logoutBtn.innerHTML = originalText;
            logoutBtn.disabled = false;
        });
    }
    
    function clearLocalData() {
        try {
            // Limpar localStorage
            localStorage.clear();
            
            // Limpar sessionStorage
            sessionStorage.clear();
            
            // Limpar cookies relacionados ao usuário
            clearUserCookies();
            
            // Limpar cache do navegador (se possível)
            if ('caches' in window) {
                caches.keys().then(names => {
                    names.forEach(name => {
                        caches.delete(name);
                    });
                });
            }
            
        } catch (error) {
            console.warn('Erro ao limpar dados locais:', error);
        }
    }
    
    function clearUserCookies() {
        // Lista de cookies comuns que podem conter dados do usuário
        const cookiesToClear = [
            'PHPSESSID',
            'user_token',
            'auth_token',
            'remember_token',
            'user_id',
            'session_id'
        ];
        
        cookiesToClear.forEach(cookieName => {
            // Limpar cookie no domínio atual
            document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
            
            // Limpar cookie no subdomínio
            document.cookie = `${cookieName}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=${window.location.hostname};`;
        });
    }
    
    // Detectar quando o usuário fecha a aba/navegador
    window.addEventListener('beforeunload', function() {
        // Enviar beacon para logout se a sessão ainda estiver ativa
        if (navigator.sendBeacon && localStorage.getItem('user_logged_in')) {
            navigator.sendBeacon('../../src/auth/logout.php', JSON.stringify({
                type: 'beforeunload'
            }));
        }
    });
});

