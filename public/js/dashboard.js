// Gráfico de Localização da Frota
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
            hoverOffset: 8
        }]
    },
    options: {
        cutout: '70%',
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    usePointStyle: true,
                    pointStyle: 'circle',
                    color: '#333',
                    font: {
                        size: 12,
                        family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                    },
                    padding: 15
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleFont: {
                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                },
                bodyFont: {
                    family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                }
            }
        },
        responsive: true,
        maintainAspectRatio: false
    }
});

// Dados simulados para demonstração
const alertas = [
    { mensagem: "Trem 001 - Linha Amarela apresentando desgaste nas rodas.", tipo: "info" },
    { mensagem: "Trem 004 - Linha Azul apresentando luzes fracas dentro do vagão.", tipo: "warning" },
    { mensagem: "Todos os trens operando normalmente", tipo: "success" },
    { mensagem: "Trem 003 - Linha Verde manutenção solicitada", tipo: "info" }
];

// Função para atualizar dados em tempo real (simulação)
function atualizarDadosTempoReal() {
    // Simulação de atualização de dados
    const statsCards = document.querySelectorAll('.stat-number');
    if (statsCards.length > 0) {
        // Simular pequenas variações nos números
        statsCards.forEach(card => {
            const currentValue = parseInt(card.textContent);
            const variation = Math.floor(Math.random() * 3) - 1; // -1, 0, ou 1
            const newValue = Math.max(0, currentValue + variation);
            card.textContent = newValue;
        });
    }
}

// Atualizar dados a cada 30 segundos (opcional)
// setInterval(atualizarDadosTempoReal, 30000);

console.log('Dashboard inicializado com sucesso!');