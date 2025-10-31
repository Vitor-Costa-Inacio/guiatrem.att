// JavaScript para a página de estações
class GerenciadorEstacoes {
    constructor() {
        this.mapa = null;
        this.estacoes = [];
        this.marcadores = [];
        this.modoAdicao = false;
        this.marcadorTemporario = null;
        this.estacaoEditando = null;
        this.init();
    }

    init() {
        this.inicializarMapa();
        this.configurarEventos();
        this.carregarEstacoes();
    }

    inicializarMapa() {
        // Coordenadas iniciais do Brasil
        const centroBrasil = [-14.2350, -51.9253];
        
        this.mapa = L.map('map').setView(centroBrasil, 5);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(this.mapa);

        // Evento de clique no mapa
        this.mapa.on('click', (e) => {
            if (this.modoAdicao) {
                this.adicionarMarcadorTemporario(e.latlng);
                this.preencherCoordenadas(e.latlng);
            }
        });
    }

    configurarEventos() {
        // Botão adicionar estação
        document.getElementById('btn-adicionar-estacao').addEventListener('click', () => {
            this.ativarModoAdicao();
        });

        // Botão modo edição
        document.getElementById('btn-modo-edicao').addEventListener('click', () => {
            this.toggleModoEdicao();
        });

        // Botão salvar estação
        document.getElementById('btn-salvar-estacao').addEventListener('click', () => {
            this.salvarEstacao();
        });

        // Botão cancelar
        document.getElementById('btn-cancelar').addEventListener('click', () => {
            this.cancelarOperacao();
        });

        // Confirmação do modal
        document.getElementById('btn-confirmar').addEventListener('click', () => {
            this.confirmarOperacao();
        });
    }

    ativarModoAdicao() {
        this.modoAdicao = true;
        this.mapa.getContainer().style.cursor = 'crosshair';
        this.limparFormulario();
        document.getElementById('btn-adicionar-estacao').classList.add('active');
        
        // Mostrar instrução
        this.mostrarMensagem('Clique no mapa para adicionar uma estação');
    }

    desativarModoAdicao() {
        this.modoAdicao = false;
        this.mapa.getContainer().style.cursor = '';
        document.getElementById('btn-adicionar-estacao').classList.remove('active');
        
        if (this.marcadorTemporario) {
            this.mapa.removeLayer(this.marcadorTemporario);
            this.marcadorTemporario = null;
        }
    }

    toggleModoEdicao() {
        const btn = document.getElementById('btn-modo-edicao');
        const editando = btn.classList.contains('btn-primary');
        
        if (editando) {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-secondary');
            btn.innerHTML = '<i class="fas fa-edit me-2"></i>Modo Edição';
        } else {
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-primary');
            btn.innerHTML = '<i class="fas fa-edit me-2"></i>Editando...';
        }
    }

    adicionarMarcadorTemporario(latlng) {
        if (this.marcadorTemporario) {
            this.mapa.removeLayer(this.marcadorTemporario);
        }

        this.marcadorTemporario = L.marker(latlng, {
            icon: L.divIcon({
                className: 'marcador-temporario',
                html: '<div style="background-color: #3498db; width: 18px; height: 18px; border-radius: 50%; border: 3px solid white;"></div>',
                iconSize: [24, 24]
            })
        }).addTo(this.mapa);

        return this.marcadorTemporario;
    }

    preencherCoordenadas(latlng) {
        document.getElementById('lat-estacao').value = latlng.lat.toFixed(6);
        document.getElementById('lng-estacao').value = latlng.lng.toFixed(6);
    }

    salvarEstacao() {
        const nome = document.getElementById('nome-estacao').value.trim();
        const endereco = document.getElementById('endereco-estacao').value.trim();
        const lat = document.getElementById('lat-estacao').value;
        const lng = document.getElementById('lng-estacao').value;

        if (!nome) {
            this.mostrarModal('Por favor, informe o nome da estação');
            return;
        }

        if (!lat || !lng) {
            this.mostrarModal('Por favor, selecione uma localização no mapa');
            return;
        }

        const dadosEstacao = {
            id: this.estacaoEditando ? this.estacaoEditando.id : Date.now(),
            nome: nome,
            endereco: endereco,
            lat: parseFloat(lat),
            lng: parseFloat(lng)
        };

        this.mostrarModal(`Deseja salvar a estação "${nome}"?`, () => {
            this.processarSalvamento(dadosEstacao);
        });
    }

    processarSalvamento(dadosEstacao) {
        // Simular API call
        fetch('api.php?action=save_station', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(dadosEstacao)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.adicionarEstacaoNoMapa(dadosEstacao);
                this.adicionarEstacaoNaLista(dadosEstacao);
                this.limparFormulario();
                this.desativarModoAdicao();
                this.mostrarMensagem('Estação salva com sucesso!', 'success');
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            this.mostrarMensagem('Erro ao salvar estação: ' + error.message, 'error');
        });
    }

    adicionarEstacaoNoMapa(estacao) {
        const marcador = L.marker([estacao.lat, estacao.lng], {
            icon: L.divIcon({
                className: 'marcador-estacao',
                html: '<div style="background-color: #e74c3c; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white;"></div>',
                iconSize: [26, 26]
            })
        }).addTo(this.mapa);

        marcador.bindPopup(`
            <div>
                <h6><strong>${estacao.nome}</strong></h6>
                <p class="mb-1">${estacao.endereco}</p>
                <small>Lat: ${estacao.lat}, Lng: ${estacao.lng}</small>
                <div class="mt-2">
                    <button class="btn btn-sm btn-outline-primary" onclick="gerenciadorEstacoes.editarEstacao(${estacao.id})">
                        Editar
                    </button>
                </div>
            </div>
        `);

        this.marcadores.push({
            id: estacao.id,
            marcador: marcador,
            dados: estacao
        });
    }

    adicionarEstacaoNaLista(estacao) {
        const container = document.getElementById('lista-estacoes-container');
        const estacaoItem = document.createElement('div');
        estacaoItem.className = 'estacao-item';
        estacaoItem.innerHTML = `
            <div class="estacao-nome">${estacao.nome}</div>
            <div class="estacao-coords">
                ${estacao.lat.toFixed(4)}, ${estacao.lng.toFixed(4)}
            </div>
            <div class="estacao-info">${estacao.endereco}</div>
        `;

        estacaoItem.addEventListener('click', () => {
            this.selecionarEstacao(estacao.id);
        });

        container.appendChild(estacaoItem);
    }

    selecionarEstacao(id) {
        // Remover seleção anterior
        document.querySelectorAll('.estacao-item').forEach(item => {
            item.classList.remove('active');
        });

        // Encontrar e selecionar estação
        const estacao = this.marcadores.find(m => m.id === id);
        if (estacao) {
            estacao.marcador.openPopup();
            this.mapa.setView([estacao.dados.lat, estacao.dados.lng], 12);
            
            // Adicionar classe active ao item da lista
            const items = document.querySelectorAll('.estacao-item');
            items.forEach(item => {
                if (item.querySelector('.estacao-nome').textContent === estacao.dados.nome) {
                    item.classList.add('active');
                }
            });
        }
    }

    editarEstacao(id) {
        const estacao = this.marcadores.find(m => m.id === id);
        if (estacao) {
            this.estacaoEditando = estacao.dados;
            this.preencherFormulario(estacao.dados);
            this.ativarModoAdicao();
        }
    }

    preencherFormulario(estacao) {
        document.getElementById('nome-estacao').value = estacao.nome;
        document.getElementById('endereco-estacao').value = estacao.endereco;
        document.getElementById('lat-estacao').value = estacao.lat;
        document.getElementById('lng-estacao').value = estacao.lng;
    }

    limparFormulario() {
        document.getElementById('nome-estacao').value = '';
        document.getElementById('endereco-estacao').value = '';
        document.getElementById('lat-estacao').value = '';
        document.getElementById('lng-estacao').value = '';
        this.estacaoEditando = null;
    }

    cancelarOperacao() {
        this.desativarModoAdicao();
        this.limparFormulario();
        this.mostrarMensagem('Operação cancelada');
    }

    carregarEstacoes() {
        // Carregar estações do servidor
        fetch('api.php?action=get_stations')
            .then(response => response.json())
            .then(estacoes => {
                this.estacoes = estacoes;
                estacoes.forEach(estacao => {
                    this.adicionarEstacaoNoMapa(estacao);
                    this.adicionarEstacaoNaLista(estacao);
                });
            })
            .catch(error => {
                console.error('Erro ao carregar estações:', error);
                // Carregar dados de exemplo em caso de erro
                this.carregarEstacoesExemplo();
            });
    }

    carregarEstacoesExemplo() {
        const estacoesExemplo = [
            { id: 1, nome: "Estação Central", lat: -26.3040, lng: -48.8460, endereco: "Joinville - SC" },
            { id: 2, nome: "Estação Norte", lat: -3.7304, lng: -38.5218, endereco: "Fortaleza - CE" },
            { id: 3, nome: "Estação Sul", lat: -25.4277, lng: -49.2731, endereco: "Curitiba - PR" }
        ];

        estacoesExemplo.forEach(estacao => {
            this.adicionarEstacaoNoMapa(estacao);
            this.adicionarEstacaoNaLista(estacao);
        });
    }

    mostrarModal(mensagem, callback) {
        document.getElementById('modal-mensagem').textContent = mensagem;
        const modal = new bootstrap.Modal(document.getElementById('modalConfirmacao'));
        
        // Configurar callback de confirmação
        document.getElementById('btn-confirmar').onclick = callback;
        
        modal.show();
    }

    confirmarOperacao() {
        // Fechar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmacao'));
        modal.hide();
    }

    mostrarMensagem(mensagem, tipo = 'info') {
        // Implementar sistema de notificações
        console.log(`[${tipo.toUpperCase()}] ${mensagem}`);
        
        // Poderia usar Toast ou alerta Bootstrap aqui
        if (tipo === 'success') {
            alert('✅ ' + mensagem);
        } else if (tipo === 'error') {
            alert('❌ ' + mensagem);
        } else {
            alert('ℹ️ ' + mensagem);
        }
    }
}

// Inicializar gerenciador quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    window.gerenciadorEstacoes = new GerenciadorEstacoes();
});