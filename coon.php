<?php
// Inicia a sessão
session_start();

// Configura o tempo de vida da sessão
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);

// Ativa o modo de depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifica se a sessão foi iniciada corretamente
if (session_status() !== PHP_SESSION_ACTIVE) {
    error_log("Erro: A sessão não foi iniciada corretamente.");
    exit("Erro: A sessão não foi iniciada corretamente.");
}

// Verifica se o usuário está logado
if (!isset($_SESSION['id_aluno'])) {
    error_log("Sessão expirada ou ID do aluno não está configurado. Redirecionando para a página de login.");
    header("Location: inicioo.php");
    exit;
}

// Inclui a configuração do banco
include("config.php");

// Testa a c…
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status da Inscrição</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: "Open Sans", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ededed;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: right;
            width: 100%;
            margin: 0px 0px;
        }

        header {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            margin-bottom: 3px;
            align-items: center;
            padding: 0px;
            height: 60px;
            background-color: #f9f9f9;
        }

        .logo img {
            height: 55px;
            margin-top: 8px;
            margin-left: 5px;
        }

        .menu {
            background-color: #f9f9f9;
            padding: 18px;
            text-align: center;
            display: flex;
            align-items: center;
            box-shadow: #f9f9f9;
            margin-top: 15px;
        }

        .menu a {
            text-decoration: none;
            color: rgb(129, 121, 121);
            padding: 10px 25px;
            font-family: 'Open-sans', sans-serif;
            font-size: 16px;
            font-weight: normal;
            transition: 0.3s ease;
            position: relative;
            display: inline-block;
            margin: 0 15px;
            margin-top: -18px;
        }

        .menu a:hover {
            background-color: #f9f9f9;
        }

        .menu a::after {
            content: "";
            position: absolute;
            width: 0;
            height: 3.2px;
            bottom: -5px;
            left: 0;
            background-color: rgb(187, 87, 165);
            transition: width 0.3s ease-in-out;
        }

        .menu-login-container {
            display: flex;
            align-items: center;
            margin-left: auto;
        }

        .menu {
            margin-top: -7px;
            display: flex;
            flex-direction: row;
            gap: 15px;
            position: static;
            background-color: transparent;
            margin-left: auto;
            font-family: "Open Sans", sans-serif;
        }

        .menu a {
            text-decoration: none;
            padding: 0 15px;
            color: rgb(129, 121, 121);
            font-size: 17px;
            font-weight: normal;
            transition: 0.3s ease;
            position: relative;
            margin: 0 15px;
            font-family: 'Open-sans', sans-serif;
            margin-top: 11px;
        }

        .menu-icon {
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer;
            display: none;
            margin-top: 10px;
            margin-right: 15px;
        }

        .user-menu {
            position: relative;
        }

        .user-button {
            background-color: #06357b;
            color: #ffffff;
            padding: 8px 15px;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-left: 10px;
            margin-right: 13px;
            height: 45px;
            width: 45px;
        }

        .user-button i {
            font-size: 19px;
            margin-left: -1px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #ffffff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            min-width: 180px;
            z-index: 1000;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
        }

        .dropdown-menu a:hover {
            background-color: #f4f4f9;
        }

        .user-menu:hover .dropdown-menu {
            display: block;
        }

        .modal-logoff {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content-logoff {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            width: 300px;
            text-align: center;
            position: relative;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-confirmar, .btn-cancelar {
            padding: 8px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .btn-confirmar {
            background-color: #174650;
            color: white;
        }

        .btn-confirmar:hover {
            background-color: #5aa2b0;
        }

        .btn-cancelar {
            background-color: #dc3545;
            color: white;
        }

        .btn-cancelar:hover {
            background-color: #c82333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }

        .status-card {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }

        .status-badge {
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            display: inline-block;
            margin: 10px 0;
            font-size: 1.1em;
        }

        .status-aprovado {
            background-color: #d4edda;
            color: #155724;
        }

        .status-pendente {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-reprovado {
            background-color: #f8d7da;
            color: #721c24;
        }

        .info-row {
            margin: 15px 0;
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 1.1em;
        }

        .info-label {
            font-weight: bold;
            color: #495057;
            margin-right: 10px;
        }

        .documents-section {
            margin-top: 25px;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }

        .document-item {
            display: flex;
            align-items: center;
            margin: 12px 0;
            padding: 8px;
            background-color: white;
            border-radius: 5px;
        }

        .document-icon {
            margin-right: 15px;
            font-size: 1.2em;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-success {
            background-color: #28a745;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn:hover {
            opacity: 0.9;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .status-card {
                padding: 15px;
            }

            .info-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header class="header-container">
        <div class="logo">
            <img src="./img/Slide S.P.M. (13).png" alt="Logotipo">
        </div>

        <div class="menu-login-container">
            <nav class="menu" id="menu">
                <a href="./logadoaluno.php">Início</a>
                <a href="./inc4.php">Inscrição</a>
                <a href="./consulta.php">Consulta</a>
                <a href="./duvidaa.html">Dúvidas</a>
                <a href="./rregras1.html">Regras</a>
                <a href="./sobre1.html">Sobre</a>
            </nav>

            <div class="user-menu">
                <button class="user-button" id="menu-toggle">
                    <i class="fas fa-user"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="edit.it.php">Perfil</a>
                    <a href="javascript:void(0);" onclick="confirmarLogoff()">Sair</a>
                </div>
            </div>
        </div>
    </header>

    <div id="logoffModal" class="modal-logoff">
        <div class="modal-content-logoff">
            <h2>Confirmar Saída</h2>
            <p>Tem certeza que deseja sair do sistema?</p>
            <div class="modal-buttons">
                <button onclick="realizarLogoff()" class="btn-confirmar">Confirmar</button>
                <button onclick="fecharModalLogoff()" class="btn-cancelar">Cancelar</button>
            </div>
        </div>
    </div>

    <div class="container">
        <h1 style="text-align: center; color: #333; margin-bottom: 30px;">Status da Inscrição</h1>

        <?php if ($inscricao): ?>
            <div class="status-card">
                <h2 style="color: #333; margin-bottom: 20px;">Informações da Inscrição</h2>
                
                <div style="text-align: center; margin-bottom: 20px;">
                    <?php
                    $statusClass = '';
                    $statusText = '';
                    $statusIcon = '';
                    
                    switch($inscricao['status']) {
                        case 'aprovado':
                            $statusClass = 'status-aprovado';
                            $statusText = 'Aprovado';
                            $statusIcon = 'check-circle';
                            break;
                        case 'pendente':
                            $statusClass = 'status-pendente';
                            $statusText = 'Em Análise';
                            $statusIcon = 'clock';
                            break;
                        default:
                            $statusClass = 'status-pendente';
                            $statusText = 'Pendente';
                            $statusIcon = 'exclamation-circle';
                    }
                    ?>
                    <span class="status-badge <?php echo $statusClass; ?>">
                        <i class="fas fa-<?php echo $statusIcon; ?> me-2"></i>
                        <?php echo $statusText; ?>
                    </span>
                </div>

                <div class="info-row">
                    <span class="info-label">Nome:</span>
                    <?php echo htmlspecialchars($inscricao['nome']); ?>
                </div>

                <div class="info-row">
                    <span class="info-label">Curso:</span>
                    <?php echo htmlspecialchars($inscricao['nome_curso']); ?>
                </div>

                <div class="info-row">
                    <span class="info-label">Data da Inscrição:</span>
                    <?php echo formatarData($inscricao['data_matricula']); ?>
                </div>

                <div class="documents-section">
                    <h3 style="color: #666; margin-bottom: 15px;">Documentos Enviados</h3>
                    
                    <?php
                    $documentos = [
                        'RG (Frente)' => $inscricao['identidade_frente'],
                        'RG (Verso)' => $inscricao['identidade_verso'],
                        'CPF' => $inscricao['cpf_frente'],
                        'Histórico' => $inscricao['historico'],
                        'Foto' => $inscricao['foto']
                    ];

                    foreach ($documentos as $nome => $documento):
                        $icon = $documento ? 'check' : 'times';
                        $color = $documento ? '#28a745' : '#dc3545';
                    ?>
                        <div class="document-item">
                            <i class="fas fa-<?php echo $icon; ?>" style="color: <?php echo $color; ?>"></i>
                            <span style="margin-left: 10px;"><?php echo $nome; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($inscricao['status'] == 'aprovado'): ?>
                    <div style="text-align: center; margin-top: 20px;">
                        <p style="color: #28a745; font-weight: bold;">
                            Parabéns! Sua inscrição foi aprovada.
                        </p>
                        <a href="index.php" class="btn btn-success">
                            Acessar Dashboard
                        </a>
                    </div>
                <?php elseif ($inscricao['status'] == 'pendente'): ?>
                    <div style="text-align: center; margin-top: 20px;">
                        <p style="color: #856404;">
                            Sua inscrição está em análise. Por favor, aguarde o resultado.
                        </p>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; margin-top: 20px;">
                        <p style="color: #721c24;">
                            Por favor, complete o envio de todos os documentos necessários.
                        </p>
                        <a href="inscricao(3).php" class="btn btn-danger">
                            Completar Documentação
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="status-card" style="text-align: center;">
                <p style="color: #721c24;">Nenhuma inscrição encontrada.</p>
                <a href="inscricao (3).php" class="btn btn-primary">
                    Fazer Inscrição
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function confirmarLogoff() {
            const modal = document.getElementById('logoffModal');
            modal.style.display = 'block';
        }

        function fecharModalLogoff() {
            const modal = document.getElementById('logoffModal');
            modal.style.display = 'none';
        }

        function realizarLogoff() {
            window.location.href = 'logout.php';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('logoffModal');
            if (event.target === modal) {
                fecharModalLogoff();
            }
        }
    </script>
</body>
</html>