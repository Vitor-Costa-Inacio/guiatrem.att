
document.addEventListener('DOMContentLoaded', function() {
    const linhas = document.querySelectorAll('.linha-container');
   
    function toggleStatus(linha) {
        const indicator = linha.querySelector('.status-indicator');
        const statusText = linha.querySelector('.status-text');
       
        if (indicator.classList.contains('ativa')) {
            indicator.classList.remove('ativa');
            indicator.classList.add('inativa');
            statusText.textContent = 'Inativa';
        } else {
            indicator.classList.remove('inativa');
            indicator.classList.add('ativa');
            statusText.textContent = 'Ativa';
        }
    }
   
    linhas.forEach(linha => {
        linha.addEventListener('click', function() {
            toggleStatus(this);
        });
    });
   
    setInterval(function() {
        const randomIndex = Math.floor(Math.random() * linhas.length);
        toggleStatus(linhas[randomIndex]);
    }, 5000);


    const alertas = [
        { mensagem: "Atualização do sistema em andamento", tipo: "info" },
        { mensagem: "Problemas na linha 3 - atrasos esperados", tipo: "warning" },
        { mensagem: "Todos os sistemas operando normalmente", tipo: "success" },
        { mensagem: "Manutenção programada para amanhã às 2h", tipo: "info" }
    ];


    const alertMessage = document.getElementById('alert-message');
    const alertTime = document.getElementById('alert-time');


    function mostrarAlerta() {
        if (!alertMessage || !alertTime) return;
       
        const alertaAleatorio = alertas[Math.floor(Math.random() * alertas.length)];
       
        alertMessage.textContent = alertaAleatorio.mensagem;
        alertTime.textContent = new Date().toLocaleTimeString();
       
        const cardAlerta = document.querySelector('.alertas');
        if (cardAlerta) {
            cardAlerta.style.borderLeftColor =
                alertaAleatorio.tipo === 'warning' ? '#e74c3c' :
                alertaAleatorio.tipo === 'success' ? '#2ecc71' : '#3498db';
        }
    }


    if (alertMessage && alertTime) {
        mostrarAlerta();
        setInterval(mostrarAlerta, 10000);
    }
});