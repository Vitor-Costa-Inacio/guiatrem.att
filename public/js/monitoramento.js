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
            borderWidth: 0
        }]
    },
    options: {
        cutout: '65%',
        plugins: {
            legend: {
                position: 'right',
                labels: {
                    usePointStyle: true,
                    pointStyle: 'circle',
                    color: 'black',
                    font: {
                        size: 13
                    }
                }
            }
        },
        responsive: true,
        maintainAspectRatio: true
    }
});

const alertas = [
    { mensagem: "Trem 001 - Linha Amarela apresentando desgate nas rodas.", tipo: "info" },
    { mensagem: "Trem 004 - Linha Azul apresentando luzes fracas dentro do vagão.", tipo: "warning" },
    { mensagem: "Todos os trens operando normalmente", tipo: "success" },
    { mensagem: "Trem 003 - Linha Verde manutenção solicitada", tipo: "info" }
];

const alertMessage = document.getElementById('alert-message');
const alertTime = document.getElementById('alert-time');

function mostrarAlerta() {
    if (!alertMessage || !alertTime) return;

    const alertaAleatorio = alertas[Math.floor(Math.random() * alertas.length)];
    alertMessage.textContent = alertaAleatorio.mensagem;
    alertTime.textContent = new Date().toLocaleTimeString();
}

if (alertMessage && alertTime) {
    mostrarAlerta();
    setInterval(mostrarAlerta, 10000);
}

function enviar() {
    const linha = document.querySelector('.form-select').value;
    const trem = document.querySelectorAll('.form-select')[1].value;
    const manutencao = document.querySelectorAll('.form-select')[2].value;
    const prioridade = document.querySelectorAll('.form-select')[3].value;
    
    if (linha && trem && manutencao && prioridade) {
        alert('Solicitação enviada com sucesso!');
    } else {
        alert('Por favor, preencha todos os campos.');
    }
    return false;
}