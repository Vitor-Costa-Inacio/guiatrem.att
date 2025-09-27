function abrir() {
    const lateral = document.getElementById("lateral");
    const rotasSection = document.getElementById("Rotas");
    
    lateral.classList.add("aberto");
    rotasSection.classList.add("menu-aberto"); // Adiciona classe ao #Rotas
}

function fechar() {
    const lateral = document.getElementById("lateral");
    const rotasSection = document.getElementById("Rotas");
    
    lateral.classList.remove("aberto");
    rotasSection.classList.remove("menu-aberto"); // Remove classe do #Rotas
}