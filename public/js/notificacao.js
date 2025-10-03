class NotificationManager {
    constructor() {
        this.apiUrl = '../../src/api/notificacao.php';
        this.notifications = [];
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadNotifications();
        this.updateNotificationBadge();
        
        // Atualizar notificações a cada 30 segundos
        setInterval(() => {
            this.updateNotificationBadge();
        }, 30000);
    }

    bindEvents() {
        // Botão de atualizar
        document.getElementById('refreshBtn').addEventListener('click', () => {
            this.loadNotifications();
        });

        // Botão de marcar todas como lidas
        document.getElementById('markAllReadBtn').addEventListener('click', () => {
            this.markAllAsRead();
        });

        // Botão de notificação na navbar
        document.getElementById('notificationBtn').addEventListener('click', () => {
            this.updateNotificationBadge();
        });
    }

    async loadNotifications() {
        try {
            this.showLoading(true);
            
            const response = await fetch(this.apiUrl, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.notifications = data.data || [];
                this.renderNotifications();
            } else {
                this.showError('Erro ao carregar notificações: ' + data.message);
            }
        } catch (error) {
            console.error('Erro ao carregar notificações:', error);
            this.showError('Erro de conexão ao carregar notificações.');
        } finally {
            this.showLoading(false);
        }
    }

    async updateNotificationBadge() {
        try {
            const response = await fetch(this.apiUrl + '?action=unread_count', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();
            
            if (data.success) {
                const count = data.data.count;
                const badge = document.getElementById('notificationBadge');
                
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        } catch (error) {
            console.error('Erro ao atualizar badge de notificações:', error);
        }
    }

    renderNotifications() {
        const container = document.getElementById('notificationsContainer');
        const noNotificationsMessage = document.getElementById('noNotificationsMessage');

        if (this.notifications.length === 0) {
            container.innerHTML = '';
            noNotificationsMessage.style.display = 'block';
            return;
        }

        noNotificationsMessage.style.display = 'none';
        
        const notificationsHtml = this.notifications.map(notification => 
            this.createNotificationHtml(notification)
        ).join('');

        container.innerHTML = notificationsHtml;
        
        // Adicionar event listeners para as ações
        this.bindNotificationActions();
    }

    createNotificationHtml(notification) {
        const isRead = notification.read_status == 1;
        const cssClass = isRead ? 'notification-read' : 'notification-unread';
        const createdAt = new Date(notification.created_at);
        const timeString = this.formatTime(createdAt);
        const typeClass = `type-${notification.type}`;

        return `
            <div class="${cssClass} notification-enter" data-id="${notification.id}">
                <div class="header">
                    <span>
                        Notificação do Sistema
                        <span class="notification-type ${typeClass}">${notification.type}</span>
                    </span>
                    <span class="time">${timeString}</span>
                </div>
                <div class="message">
                    <p>${this.escapeHtml(notification.message)}</p>
                </div>
                <div class="notification-actions">
                    ${!isRead ? `
                        <button class="btn-mark-read" data-id="${notification.id}">
                            Marcar como lida
                        </button>
                    ` : ''}
                    <button class="btn-delete" data-id="${notification.id}">
                        Excluir
                    </button>
                </div>
            </div>
        `;
    }

    bindNotificationActions() {
        // Marcar como lida
        document.querySelectorAll('.btn-mark-read').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const notificationId = parseInt(btn.dataset.id);
                this.markAsRead(notificationId);
            });
        });

        // Excluir notificação
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const notificationId = parseInt(btn.dataset.id);
                this.deleteNotification(notificationId);
            });
        });

        // Clique na notificação para marcar como lida
        document.querySelectorAll('.notification-unread').forEach(notification => {
            notification.addEventListener('click', () => {
                const notificationId = parseInt(notification.dataset.id);
                this.markAsRead(notificationId);
            });
        });
    }

    async markAsRead(notificationId) {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'mark_read',
                    notification_id: notificationId
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // Atualizar a notificação localmente
                const notification = this.notifications.find(n => n.id == notificationId);
                if (notification) {
                    notification.read_status = 1;
                }
                
                this.renderNotifications();
                this.updateNotificationBadge();
                this.showSuccess('Notificação marcada como lida.');
            } else {
                this.showError('Erro ao marcar notificação como lida: ' + data.message);
            }
        } catch (error) {
            console.error('Erro ao marcar notificação como lida:', error);
            this.showError('Erro de conexão.');
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'mark_all_read'
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // Atualizar todas as notificações localmente
                this.notifications.forEach(notification => {
                    notification.read_status = 1;
                });
                
                this.renderNotifications();
                this.updateNotificationBadge();
                this.showSuccess('Todas as notificações foram marcadas como lidas.');
            } else {
                this.showError('Erro ao marcar todas as notificações como lidas: ' + data.message);
            }
        } catch (error) {
            console.error('Erro ao marcar todas as notificações como lidas:', error);
            this.showError('Erro de conexão.');
        }
    }

    async deleteNotification(notificationId) {
        if (!confirm('Tem certeza que deseja excluir esta notificação?')) {
            return;
        }

        try {
            const response = await fetch(this.apiUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    notification_id: notificationId
                })
            });

            const data = await response.json();
            
            if (data.success) {
                // Remover a notificação localmente
                this.notifications = this.notifications.filter(n => n.id != notificationId);
                
                // Adicionar animação de remoção
                const notificationElement = document.querySelector(`[data-id="${notificationId}"]`);
                if (notificationElement) {
                    notificationElement.classList.add('notification-removing');
                    setTimeout(() => {
                        this.renderNotifications();
                        this.updateNotificationBadge();
                    }, 300);
                }
                
                this.showSuccess('Notificação excluída com sucesso.');
            } else {
                this.showError('Erro ao excluir notificação: ' + data.message);
            }
        } catch (error) {
            console.error('Erro ao excluir notificação:', error);
            this.showError('Erro de conexão.');
        }
    }

    formatTime(date) {
        const now = new Date();
        const diffInMinutes = Math.floor((now - date) / (1000 * 60));
        
        if (diffInMinutes < 1) {
            return 'Agora mesmo';
        } else if (diffInMinutes < 60) {
            return `${diffInMinutes} min atrás`;
        } else if (diffInMinutes < 1440) { // 24 horas
            const hours = Math.floor(diffInMinutes / 60);
            return `${hours}h atrás`;
        } else {
            const days = Math.floor(diffInMinutes / 1440);
            return `${days} dia${days > 1 ? 's' : ''} atrás`;
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    showLoading(show) {
        const loadingIndicator = document.getElementById('loadingIndicator');
        const container = document.getElementById('notificationsContainer');
        
        if (show) {
            loadingIndicator.style.display = 'block';
            container.style.display = 'none';
        } else {
            loadingIndicator.style.display = 'none';
            container.style.display = 'block';
        }
    }

    showSuccess(message) {
        this.showToast(message, 'success');
    }

    showError(message) {
        this.showToast(message, 'error');
    }

    showToast(message, type) {
        // Criar toast simples
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // Remover após 3 segundos
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 3000);
    }

    // Método público para criar notificações (pode ser usado por outros módulos)
    async createNotification(message, type = 'info') {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'create',
                    message: message,
                    type: type
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.loadNotifications();
                this.updateNotificationBadge();
                return true;
            } else {
                console.error('Erro ao criar notificação:', data.message);
                return false;
            }
        } catch (error) {
            console.error('Erro ao criar notificação:', error);
            return false;
        }
    }
}

// Inicializar o gerenciador de notificações quando a página carregar
document.addEventListener('DOMContentLoaded', () => {
    window.notificationManager = new NotificationManager();
});

// Função global para criar notificações (pode ser usada por outros scripts)
window.createNotification = (message, type = 'info') => {
    if (window.notificationManager) {
        return window.notificationManager.createNotification(message, type);
    }
    return false;
};