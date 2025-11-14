document.addEventListener('DOMContentLoaded', function() {
    const linhas = document.querySelectorAll('.linha-container');
   
    function toggleStatus(linha) {
        const indicator = linha.querySelector('.status-indicator');
        const statusText = linha.querySelector('.status-text');
       
        if (indicator.classList.contains('ativa')) {
            indicator.classList.remove('ativa');
            indicator.classList.add('inativa');
            statusText.textContent = 'Inativa';
            linha.style.opacity = '0.7';
        } else {
            indicator.classList.remove('inativa');
            indicator.classList.add('ativa');
            statusText.textContent = 'Ativa';
            linha.style.opacity = '1';
        }
    }
   
    linhas.forEach(linha => {
        linha.addEventListener('click', function() {
            toggleStatus(this);
        });
    });
   
    /*Simulação de mudança automática de status (opcional)
    setInterval(function() {
        const randomIndex = Math.floor(Math.random() * linhas.length);
        toggleStatus(linhas[randomIndex]);
    }, 15000);*/

    // Sistema de alertas
    const alertas = [
        { mensagem: "Atualização do sistema em andamento", tipo: "info" },
        { mensagem: "Problemas na linha 3 - atrasos esperados", tipo: "warning" },
        { mensagem: "Todos os sistemas operando normalmente", tipo: "success" },
        { mensagem: "Manutenção programada para amanhã às 2h", tipo: "info" },
        { mensagem: "Nova estação adicionada: Estação Central", tipo: "info" },
        { mensagem: "Problema de sinalização na Linha Verde", tipo: "warning" }
    ];

    const alertMessage = document.getElementById('alert-message');
    const alertTime = document.getElementById('alert-time');

    function mostrarAlerta() {
        if (!alertMessage || !alertTime) return;
       
        const alertaAleatorio = alertas[Math.floor(Math.random() * alertas.length)];
       
        alertMessage.textContent = alertaAleatorio.mensagem;
        alertTime.textContent = new Date().toLocaleTimeString('pt-BR', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
       
        // Aplicar cor baseada no tipo de alerta
        const alertasDiv = document.querySelector('.alertas');
        if (alertasDiv) {
            // Reset de cores
            alertasDiv.style.borderLeft = 'none';
            
            if (alertaAleatorio.tipo === 'warning') {
                alertMessage.style.color = '#e74c3c';
                alertasDiv.style.borderLeft = '4px solid #e74c3c';
            } else if (alertaAleatorio.tipo === 'success') {
                alertMessage.style.color = '#2ecc71';
                alertasDiv.style.borderLeft = '4px solid #2ecc71';
            } else {
                alertMessage.style.color = '#3498db';
                alertasDiv.style.borderLeft = '4px solid #3498db';
            }
        }
    }

    if (alertMessage && alertTime) {
        mostrarAlerta();
        setInterval(mostrarAlerta, 10000);
    }

    // Controles de criação de rota
    const btnIniciarRota = document.getElementById('btn-iniciar-rota');
    const btnFinalizarRota = document.getElementById('btn-finalizar-rota');
    const btnCancelarRota = document.getElementById('btn-cancelar-rota');
    const estacoesRotaDiv = document.getElementById('estacoes-rota');

    if (btnIniciarRota && btnFinalizarRota && btnCancelarRota) {
        btnIniciarRota.addEventListener('click', function() {
            // Iniciar modo de criação de rota
            this.disabled = true;
            btnFinalizarRota.disabled = false;
            btnCancelarRota.disabled = false;
            estacoesRotaDiv.style.display = 'block';
            
            // Mudar cursor do mapa
            if (window.mapaGestao) {
                window.mapaGestao.iniciarCriacaoRota();
            }
            
            mostrarMensagem('Modo criação de rota ativado. Clique nas estações para adicioná-las à rota.');
        });

        btnFinalizarRota.addEventListener('click', function() {
            const nomeRota = document.getElementById('nome-rota').value.trim();
            
            if (!nomeRota) {
                alert('Por favor, informe um nome para a rota.');
                return;
            }
            
            // Finalizar rota
            if (window.mapaGestao) {
                window.mapaGestao.finalizarRota();
            }
            
            // Resetar controles
            btnIniciarRota.disabled = false;
            btnFinalizarRota.disabled = true;
            btnCancelarRota.disabled = true;
            estacoesRotaDiv.style.display = 'none';
            document.getElementById('nome-rota').value = '';
            
            mostrarMensagem(`Rota "${nomeRota}" criada com sucesso!`);
        });

        btnCancelarRota.addEventListener('click', function() {
            // Cancelar criação de rota
            if (window.mapaGestao) {
                window.mapaGestao.cancelarCriacaoRota();
            }
            
            // Resetar controles
            btnIniciarRota.disabled = false;
            btnFinalizarRota.disabled = true;
            btnCancelarRota.disabled = true;
            estacoesRotaDiv.style.display = 'none';
            document.getElementById('nome-rota').value = '';
            
            mostrarMensagem('Criação de rota cancelada.');
        });
    }

    function mostrarMensagem(mensagem) {
        // Implementação simples - pode ser substituída por um sistema de toast
        console.log('Mensagem do sistema:', mensagem);
    }

    // Carregar lista de estações
    carregarListaEstacoes();
});

function carregarListaEstacoes() {
    const container = document.getElementById('lista-estacoes');
    if (!container) return;

    // Simular carregamento de estações
    const estacoesExemplo = [
        { id: 1, nome: "Estação Central Joinville", endereco: "Joinville, Santa Catarina" },
        { id: 2, nome: "Estação Fortaleza", endereco: "Fortaleza, Ceará" },
        { id: 3, nome: "Estação Curitiba", endereco: "Curitiba, Paraná" },
        { id: 4, nome: "Estação Itapetininga", endereco: "Itapetininga, São Paulo" },
        { id: 5, nome: "Estação Belo Horizonte", endereco: "Belo Horizonte, Minas Gerais" },
        { id: 6, nome: "Estação São Paulo", endereco: "São Paulo, São Paulo" },
        { id: 7, nome: "Estação Rio de Janeiro", endereco: "Rio de Janeiro, Rio de Janeiro" },
        { id: 8, nome: "Estação Porto Alegre", endereco: "Porto Alegre, Rio Grande do Sul" },
        { id: 9, nome: "Estação Salvador", endereco: "Salvador, Bahia" }
    ];

    container.innerHTML = '';

    estacoesExemplo.forEach(estacao => {
        const item = document.createElement('div');
        item.className = 'estacao-item';
        item.innerHTML = `
            <div class="estacao-nome">${estacao.nome}</div>
            <div class="estacao-endereco">${estacao.endereco}</div>
        `;

        item.addEventListener('click', function() {
            // Centralizar mapa na estação (será implementado no mapa-gestao.js)
            if (window.mapaGestao) {
                window.mapaGestao.centralizarNaEstacao(estacao.id);
            }
            
            // Destacar item selecionado
            document.querySelectorAll('.estacao-item').forEach(el => {
                el.style.backgroundColor = 'white';
            });
            this.style.backgroundColor = '#e3f2fd';
        });

        container.appendChild(item);
    });
}