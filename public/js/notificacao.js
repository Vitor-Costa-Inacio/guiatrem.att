// Caminho correto da API (a partir de /public/html/)
const API_URL = "../../src/api/notificacao.php";

document.addEventListener("DOMContentLoaded", () => {
    carregarNotificacoes();
    configurarBotoes();
});

/* -----------------------------
   Carregar lista de notificações
--------------------------------*/
async function carregarNotificacoes() {
    mostrarLoading(true);

    try {
        const response = await fetch(`${API_URL}?action=listar`);
        
        if (!response.ok) {
            throw new Error("Falha ao conectar à API.");
        }

        const notificacoes = await response.json();

        renderizarNotificacoes(notificacoes);
    } catch (error) {
        console.error("Erro ao carregar notificações:", error);
        document.getElementById("notificationsContainer").innerHTML = `
            <div class="alert alert-danger">
                Erro ao conectar ao servidor de notificações.
            </div>
        `;
    }

    mostrarLoading(false);
}

function mostrarLoading(show) {
    document.getElementById("loadingIndicator").style.display = show ? "block" : "none";
}

/* -----------------------------
   Renderizar notificações
--------------------------------*/
function renderizarNotificacoes(lista) {
    const container = document.getElementById("notificationsContainer");
    const vazio = document.getElementById("noNotificationsMessage");

    container.innerHTML = "";

    if (!lista || lista.length === 0) {
        vazio.style.display = "block";
        return;
    }

    vazio.style.display = "none";

    lista.forEach(not => {
        const card = document.createElement("div");
        card.className = `card mb-3 shadow-sm ${not.lido == 0 ? "border-primary" : ""}`;

        card.innerHTML = `
            <div class="card-body">
                <h5 class="card-title">${not.titulo}</h5>
                <p class="card-text">${not.mensagem}</p>
                <p class="text-muted small">Recebida em: ${not.data}</p>

                ${
                    not.lido == 0
                        ? `<button class="btn btn-sm btn-primary" onclick="marcarComoLida(${not.id})">Marcar como lida</button>`
                        : `<span class="badge bg-success">Lida</span>`
                }
            </div>
        `;

        container.appendChild(card);
    });
}

/* -----------------------------
   Marcar notificação como lida
--------------------------------*/
async function marcarComoLida(id) {
    try {
        await fetch(`${API_URL}?action=marcar_lida&id=${id}`);
        carregarNotificacoes();
    } catch (error) {
        console.error("Erro ao marcar como lida:", error);
    }
}

/* -----------------------------
   Marcar todas como lidas
--------------------------------*/
async function marcarTodasComoLidas() {
    try {
        await fetch(`${API_URL}?action=marcar_todas`);
        carregarNotificacoes();
    } catch (error) {
        console.error("Erro:", error);
    }
}

/* -----------------------------
   Configurar botões
--------------------------------*/
function configurarBotoes() {
    document.getElementById("markAllReadBtn").addEventListener("click", marcarTodasComoLidas);
    document.getElementById("refreshBtn").addEventListener("click", carregarNotificacoes);
}
