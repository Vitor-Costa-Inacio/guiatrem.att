<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gerenciamento Ferroviário</title>
    <link rel="stylesheet" href="./style.css">
    <style>
        
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Sistema de Gerenciamento Ferroviário</h1>
            <p class="subtitle">Central de controle e monitoramento do sistema ferroviário</p>
        </div>
    </header>
    
    <div class="container">
        <div class="grid">
            <!-- Gerenciamento de Sensores -->
            <div class="card" onclick="redirectTo('sensores')">
                <div class="card-icon">
                    <i>📊</i>
                </div>
                <div class="card-content">
                    <h3 class="card-title">Gerenciamento de Sensores</h3>
                    <p class="card-description">Monitore e configure todos os sensores do sistema ferroviário, verifique status e receba dados em tempo real.</p>
                  <!-- <form action="http://localhost:90/gerenciamento-dos-requisios/src/sistemas_sensores/models/sensor.php" method="get">-->
                    <button type="submit" class="card-link" href="/models/sensor.php">Sensor Management</button>
                </div>
            </div>
            
            <!-- Gerenciamento de Itinerários -->
            <div class="card" onclick="redirectTo('itinerarios')">
                <div class="card-icon">
                    <i>🗺️</i>
                </div>
                <div class="card-content">
                    <h3 class="card-title">Gerenciamento de Itinerários</h3>
                    <p class="card-description">Planeje e gerencie os itinerários dos trens, ajuste horários e defina rotas otimizadas.</p>
                    <a href="#" class="card-link">Acessar</a>
                </div>
            </div>
            
            <!-- Gerenciamento de Trens -->
            <div class="card" onclick="redirectTo('trens')">
                <div class="card-icon">
                    <i>🚆</i>
                </div>
                <div class="card-content">
                    <h3 class="card-title">Gerenciamento de Trens</h3>
                    <p class="card-description">Controle a frota de trens, acompanhe localização em tempo real e gerencie atribuições.</p>
                    <a href="#" class="card-link">Acessar</a>
                </div>
            </div>
            
            <!-- Gerenciamento de Rotas -->
            <div class="card" onclick="redirectTo('rotas')">
                <div class="card-icon">
                    <i>🛤️</i>
                </div>
                <div class="card-content">
                    <h3 class="card-title">Gerenciamento de Rotas</h3>
                    <p class="card-description">Defina e gerencie as rotas ferroviárias, verifique condições e programe manutenções.</p>
                    <a href="#" class="card-link">Acessar</a>
                </div>
            </div>
            
            <!-- Gerenciamento de Alertas -->
            <div class="card" onclick="redirectTo('alertas')">
                <div class="card-icon">
                    <i>⚠️</i>
                </div>
                <div class="card-content">
                    <h3 class="card-title">Gerenciamento de Alertas</h3>
                    <p class="card-description">Configure e monitore alertas do sistema, receba notificações de eventos críticos.</p>
                    <a href="#" class="card-link">Acessar</a>
                </div>
            </div>
            
            <!-- Gerenciamento de Manutenções -->
            <div class="card" onclick="redirectTo('manutencoes')">
                <div class="card-icon">
                    <i>🔧</i>
                </div>
                <div class="card-content">
                    <h3 class="card-title">Gerenciamento de Manutenções</h3>
                    <p class="card-description">Agende e acompanhe manutenções preventivas e corretivas da frota de trens.</p>
                    <a href="#" class="card-link">Acessar</a>
                </div>
            </div>
            
            <!-- Gerenciamento de Notificações -->
            <div class="card" onclick="redirectTo('notificacoes')">
                <div class="card-icon">
                    <i>🔔</i>
                </div>
                <div class="card-content">
                    <h3 class="card-title">Gerenciamento de Notificações</h3>
                    <p class="card-description">Configure e gerencie o sistema de notificações para usuários e operadores.</p>
                    <a href="#" class="card-link">Acessar</a>
                </div>
            </div>
            
            <!-- Gerenciamento de Relatórios -->
            <div class="card" onclick="redirectTo('relatorios')">
                <div class="card-icon">
                    <i>📈</i>
                </div>
                <div class="card-content">
                    <h3 class="card-title">Gerenciamento de Relatórios</h3>
                    <p class="card-description">Gere e visualize relatórios de desempenho, operações e manutenção do sistema.</p>
                    <a href="#" class="card-link">Acessar</a>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>