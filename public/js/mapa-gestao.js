// Mapa interativo para gestão de rotas
class MapaGestao {
    constructor() {
        this.map = null;
        this.estacoes = [];
        this.rotas = [];
        this.marcadores = [];
        this.linhasRotas = [];
        this.criandoRota = false;
        this.rotaAtual = [];
        this.linhaTemporaria = null;
        this.init();
    }

    init() {
        this.inicializarMapa();
        this.configurarEventos();
        this.carregarDados();
    }

    inicializarMapa() {
        // Coordenadas iniciais do Brasil
        const centroBrasil = [-14.2350, -51.9253];
        
        this.map = L.map('map').setView(centroBrasil, 5);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(this.map);

        // Evento de clique no mapa
        this.map.on('click', (e) => {
            if (this.criandoRota) {
                this.adicionarEstacaoTemporaria(e.latlng);
            }
        });
    }

    configurarEventos() {
        // Botão iniciar rota
        document.getElementById('btn-iniciar-rota').addEventListener('click', () => {
            this.iniciarCriacaoRota();
        });

        // Botão finalizar rota
        document.getElementById('btn-finalizar-rota').addEventListener('click', () => {
            this.finalizarRota();
        });

        // Botão cancelar rota
        document.getElementById('btn-cancelar-rota').addEventListener('click', () => {
            this.cancelarCriacaoRota();
        });
    }

    carregarDados() {
        this.carregarEstacoes();
        this.carregarRotas();
    }

    carregarEstacoes() {
        fetch('api.php?action=get_stations')
            .then(response => response.json())
            .then(estacoes => {
                this.estacoes = estacoes;
                this.renderizarEstacoes();
                this.atualizarListaEstacoes();
            })
            .catch(error => {
                console.error('Erro ao carregar estações:', error);
                this.carregarEstacoesExemplo();
            });
    }

    carregarRotas() {
        fetch('api.php?action=get_routes')
            .then(response => response.json())
            .then(rotas => {
                this.rotas = rotas;
                this.renderizarRotas();
            })
            .catch(error => {
                console.error('Erro ao carregar rotas:', error);
            });
    }

    carregarEstacoesExemplo() {
        const estacoesExemplo = [
            { id: 1, nome: "Estação Central", latitude: -26.3040, longitude: -48.8460, endereco: "Joinville - SC" },
            { id: 2, nome: "Estação Norte", latitude: -3.7304, longitude: -38.5218, endereco: "Fortaleza - CE" },
            { id: 3, nome: "Estação Sul", latitude: -25.4277, longitude: -49.2731, endereco: "Curitiba - PR" },
            { id: 4, nome: "Estação Leste", latitude: -19.9227, longitude: -43.9451, endereco: "Belo Horizonte - MG" },
            { id: 5, nome: "Estação Oeste", latitude: -15.7975, longitude: -47.8919, endereco: "Brasília - DF" }
        ];

        this.estacoes = estacoesExemplo;
        this.renderizarEstacoes();
        this.atualizarListaEstacoes();
    }

    renderizarEstacoes() {
        // Limpar marcadores existentes
        this.marcadores.forEach(marcador => {
            this.map.removeLayer(marcador);
        });
        this.marcadores = [];

        // Adicionar marcadores das estações
        this.estacoes.forEach(estacao => {
            const marcador = L.marker([estacao.latitude, estacao.longitude], {
                icon: L.divIcon({
                    className: 'marcador-estacao',
                    html: '<div style="background-color: #e74c3c; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white;"></div>',
                    iconSize: [26, 26]
                })
            }).addTo(this.map);

            marcador.bindPopup(`
                <div>
                    <h6><strong>${estacao.nome}</strong></h6>
                    <p class="mb-1">${estacao.endereco}</p>
                    <small>Lat: ${estacao.latitude}, Lng: ${estacao.longitude}</small>
                    ${this.criandoRota ? `
                    <div class="mt-2">
                        <button class="btn btn-sm btn-success" onclick="mapaGestao.adicionarEstacaoRota(${estacao.id})">
                            Adicionar à Rota
                        </button>
                    </div>
                    ` : ''}
                </div>
            `);

            // Evento de clique na estação
            marcador.on('click', () => {
                if (this.criandoRota) {
                    this.adicionarEstacaoRota(estacao.id);
                }
            });

            this.marcadores.push(marcador);
        });
    }

    renderizarRotas() {
        // Limpar linhas existentes
        this.linhasRotas.forEach(linha => {
            this.map.removeLayer(linha);
        });
        this.linhasRotas = [];

        // Adicionar linhas das rotas
        this.rotas.forEach(rota => {
            if (rota.estacoes && rota.estacoes.length > 1) {
                const coordenadas = rota.estacoes.map(estacao => [estacao.latitude, estacao.longitude]);
                
                const linha = L.polyline(coordenadas, {
                    color: '#e74c3c',
                    weight: 4,
                    opacity: 0.8,
                    dashArray: '10, 10'
                }).addTo(this.map);

                // Linha de sombra
                const sombra = L.polyline(coordenadas, {
                    color: '#3498db',
                    weight: 6,
                    opacity: 0.3
                }).addTo(this.map);

                linha.bindPopup(`
                    <div>
                        <h6><strong>${rota.nome}</strong></h6>
                        <p>Distância: ${rota.distancia_km} km</p>
                        <p>Tempo: ${rota.tempo_estimado_min} min</p>
                        <p>Estações: ${rota.estacoes.length}</p>
                        <button class="btn btn-sm btn-info mt-2" onclick="mapaGestao.mostrarDetalhesRota(${rota.id})">
                            Ver Detalhes
                        </button>
                    </div>
                `);

                this.linhasRotas.push(linha);
                this.linhasRotas.push(sombra);
            }
        });
    }

    atualizarListaEstacoes() {
        const container = document.getElementById('lista-estacoes');
        container.innerHTML = '';

        this.estacoes.forEach(estacao => {
            const item = document.createElement('div');
            item.className = 'estacao-item';
            item.innerHTML = `
                <div class="estacao-nome">${estacao.nome}</div>
                <div class="estacao-endereco">${estacao.endereco}</div>
            `;

            item.addEventListener('click', () => {
                this.map.setView([estacao.latitude, estacao.longitude], 12);
                // Abrir popup do marcador
                const marcador = this.marcadores.find(m => 
                    m.getLatLng().lat === estacao.latitude && 
                    m.getLatLng().lng === estacao.longitude
                );
                if (marcador) {
                    marcador.openPopup();
                }
            });

            container.appendChild(item);
        });
    }

    iniciarCriacaoRota() {
        this.criandoRota = true;
        this.rotaAtual = [];
        
        document.getElementById('btn-iniciar-rota').disabled = true;
        document.getElementById('btn-finalizar-rota').disabled = false;
        document.getElementById('btn-cancelar-rota').disabled = false;
        document.getElementById('estacoes-rota').style.display = 'block';

        this.map.getContainer().style.cursor = 'crosshair';
        
        // Atualizar popups das estações
        this.renderizarEstacoes();
        
        this.mostrarMensagem('Modo criação de rota ativado. Clique nas estações para adicioná-las à rota.');
    }

    cancelarCriacaoRota() {
        this.criandoRota = false;
        this.rotaAtual = [];
        
        document.getElementById('btn-iniciar-rota').disabled = false;
        document.getElementById('btn-finalizar-rota').disabled = true;
        document.getElementById('btn-cancelar-rota').disabled = true;
        document.getElementById('estacoes-rota').style.display = 'none';
        document.getElementById('lista-estacoes-rota').innerHTML = '';

        this.map.getContainer().style.cursor = '';
        
        // Remover linha temporária
        if (this.linhaTemporaria) {
            this.map.removeLayer(this.linhaTemporaria);
            this.linhaTemporaria = null;
        }
        
        // Atualizar popups das estações
        this.renderizarEstacoes();
        
        this.mostrarMensagem('Criação de rota cancelada.');
    }

    adicionarEstacaoRota(estacaoId) {
        const estacao = this.estacoes.find(e => e.id == estacaoId);
        if (!estacao) return;

        // Verificar se a estação já está na rota
        if (this.rotaAtual.some(e => e.id == estacaoId)) {
            this.mostrarMensagem('Esta estação já está na rota.', 'warning');
            return;
        }

        this.rotaAtual.push(estacao);
        this.atualizarListaEstacoesRota();
        this.atualizarLinhaTemporaria();
        
        this.mostrarMensagem(`Estação "${estacao.nome}" adicionada à rota.`);
    }

    adicionarEstacaoTemporaria(latlng) {
        // Criar estação temporária no mapa
        const marcadorTemporario = L.marker(latlng, {
            icon: L.divIcon({
                className: 'marcador-temporario',
                html: '<div style="background-color: #3498db; width: 18px; height: 18px; border-radius: 50%; border: 3px solid white;"></div>',
                iconSize: [24, 24]
            })
        }).addTo(this.map);

        // Adicionar estação temporária à rota
        const estacaoTemporaria = {
            id: 'temp_' + Date.now(),
            nome: 'Estação Temporária',
            latitude: latlng.lat,
            longitude: latlng.lng,
            endereco: 'Localização selecionada no mapa'
        };

        this.rotaAtual.push(estacaoTemporaria);
        this.atualizarListaEstacoesRota();
        this.atualizarLinhaTemporaria();

        marcadorTemporario.bindPopup(`
            <div>
                <h6><strong>${estacaoTemporaria.nome}</strong></h6>
                <p class="mb-1">${estacaoTemporaria.endereco}</p>
                <small>Lat: ${latlng.lat.toFixed(6)}, Lng: ${latlng.lng.toFixed(6)}</small>
            </div>
        `).openPopup();

        this.mostrarMensagem('Estação temporária adicionada à rota.');
    }

    atualizarListaEstacoesRota() {
        const container = document.getElementById('lista-estacoes-rota');
        container.innerHTML = '';

        this.rotaAtual.forEach((estacao, index) => {
            const item = document.createElement('div');
            item.className = 'estacao-item-rota';
            item.innerHTML = `
                <strong>${index + 1}.</strong> ${estacao.nome}
                ${estacao.id.toString().startsWith('temp_') ? '<small class="text-muted"> (Temporária)</small>' : ''}
            `;
            container.appendChild(item);
        });
    }

    atualizarLinhaTemporaria() {
        // Remover linha temporária anterior
        if (this.linhaTemporaria) {
            this.map.removeLayer(this.linhaTemporaria);
        }

        if (this.rotaAtual.length > 1) {
            const coordenadas = this.rotaAtual.map(estacao => [estacao.latitude, estacao.longitude]);
            this.linhaTemporaria = L.polyline(coordenadas, {
                color: '#3498db',
                weight: 4,
                opacity: 0.7,
                dashArray: '5, 5'
            }).addTo(this.map);
        }
    }

    finalizarRota() {
        const nomeRota = document.getElementById('nome-rota').value.trim() || `Rota ${this.rotas.length + 1}`;

        if (this.rotaAtual.length < 2) {
            this.mostrarMensagem('Uma rota precisa ter pelo menos duas estações.', 'error');
            return;
        }

        // Preparar dados para envio
        const dadosRota = {
            nome: nomeRota,
            estacoes: JSON.stringify(this.rotaAtual.map(estacao => estacao.id).filter(id => !id.toString().startsWith('temp_')))
        };

        // Enviar para o servidor
        fetch('api.php?action=save_route', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(dadosRota)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.mostrarMensagem(`Rota "${nomeRota}" criada com sucesso!`, 'success');
                this.cancelarCriacaoRota();
                this.carregarRotas(); // Recarregar rotas para mostrar a nova
                document.getElementById('nome-rota').value = '';
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Erro ao salvar rota:', error);
            this.mostrarMensagem('Erro ao salvar rota: ' + error.message, 'error');
        });
    }

    mostrarDetalhesRota(rotaId) {
        const rota = this.rotas.find(r => r.id == rotaId);
        if (!rota) return;

        document.getElementById('rota-nome').textContent = rota.nome;
        document.getElementById('rota-distancia').textContent = rota.distancia_km + ' km';
        document.getElementById('rota-tempo').textContent = rota.tempo_estimado_min + ' minutos';
        document.getElementById('rota-estacoes').textContent = rota.estacoes ? rota.estacoes.length : 0;

        const modal = new bootstrap.Modal(document.getElementById('modalRota'));
        modal.show();
    }

    mostrarMensagem(mensagem, tipo = 'info') {
        // Implementar sistema de notificação
        console.log(`[${tipo.toUpperCase()}] ${mensagem}`);
        
        // Exemplo simples com alert
        const cores = {
            'info': '#3498db',
            'success': '#2ecc71',
            'warning': '#f39c12',
            'error': '#e74c3c'
        };
        
        // Poderia ser implementado com Toast ou notificação customizada
        alert(mensagem);
    }
}

// Inicializar mapa quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    window.mapaGestao = new MapaGestao();
});