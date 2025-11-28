<?php
// Incluir configurações - caminhos ajustados para a estrutura
$config_path = __DIR__ . '/../../config/config.php';
$database_path = __DIR__ . '/../../config/database.php';

if (!file_exists($config_path) || !file_exists($database_path)) {
    die("Arquivos de configuração não encontrados. Verifique a instalação.");
}

include_once $config_path;
include_once $database_path;

// Iniciar sessão se não estiver iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Função de redirecionamento
function redirect($url) {
    header("Location: $url");
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
} catch (Exception $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Verificar se há mensagens de sucesso/erro
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$message_type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : '';
unset($_SESSION['message']);
unset($_SESSION['message_type']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GuiaTrem - Gerenciamento de Trens</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <nav class="navbar bg-primary-emphasis fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand text-white d-flex gap-2" href="../../public/html/dashboard.html">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="30" fill="currentColor"
                        class="bi bi-train-freight-front" viewBox="0 0 16 16">
                        <path
                            d="M5.065.158A1.5 1.5 0 0 1 5.736 0h4.528a1.5 1.5 0 0 1 .67.158l3.237 1.618a1.5 1.5 0 0 1 .83 1.342V13.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 13.5V3.118a1.5 1.5 0 0 1 .828-1.342zM2 9.372V13.5A1.5 1.5 0 0 0 3.5 15h4V8h-.853a.5.5 0 0 0-.144.021zM8.5 15h4a1.5 1.5 0 0 0 1.5-1.5V9.372l-4.503-1.35A.5.5 0 0 0 9.353 8H8.5zM14 8.328v-5.21a.5.5 0 0 0-.276-.447l-3.236-1.618A.5.5 0 0 0 10.264 1H5.736a.5.5 0 0 0-.223.053L2.277 2.67A.5.5 0 0 0 2 3.118v5.21l1-.3V5a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v3.028zm-2-.6V5H8.5v2h.853a1.5 1.5 0 0 1 .431.063zM7.5 7V5H4v2.728l2.216-.665A1.5 1.5 0 0 1 6.646 7zm-1-5a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm-3 8a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1m9 0a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1M5 13a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                    </svg> Guia Trem
                </a>

                <div class="d-flex">
                    <div class="d-flex align-items-center">
                        <a href="notificacao.html">
                            <button class="btn text-white position-relative" type="button" id="notificationBtn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                    class="bi bi-bell" viewBox="0 0 16 16">
                                    <path
                                        d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6" />
                                </svg>
                                <span
                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    id="notificationBadge" style="display: none;">
                                    0
                                </span>
                            </button>
                        </a>

                        <button class="navbar-toggler navbar-dark text-white" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="offcanvas offcanvas-end " tabindex="-1" id="offcanvasNavbar"
                aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header bg-primary-infi text-white gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="30" fill="currentColor"
                        class="bi bi-train-freight-front" viewBox="0 0 16 16">
                        <path
                            d="M5.065.158A1.5 1.5 0 0 1 5.736 0h4.528a1.5 1.5 0 0 1 .67.158l3.237 1.618a1.5 1.5 0 0 1 .83 1.342V13.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 13.5V3.118a1.5 1.5 0 0 1 .828-1.342zM2 9.372V13.5A1.5 1.5 0 0 0 3.5 15h4V8h-.853a.5.5 0 0 0-.144.021zM8.5 15h4a1.5 1.5 0 0 0 1.5-1.5V9.372l-4.503-1.35A.5.5 0 0 0 9.353 8H8.5zM14 8.328v-5.21a.5.5 0 0 0-.276-.447l-3.236-1.618A.5.5 0 0 0 10.264 1H5.736a.5.5 0 0 0-.223.053L2.277 2.67A.5.5 0 0 0 2 3.118v5.21l1-.3V5a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v3.028zm-2-.6V5H8.5v2h.853a1.5 1.5 0 0 1 .431.063zM7.5 7V5H4v2.728l2.216-.665A1.5 1.5 0 0 1 6.646 7zm-1-5a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm-3 8a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1m9 0a.5.5 0 1 0 0 1 .5.5 0 0 0 0-1M5 13a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 1a1 1 0 1 0 0-2 1 1 0 0 0 0 2" />
                    </svg>
                    <h5 class="offcanvas-title pg-1" id="offcanvasNavbarLabel">Guia Trem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>

                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <li class="nav-item list-unstyled ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-house-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z" />
                                        <path
                                            d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z" />
                                    </svg>
                                </li>
                            </div>
                            <div class="col">
                                <a class="nav-link active" aria-current="page" href="../../public/html/dashboard.html">Início</a>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <li class="nav-item list-unstyled">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                                    </svg>
                                </li>
                            </div>
                            <div class="col">
                                <a class="nav-link active" aria-current="page" href="../../public/html/gestao.html">Gestão de Rotas</a>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <li class="nav-item list-unstyled">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-gear-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z" />
                                    </svg>
                                </li>
                            </div>
                            <div class="col">
                                <a class="nav-link active" aria-current="page" href="../../public/html/monitoramento.html">Monitoramento
                                    de
                                    Manutenção</a>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <li class="nav-item list-unstyled">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-file-earmark-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M4 0h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2m5.5 1.5v2a1 1 0 0 0 1 1h2z" />
                                    </svg>
                                </li>
                            </div>
                            <div class="col">
                                <a class="nav-link active" aria-current="page" href="../../public/html/relatorio.html">Relatórios</a>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <li class="nav-item list-unstyled">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-tools" viewBox="0 0 16 16">
                                        <path
                                            d="M1 0 0 1l2.2 3.081a1 1 0 0 0 .815.419h.07a1 1 0 0 1 .708.293l2.675 2.675-2.617 2.654A3.003 3.003 0 0 0 0 13a3 3 0 1 0 5.878-.851l2.654-2.617.968.968-.305.914a1 1 0 0 0 .242 1.023l3.27 3.27a.997.997 0 0 0 1.414 0l1.586-1.586a.997.997 0 0 0 0-1.414l-3.27-3.27a1 1 0 0 0-1.023-.242L10.5 9.5l-.96-.96 2.68-2.643A3.005 3.005 0 0 0 16 3q0-.405-.102-.777l-2.14 2.141L12 4l-.364-1.757L13.777.102a3 3 0 0 0-3.675 3.68L7.462 6.46 4.793 3.793a1 1 0 0 1-.293-.707v-.071a1 1 0 0 0-.419-.814zm9.646 10.646a.5.5 0 0 1 .708 0l2.914 2.915a.5.5 0 0 1-.707.707l-2.915-2.914a.5.5 0 0 1 0-.708M3 11l.471.242.529.026.287.445.445.287.026.529L5 13l-.242.471-.026.529-.445.287-.287.445-.529.026L3 15l-.471-.242L2 14.732l-.287-.445L1.268 14l-.026-.529L1 13l.242-.471.026-.529.445-.287.287-.445.529-.026z" />
                                    </svg>
                                </li>
                            </div>
                            <div class="col">
                                <a class="nav-link active" aria-current="page" href="../../public/html/tecnico.html">Técnico de
                                    Manutenção</a>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <li class="nav-item list-unstyled">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z" />
                                    </svg>
                                </li>
                            </div>
                            <div class="col">
                                <a class="nav-link active" aria-current="page" href="../../public/html/estacoes.html">Adiconar
                                    Estações</a>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <li class="nav-item list-unstyled">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="bi bi-plus-circle-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3z" />
                                    </svg>
                                </li>
                            </div>
                            <div class="col">
                                <a class="nav-link active" aria-current="page" href="../../public/html/itinerarios.html">Gerenciamento Itinerários</a>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <li class="nav-item list-unstyled">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-clock-history" viewBox="0 0 16 16">
                                        <path
                                            d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z" />
                                        <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z" />
                                        <path
                                            d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5" />
                                    </svg>
                                </li>
                            </div>
                            <div class="col">
                                <a class="nav-link active" aria-current="page" href="../../public/html/historico.html">Histórico de
                                    Manutenção</a>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <li class="nav-item list-unstyled">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-gear-fill" viewBox="0 0 16 16">
                                        <path
                                            d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z">
                                        </path>
                                    </svg>
                                </li>
                            </div>
                            <div class="col">
                                <a class="nav-link active" aria-current="page"
                                    href="../../src/api/principal.php">Sistema e Sensores</a>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <li class="nav-item list-unstyled">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" 
                                        class="bi bi-train-front" viewBox="0 0 16 16">
                                        <path 
                                            d="M5.621 1.485c1.815-.454 2.943-.454 4.758 0 .784.196 1.743.673 2.527 1.119.688.39 1.094 1.148 1.094 1.979V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V4.583c0-.831.406-1.588 1.094-1.98.784-.445 1.744-.922 2.527-1.118m5-.97C8.647.02 7.353.02 5.38.515c-.924.23-1.982.766-2.78 1.22C1.566 2.322 1 3.432 1 4.582V13.5A2.5 2.5 0 0 0 3.5 16h9a2.5 2.5 0 0 0 2.5-2.5V4.583c0-1.15-.565-2.26-1.6-2.849-.797-.453-1.855-.988-2.779-1.22ZM5 13a1 1 0 1 1-2 0 1 1 0 0 1 2 0m0 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0m7 1a1 1 0 1 0-1-1 1 1 0 1 0-2 0 1 1 0 0 0 2 0 1 1 0 0 0 1 1M4.5 5a.5.5 0 0 0-.5.5v2a.5.5 0 0 0 .5.5h3V5zm4 0v3h3a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 0-.5-.5zM3 5.5A1.5 1.5 0 0 1 4.5 4h7A1.5 1.5 0 0 1 13 5.5v2A1.5 1.5 0 0 1 11.5 9h-7A1.5 1.5 0 0 1 3 7.5zM6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1z"/>
                                    </svg>
                                </li>
                            </div>
                            <div class="col">
                                <a class="nav-link active" aria-current="page"
                                    href="../../src/geren_trens/index.php">Gerenciamento de trens</a>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <li class="nav-item list-unstyled">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-person-circle" viewBox="0 0 16 16">
                                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                        <path fill-rule="evenodd"
                                            d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
                                    </svg>
                                </li>
                            </div>
                            <div class="col">
                                <a class="nav-link active" aria-current="page" href="../../public/html/perfil.html">Perfil</a>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <li class="nav-item list-unstyled">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                        class="bi bi-info-circle" viewBox="0 0 16 16">
                                        <path
                                            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                        <path
                                            d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                    </svg>
                                </li>
                            </div>
                            <div class="col">
                                <a class="nav-link active" aria-current="page" href="../../public/html/sobre.html">Sobre</a>
                            </div>
                        </div>
                        <div class="mt-auto text-center py-3">
                            <button class="btn btn-outline-danger logout-btn w-75" type="button" id="logoutBtn">
                                <i class="fas fa-sign-out-alt me-2"></i>Sair
                            </button>
                        </div>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <br>
    <br>

    <div class="container">
        
<br>

        <?php if($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="crud-section">
            <h2>Cadastrar Novo Trem</h2>
            <form action="create.php" method="POST" id="createForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="linha">Linha:</label>
                        <input type="text" id="linha" name="linha" required placeholder="Ex: Linha 1 - Verde">
                    </div>
                    <div class="form-group">
                        <label for="numero_trem">Número do Trem:</label>
                        <input type="text" id="numero_trem" name="numero_trem" required placeholder="Ex: T-001">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="modelo">Modelo:</label>
                        <input type="text" id="modelo" name="modelo" required placeholder="Ex: Modelo MX5000">
                    </div>
                    <div class="form-group">
                        <label for="capacidade">Capacidade:</label>
                        <input type="number" id="capacidade" name="capacidade" required min="1" placeholder="Ex: 1500">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="status_trem">Status:</label>
                        <select id="status_trem" name="status_trem" required>
                            <option value="ativo">Ativo</option>
                            <option value="manutencao">Manutenção</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="data_ultima_manutencao">Data da Última Manutenção:</label>
                        <input type="date" id="data_ultima_manutencao" name="data_ultima_manutencao">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Cadastrar Trem</button>
            </form>
        </div>

        <div class="crud-section">
            <h2>Frota de Trens</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Linha</th>
                            <th>Número</th>
                            <th>Modelo</th>
                            <th>Capacidade</th>
                            <th>Status</th>
                            <th>Última Manutenção</th>
                            <th>Data Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $query = "SELECT * FROM trens ORDER BY id_trem DESC";
                            $stmt = $db->prepare($query);
                            $stmt->execute();
                            
                            if($stmt->rowCount() > 0) {
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $status_class = '';
                                    switch($row['status_trem']) {
                                        case 'ativo': $status_class = 'status-ativo'; break;
                                        case 'manutencao': $status_class = 'status-manutencao'; break;
                                        case 'inativo': $status_class = 'status-inativo'; break;
                                    }
                                    
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['id_trem']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['linha']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['numero_trem']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['modelo']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['capacidade']) . " passageiros</td>";
                                    echo "<td><span class='status-badge " . $status_class . "'>" . ucfirst(htmlspecialchars($row['status_trem'])) . "</span></td>";
                                    echo "<td>" . ($row['data_ultima_manutencao'] ? date('d/m/Y', strtotime($row['data_ultima_manutencao'])) : 'N/A') . "</td>";
                                    echo "<td>" . date('d/m/Y H:i', strtotime($row['data_criacao'])) . "</td>";
                                    echo "<td class='actions'>";
                                    echo "<button class='btn btn-edit' onclick='openEditModal(" . json_encode($row) . ")'>Editar</button>";
                                    echo "<button class='btn btn-delete' onclick='confirmDelete(" . $row['id_trem'] . ")'>Excluir</button>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>Nenhum trem cadastrado</td></tr>";
                            }
                        } catch (Exception $e) {
                            echo "<tr><td colspan='9'>Erro ao carregar dados: " . $e->getMessage() . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para Edição -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Editar Trem</h2>
            <form id="editForm" action="update.php" method="POST">
                <input type="hidden" id="edit_id_trem" name="id_trem">
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_linha">Linha:</label>
                        <input type="text" id="edit_linha" name="linha" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_numero_trem">Número do Trem:</label>
                        <input type="text" id="edit_numero_trem" name="numero_trem" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_modelo">Modelo:</label>
                        <input type="text" id="edit_modelo" name="modelo" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_capacidade">Capacidade:</label>
                        <input type="number" id="edit_capacidade" name="capacidade" required min="1">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_status_trem">Status:</label>
                        <select id="edit_status_trem" name="status_trem" required>
                            <option value="ativo">Ativo</option>
                            <option value="manutencao">Manutenção</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_data_ultima_manutencao">Data da Última Manutenção:</label>
                        <input type="date" id="edit_data_ultima_manutencao" name="data_ultima_manutencao">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Atualizar Trem</button>
            </form>
        </div>
    </div>
</body>
</html>