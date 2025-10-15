// Gráfico de Localização da Frota - Versão Melhorada
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('graficoRosca').getContext('2d');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Realizando viagem', 'Estação', 'Pátio', 'Oficina'],
            datasets: [{
                data: [80, 10, 4, 6],
                backgroundColor: [
                    '#42B6E7',
                    '#001872',
                    '#FFB54A',
                    '#FF585F'
                ],
                borderWidth: 0,
                hoverOffset: 15,
                borderRadius: 8
            }]
        },
        options: {
            cutout: '70%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        font: {
                            size: 12,
                            family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
                            weight: '500'
                        },
                        color: '#333'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 13,
                        family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                    },
                    bodyFont: {
                        size: 13,
                        family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                    },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.parsed}%`;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 2000,
                easing: 'easeOutQuart'
            }
        }
    });

    // Sistema de alertas interativo
    const alertas = [
        { 
            mensagem: "Trem 001 - Linha Amarela apresentando desgaste nas rodas.", 
            tipo: "warning",
            tempo: "Há 20 min",
            prioridade: "alta"
        },
        { 
            mensagem: "Trem 004 - Linha Azul apresentando luzes fracas dentro do vagão.", 
            tipo: "info",
            tempo: "Há 1 hora",
            prioridade: "media"
        },
        { 
            mensagem: "Todos os trens operando normalmente", 
            tipo: "success",
            tempo: "Há 2 horas",
            prioridade: "baixa"
        },
        { 
            mensagem: "Trem 003 - Linha Verde - Manutenção preventiva agendada", 
            tipo: "info",
            tempo: "Há 3 horas",
            prioridade: "baixa"
        }
    ];

    // Adicionar interatividade aos cards
    const cards = document.querySelectorAll('.card-custom');
    cards.forEach(card => {
        card.addEventListener('click', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
            setTimeout(() => {
                this.style.transform = 'translateY(-5px)';
            }, 150);
        });
    });

    // Atualizar contador de alertas em tempo real
    function atualizarContadorAlertas() {
        const alertasPrioritarios = alertas.filter(alerta => 
            alerta.prioridade === 'alta' || alerta.prioridade === 'media'
        ).length;
        
        const alertNumber = document.querySelector('.alert-number h4');
        if (alertNumber) {
            alertNumber.textContent = alertasPrioritarios;
            
            // Animação no contador
            alertNumber.style.transform = 'scale(1.2)';
            setTimeout(() => {
                alertNumber.style.transform = 'scale(1)';
            }, 300);
        }
    }

    // Simular atualização de alertas a cada 30 segundos
    setInterval(atualizarContadorAlertas, 30000);

    // Efeito de digitação para o título do dashboard
    const tituloDashboard = document.querySelector('.navbar-brand');
    if (tituloDashboard) {
        const textoOriginal = tituloDashboard.textContent;
        tituloDashboard.textContent = '';
        let i = 0;
        
        function typeWriter() {
            if (i < textoOriginal.length) {
                tituloDashboard.textContent += textoOriginal.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            }
        }
        
        // Iniciar efeito após um breve delay
        setTimeout(typeWriter, 1000);
    }

    // Adicionar badges de status nas atividades
    const activityItems = document.querySelectorAll('.activity-item');
    activityItems.forEach((item, index) => {
        const tempo = item.querySelector('.activity-text p').textContent.split(': ')[1];
        const badge = document.createElement('span');
        badge.className = 'badge bg-primary rounded-pill ms-2';
        badge.textContent = tempo;
        badge.style.fontSize = '0.7rem';
        badge.style.padding = '0.25rem 0.5rem';
        
        item.querySelector('.activity-text p').appendChild(badge);
    });

    console.log('Dashboard inicializado com sucesso!');
});

// Função para carregar dados em tempo real (simulação)
function carregarDadosTempoReal() {
    // Simular atualização de dados a cada 10 segundos
    setInterval(() => {
        const atividades = document.querySelectorAll('.activity-item');
        if (atividades.length > 0) {
            // Rotacionar a primeira atividade para o final (simulação de novas atividades)
            const primeiraAtividade = atividades[0];
            const container = primeiraAtividade.parentNode;
            container.removeChild(primeiraAtividade);
            container.appendChild(primeiraAtividade);
            
            // Adicionar efeito visual
            primeiraAtividade.style.opacity = '0';
            primeiraAtividade.style.transform = 'translateX(-20px)';
            
            setTimeout(() => {
                primeiraAtividade.style.opacity = '1';
                primeiraAtividade.style.transform = 'translateX(0)';
            }, 300);
        }
    }, 10000);
}

// Iniciar carregamento de dados em tempo real quando a página carregar
window.addEventListener('load', carregarDadosTempoReal);