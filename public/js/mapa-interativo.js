// Mapa interativo para o dashboard
class MapaInterativo {
    constructor() {
        this.map = null;
        this.marcadores = [];
        this.modoAdicao = false;
        this.marcadorTemporario = null;
        this.init();
    }

    init() {
        // Coordenadas iniciais do Brasil
        const centroBrasil = [-14.2350, -51.9253];
        
        // Inicializar mapa
        this.map = L.map('map').setView(centroBrasil, 5);

        // Adicionar camada do mapa
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(this.map);

        // Carregar estações existentes
        this.carregarEstacoes();

        // Adicionar evento de clique no mapa
        this.map.on('click', (e) => {
            if (this.modoAdicao) {
                this.adicionarMarcadorTemporario(e.latlng);
            }
        });
    }

    carregarEstacoes() {
        // Simular carregamento de estações do servidor
        const estacoesExemplo = [
            { id: 1, nome: "Estação Central", lat: -26.3040, lng: -48.8460, endereco: "Joinville" },
            { id: 2, nome: "Estação Norte", lat: -3.7304, lng: -38.5218, endereco: "Fortaleza" },
            { id: 3, nome: "Estação Sul", lat: -25.4277, lng: -49.2731, endereco: "Curitiba" }
        ];

        estacoesExemplo.forEach(estacao => {
            this.adicionarMarcadorEstacao(estacao);
        });
    }

    adicionarMarcadorEstacao(estacao) {
        const marcador = L.marker([estacao.lat, estacao.lng], {
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
                <small>Lat: ${estacao.lat}, Lng: ${estacao.lng}</small>
            </div>
        `);

        this.marcadores.push({
            id: estacao.id,
            marcador: marcador,
            dados: estacao
        });
    }

    adicionarMarcadorTemporario(latlng) {
        // Remover marcador temporário anterior se existir
        if (this.marcadorTemporario) {
            this.map.removeLayer(this.marcadorTemporario);
        }

        this.marcadorTemporario = L.marker(latlng, {
            icon: L.divIcon({
                className: 'marcador-temporario',
                html: '<div style="background-color: #3498db; width: 18px; height: 18px; border-radius: 50%; border: 3px solid white;"></div>',
                iconSize: [24, 24]
            })
        }).addTo(this.map);

        // Atualizar coordenadas nos campos do formulário (se existirem)
        if (document.getElementById('lat-estacao') && document.getElementById('lng-estacao')) {
            document.getElementById('lat-estacao').value = latlng.lat.toFixed(6);
            document.getElementById('lng-estacao').value = latlng.lng.toFixed(6);
        }

        return this.marcadorTemporario;
    }

    ativarModoAdicao() {
        this.modoAdicao = true;
        this.map.getContainer().style.cursor = 'crosshair';
    }

    desativarModoAdicao() {
        this.modoAdicao = false;
        this.map.getContainer().style.cursor = '';
        
        if (this.marcadorTemporario) {
            this.map.removeLayer(this.marcadorTemporario);
            this.marcadorTemporario = null;
        }
    }

    salvarEstacao(dadosEstacao) {
        // Simular salvamento no servidor
        console.log('Salvando estação:', dadosEstacao);
        
        // Adicionar marcador permanente
        this.adicionarMarcadorEstacao(dadosEstacao);
        
        // Limpar marcador temporário
        this.desativarModoAdicao();
        
        return true;
    }
}

// Inicializar mapa quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    window.mapaDashboard = new MapaInterativo();
});