// Gerenciador de Itinerários
class GerenciadorItinerarios {
    constructor() {
        this.mapa = null;
        this.itinerarios = [];
        this.itinerarioSelecionado = null;
        this.rotasDisponiveis = [];
        this.trensDisponiveis = [];
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
        
        this.mapa = L.map('map').setView(centroBrasil, 5);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(this.mapa);
    }

    configurarEventos() {
        // Botão novo itinerário
        document.getElementById('btn-novo-itinerario').addEventListener('click', () => {
            this.abrirModalNovoItinerario();
        });

        // Botão salvar itinerário
        document.getElementById('btn-salvar-itinerario').addEventListener('click', () => {
            this.salvarItinerario();
        });

        // Botão confirmar exclusão
        document.getElementById('btn-confirmar-exclusao').addEventListener('click', () => {
            this.confirmarExclusao();
        });
    }

    carregarDados() {
        this.carregarItinerarios();
        this.carregarRotasDisponiveis();
        this.carregarTrensDisponiveis();
    }

    carregarItinerarios() {
        // Simular chamada à API
        fetch('../api/itinerarios.php?action=get_all')
            .then(response => response.json())
            .then(data => {
                this.itinerarios = data;
                this.atualizarListaItinerarios();
            })
            .catch(error => {
                console.error('Erro ao carregar itinerários:', error);
                // Carregar dados de exemplo
                this.carregarItinerariosExemplo();
            });
    }

    carregarItinerariosExemplo() {
        this.itinerarios = [
            {
                id: 1,
                nome: "Itinerário Norte-Sul",
                descricao: "Rota principal conectando as regiões norte e sul",
                trem_id: 1,
                rotas: [1, 2, 3],
                status: "ativo",
                created_at: "2023-01-15"
            },
            {
                id: 2,
                nome: "Itinerário Litoral",
                descricao: "Rota turística ao longo do litoral",
                trem_id: 2,
                rotas: [4, 5],
                status: "ativo",
                created_at: "2023-02-20"
            },
            {
                id: 3,
                nome: "Itinerário Industrial",
                descricao: "Rota para transporte de carga industrial",
                trem_id: 3,
                rotas: [6, 7, 8],
                status: "inativo",
                created_at: "2023-03-10"
            }
        ];
        this.atualizarListaItinerarios();
    }

    carregarRotasDisponiveis() {
        // Simular chamada à API
        this.rotasDisponiveis = [
            { id: 1, nome: "Rota Norte", descricao: "Joinville - Curitiba", distancia: "150km" },
            { id: 2, nome: "Rota Centro", descricao: "Curitiba - São Paulo", distancia: "400km" },
            { id: 3, nome: "Rota Sul", descricao: "São Paulo - Porto Alegre", distancia: "850km" },
            { id: 4, nome: "Rota Litoral Norte", descricao: "Florianópolis - Balneário Camboriú", distancia: "120km" },
            { id: 5, nome: "Rota Litoral Sul", descricao: "Balneário Camboriú - São Paulo", distancia: "500km" },
            { id: 6, nome: "Rota Industrial 1", descricao: "Zona Industrial Norte", distancia: "50km" },
            { id: 7, nome: "Rota Industrial 2", descricao: "Zona Industrial Sul", distancia: "75km" },
            { id: 8, nome: "Rota Industrial 3", descricao: "Centro Logístico", distancia: "30km" }
        ];
        this.atualizarListaRotasDisponiveis();
    }

    carregarTrensDisponiveis() {
        // Simular chamada à API
        this.trensDisponiveis = [
            { id: 1, nome: "Trem Expresso 001", modelo: "Modelo A", capacidade: "200 passageiros" },
            { id: 2, nome: "Trem Turístico 002", modelo: "Modelo B", capacidade: "150 passageiros" },
            { id: 3, nome: "Trem Carga 003", modelo: "Modelo C", capacidade: "500 toneladas" },
            { id: 4, nome: "Trem Regional 004", modelo: "Modelo D", capacidade: "180 passageiros" }
        ];
        this.atualizarSelectTrens();
    }

    atualizarListaItinerarios() {
        const container = document.getElementById('lista-itinerarios');
        container.innerHTML = '';

        this.itinerarios.forEach(itinerario => {
            const item = document.createElement('div');
            item.className = `itinerario-item ${this.itinerarioSelecionado?.id === itinerario.id ? 'active' : ''}`;
            item.innerHTML = `
                <div class="itinerario-header">
                    <div class="itinerario-nome">${itinerario.nome}</div>
                    <div class="itinerario-acoes">
                        <span class="status-badge status-${itinerario.status}">${itinerario.status}</span>
                        <button class="btn btn-sm btn-outline-primary" onclick="gerenciadorItinerarios.editarItinerario(${itinerario.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="gerenciadorItinerarios.solicitarExclusao(${itinerario.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="itinerario-info">
                    ${itinerario.descricao}<br>
                    <small>Criado em: ${new Date(itinerario.created_at).toLocaleDateString('pt-BR')}</small>
                </div>
            `;

            item.addEventListener('click', () => {
                this.selecionarItinerario(itinerario.id);
            });

            container.appendChild(item);
        });
    }

    atualizarListaRotasDisponiveis() {
        const container = document.getElementById('lista-rotas-disponiveis');
        container.innerHTML = '';

        this.rotasDisponiveis.forEach(rota => {
            const item = document.createElement('div');
            item.className = 'rota-item';
            item.innerHTML = `
                <div class="form-check">
                    <input class="form-check-input rota-checkbox" type="checkbox" value="${rota.id}" id="rota-${rota.id}">
                    <label class="form-check-label" for="rota-${rota.id}">
                        <div class="rota-info">
                            <div class="rota-nome">${rota.nome}</div>
                            <div class="rota-descricao">${rota.descricao} - ${rota.distancia}</div>
                        </div>
                    </label>
                </div>
            `;
            container.appendChild(item);
        });
    }

    atualizarSelectTrens() {
        const select = document.getElementById('trem-itinerario');
        select.innerHTML = '<option value="">Selecione um trem</option>';
        
        this.trensDisponiveis.forEach(trem => {
            const option = document.createElement('option');
            option.value = trem.id;
            option.textContent = `${trem.nome} (${trem.modelo})`;
            select.appendChild(option);
        });
    }

    selecionarItinerario(id) {
        this.itinerarioSelecionado = this.itinerarios.find(i => i.id === id);
        this.atualizarListaItinerarios();
        this.mostrarDetalhesItinerario();
        this.visualizarItinerarioNoMapa();
    }

    mostrarDetalhesItinerario() {
        const container = document.getElementById('detalhes-itinerario');
        
        if (!this.itinerarioSelecionado) {
            container.innerHTML = `
                <div class="text-center text-muted py-5">
                    <i class="fas fa-route fa-3x mb-3"></i>
                    <p>Selecione um itinerário para visualizar os detalhes</p>
                </div>
            `;
            return;
        }

        const itinerario = this.itinerarioSelecionado;
        const trem = this.trensDisponiveis.find(t => t.id === itinerario.trem_id);
        const rotasSelecionadas = this.rotasDisponiveis.filter(r => 
            itinerario.rotas.includes(r.id)
        );

        container.innerHTML = `
            <div class="mb-4">
                <h4>${itinerario.nome}</h4>
                <p class="text-muted">${itinerario.descricao}</p>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Trem Designado:</strong><br>
                    ${trem ? `${trem.nome} (${trem.modelo})` : 'Não designado'}
                </div>
                <div class="col-md-6">
                    <strong>Status:</strong><br>
                    <span class="status-badge status-${itinerario.status}">${itinerario.status}</span>
                </div>
            </div>
            
            <div class="mb-3">
                <strong>Rotas Incluídas:</strong>
                <div class="mt-2">
                    ${rotasSelecionadas.map(rota => `
                        <div class="rota-item mb-2">
                            <div class="rota-nome">${rota.nome}</div>
                            <div class="rota-descricao">${rota.descricao} - ${rota.distancia}</div>
                        </div>
                    `).join('')}
                </div>
            </div>
            
            <div class="mt-4">
                <small class="text-muted">
                    Criado em: ${new Date(itinerario.created_at).toLocaleDateString('pt-BR')}
                </small>
            </div>
        `;
    }

    visualizarItinerarioNoMapa() {
        // Limpar mapa
        this.mapa.eachLayer(layer => {
            if (layer instanceof L.Polyline || layer instanceof L.Marker) {
                this.mapa.removeLayer(layer);
            }
        });

        if (!this.itinerarioSelecionado) return;

        // Simular visualização das rotas no mapa
        const itinerario = this.itinerarioSelecionado;
        const rotasSelecionadas = this.rotasDisponiveis.filter(r => 
            itinerario.rotas.includes(r.id)
        );

        // Simular coordenadas para as rotas
        rotasSelecionadas.forEach((rota, index) => {
            // Gerar coordenadas aleatórias para demonstração
            const latBase = -15 + (index * 5);
            const lngBase = -50 + (index * 3);
            
            const coordinates = [
                [latBase, lngBase],
                [latBase + 2, lngBase + 1],
                [latBase + 3, lngBase - 1],
                [latBase + 5, lngBase + 2]
            ];

            // Adicionar linha da rota
            const polyline = L.polyline(coordinates, {
                color: '#4180AB',
                weight: 4,
                opacity: 0.7
            }).addTo(this.mapa);

            // Adicionar marcadores
            coordinates.forEach((coord, i) => {
                L.marker(coord)
                    .addTo(this.mapa)
                    .bindPopup(`<strong>${rota.nome}</strong><br>Ponto ${i + 1}`);
            });
        });

        // Ajustar visualização do mapa
        if (rotasSelecionadas.length > 0) {
            const group = new L.featureGroup();
            this.mapa.eachLayer(layer => {
                if (layer instanceof L.Polyline || layer instanceof L.Marker) {
                    group.addLayer(layer);
                }
            });
            this.mapa.fitBounds(group.getBounds().pad(0.1));
        }
    }

    abrirModalNovoItinerario() {
        document.getElementById('modalItinerarioTitulo').textContent = 'Novo Itinerário';
        document.getElementById('formItinerario').reset();
        document.getElementById('itinerario-id').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('modalItinerario'));
        modal.show();
    }

    editarItinerario(id) {
        const itinerario = this.itinerarios.find(i => i.id === id);
        if (!itinerario) return;

        document.getElementById('modalItinerarioTitulo').textContent = 'Editar Itinerário';
        document.getElementById('itinerario-id').value = itinerario.id;
        document.getElementById('nome-itinerario').value = itinerario.nome;
        document.getElementById('descricao-itinerario').value = itinerario.descricao;
        document.getElementById('trem-itinerario').value = itinerario.trem_id;

        // Marcar rotas selecionadas
        setTimeout(() => {
            itinerario.rotas.forEach(rotaId => {
                const checkbox = document.getElementById(`rota-${rotaId}`);
                if (checkbox) checkbox.checked = true;
            });
        }, 100);

        const modal = new bootstrap.Modal(document.getElementById('modalItinerario'));
        modal.show();
    }

    salvarItinerario() {
        const id = document.getElementById('itinerario-id').value;
        const nome = document.getElementById('nome-itinerario').value.trim();
        const descricao = document.getElementById('descricao-itinerario').value.trim();
        const tremId = document.getElementById('trem-itinerario').value;

        if (!nome || !tremId) {
            alert('Por favor, preencha todos os campos obrigatórios.');
            return;
        }

        // Coletar rotas selecionadas
        const rotasSelecionadas = [];
        document.querySelectorAll('.rota-checkbox:checked').forEach(checkbox => {
            rotasSelecionadas.push(parseInt(checkbox.value));
        });

        if (rotasSelecionadas.length === 0) {
            alert('Selecione pelo menos uma rota para o itinerário.');
            return;
        }

        const dadosItinerario = {
            id: id ? parseInt(id) : null,
            nome: nome,
            descricao: descricao,
            trem_id: parseInt(tremId),
            rotas: rotasSelecionadas,
            status: 'ativo'
        };

        // Simular chamada à API
        const action = id ? 'update' : 'create';
        fetch(`../api/itinerarios.php?action=${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(dadosItinerario)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Fechar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalItinerario'));
                modal.hide();
                
                // Recarregar dados
                this.carregarItinerarios();
                this.mostrarMensagem('Itinerário salvo com sucesso!', 'success');
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            this.mostrarMensagem('Erro ao salvar itinerário: ' + error.message, 'error');
        });
    }

    solicitarExclusao(id) {
        this.itinerarioParaExcluir = id;
        const modal = new bootstrap.Modal(document.getElementById('modalConfirmacao'));
        modal.show();
    }

    confirmarExclusao() {
        if (!this.itinerarioParaExcluir) return;

        // Simular chamada à API
        fetch(`../api/itinerarios.php?action=delete&id=${this.itinerarioParaExcluir}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Fechar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalConfirmacao'));
                modal.hide();
                
                // Recarregar dados
                this.carregarItinerarios();
                this.mostrarMensagem('Itinerário excluído com sucesso!', 'success');
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            this.mostrarMensagem('Erro ao excluir itinerário: ' + error.message, 'error');
        });
    }

    mostrarMensagem(mensagem, tipo = 'info') {
        // Implementar sistema de notificações (pode usar toast do Bootstrap)
        console.log(`[${tipo.toUpperCase()}] ${mensagem}`);
        
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
    window.gerenciadorItinerarios = new GerenciadorItinerarios();
});