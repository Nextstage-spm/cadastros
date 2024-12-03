<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

// Inclui a configuração do banco
include("config.php");

// Define mensagens de status
$statusMessages = [
    'aprovado' => [
        'title' => 'Inscrição Aprovada!',
        'message' => 'Parabéns! Sua inscrição foi aprovada. Você já pode acessar todos os recursos disponíveis.',
        'icon' => 'check-circle',
        'color' => '#28a745'
    ],
    'pendente' => [
        'title' => 'Em Análise',
        'message' => 'Sua inscrição está sendo analisada por nossa equipe. Prazo médio de 5 dias úteis.',
        'icon' => 'clock',
        'color' => '#ffc107'
    ],
    'incompleto' => [
        'title' => 'Documentação Pendente',
        'message' => 'Alguns documentos ainda precisam ser enviados para completar sua inscrição.',
        'icon' => 'exclamation-circle',
        'color' => '#dc3545'
    ]
];

// Busca os dados do aluno com base na sessão
try {
    $sql = "SELECT 
                a.id,
                a.nome,
                a.data_matricula,
                a.identidade_frente,
                a.identidade_verso,
                a.cpf_frente,
                a.historico,
                a.foto,
                a.status,
                c.nome as nome_curso 
            FROM aluno_cadastro a 
            LEFT JOIN cursos c ON a.idcursos = c.id 
            WHERE a.id = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute(['id' => $_SESSION['id']]);
    $inscricao = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($inscricao) {
        // Calcula o progresso da documentação
        $documentos = [
            $inscricao['identidade_frente'],
            $inscricao['identidade_verso'],
            $inscricao['cpf_frente'],
            $inscricao['historico'],
            $inscricao['foto']
        ];
        
        $documentosEnviados = array_filter($documentos);
        $progressPercentage = round((count($documentosEnviados) / count($documentos)) * 100);
    } else {
        $inscricao = null;
        $progressPercentage = 0;
    }
} catch (PDOException $e) {
    $inscricao = null;
    $progressPercentage = 0;
}

// Função para formatar a data
function formatarData($data) {
    return date('d/m/Y', strtotime($data));
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status da Inscrição</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Tippy.js para tooltips -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <style>
        body {
            font-family: "Open Sans", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #f9f9f9;
            position: relative;
            max-height: 45px;
            height: auto;
        }

        .logo img {
            max-width: 105px;
            height: auto;
            margin-top: 7px;
        }

        /* Container para o menu e botão de login */
        .menu-login-container {
            display: flex;
            align-items: center;
            margin-left: auto; /* Para alinhar à direita */
        }

        /* Menu visível por padrão */
        .menu {
            margin-top: -7px;
            display: flex;
            flex-direction: row;
            gap: 15px;
            position: static;
            background-color: transparent;
            margin-left: auto; /* Para alinhar o menu à direita */
            font-family: "Open Sans", sans-serif;
        }


        .menu a {
            text-decoration: none;
            padding: 0 15px;
            display: block;
            text-decoration: none; /* Remove sublinhado padrão */
            color: rgb(129, 121, 121); /* Cor do texto */
            text-decoration: none; /* Remove o sublinhado dos links */; 
            font-size: 17px; /* Tamanho da fonte */
            font-weight: normal;
            transition: 0.3s ease; /* Efeito suave ao passar o mouse */
            position: relative;
            margin: 0 15px;
            font-family: 'Open-sans', sans-serif;
            margin-top: 11px;
        }

        /* Ícone do menu (hambúrguer) */
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

    .modal-content-logoff h2 {
        color: #174650;
        margin-bottom: 15px;
        font-size: 1.5em;
    }

    .modal-content-logoff p {
        margin-bottom: 20px;
        color: #666;
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

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

  

        /* Container Principal */
        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
        }

        /* Card de Status */
        .status-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        /* Cabeçalho do Card */
        .card-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .card-header h2 {
            color: #333;
            font-size: 24px;
            margin-bottom: 15px;
        }

        /* Badge de Status */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1em;
            margin: 15px 0;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Barra de Progresso */
        .progress-container {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .progress-bar {
            height: 10px;
            background: #e9ecef;
            border-radius: 5px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress {
            height: 100%;
            background: linear-gradient(45deg, #007bff, #00d4ff);
            transition: width 0.5s ease;
            border-radius: 5px;
        }

        .progress-text {
            display: block;
            text-align: center;
            color: #666;
            font-weight: 600;
            margin-top: 10px;
        }

        /* Informações do Aluno */
        .info-section {
            margin: 30px 0;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .info-row {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            width: 200px;
        }

        .info-value {
            flex: 1;
            color: #333;
        }

        /* Documentos */
        .documents-section {
            margin-top: 30px;
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
        }

        .documents-section h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.2em;
        }

        .document-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .document-item:hover {
            transform: translateX(10px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .document-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 15px;
        }

        /* Botões */
        .btn {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            color: white;
            font-weight: 600;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            margin: 10px;
        }

        .btn-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
        }

        .btn-success {
            background: linear-gradient(45deg, #28a745, #218838);
        }

        .btn-danger {
            background: linear-gradient(45deg, #dc3545, #c82333);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        /* Animações */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .status-card {
                padding: 20px;
            }

            .info-row {
                flex-direction: column;
                text-align: left;
            }

            .info-label {
                width: 100%;
                margin-bottom: 5px;
            }

            .document-item {
                flex-direction: column;
                text-align: center;
                padding: 20px;
            }

            .document-icon {
                margin: 0 0 10px 0;
            }

            .btn {
                width: 100%;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <!-- Header (mantido do código original) -->
    <header class="header-container">
        <div class="logo">
            <img src="./img/Slide S.P.M. (13).png" alt="Logotipo">
        </div>

        <div class="menu-login-container">
            <nav class="menu" id="menu">
                <a href="./logadoaluno.php">Início</a>
                <a href="./inc4.php">Inscrição</a>
                <a href="./conn.php">Consulta</a>
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

    <!-- Modal de Logoff -->
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

    <!-- Conteúdo Principal -->
    <div class="container">
        <div class="status-card fade-in">
            <div class="card-header">
                <h2>Status da Inscrição</h2>
                
                <?php if ($inscricao): ?>
                    <?php
                    $status = $inscricao['status'] ?? 'pendente';
                    $statusInfo = $statusMessages[$status] ?? $statusMessages['pendente'];
                    ?>
                    <div class="status-badge" style="background-color: <?php echo $statusInfo['color']; ?>20; color: <?php echo $statusInfo['color']; ?>">
                        <i class="fas fa-<?php echo $statusInfo['icon']; ?>" style="margin-right: 10px;"></i>
                        <?php echo $statusInfo['title']; ?>
                    </div>
                    
                    <p><?php echo $statusInfo['message']; ?></p>

                    <!-- Barra de Progresso -->
                    <div class="progress-container">
                        <h3>Progresso da Documentação</h3>
                        <div class="progress-bar">
                            <div class="progress" style="width: <?php echo $progressPercentage; ?>%"></div>
                        </div>
                        <span class="progress-text"><?php echo $progressPercentage; ?>% completo</span>
                    </div>

                    <!-- Informações do Aluno -->
                    <div class="info-section">
                        <div class="info-row">
                            <span class="info-label">Nome:</span>
                            <span class="info-value"><?php echo htmlspecialchars($inscricao['nome']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Curso:</span>
                            <span class="info-value"><?php echo htmlspecialchars($inscricao['nome_curso']); ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Data da Inscrição:</span>
                            <span class="info-value"><?php echo formatarData($inscricao['data_matricula']); ?></span>
                        </div>
                    </div>

                    <!-- Documentos -->
                    <div class="documents-section">
                        <h3>Documentos Enviados</h3>
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
                            $status = $documento ? 'Documento enviado' : 'Documento pendente';
                        ?>
                            <div class="document-item" data-tooltip="<?php echo $status; ?>">
                                <div class="document-icon" style="background-color: <?php echo $color; ?>20">
                                    <i class="fas fa-<?php echo $icon; ?>" style="color: <?php echo $color; ?>"></i>
                                </div>
                                <span><?php echo $nome; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Botões de Ação -->
                    <div style="text-align: center; margin-top: 30px;">
                        <?php if ($inscricao['status'] == 'aprovado'): ?>
                            <a href="logadoaluno.php" class="btn btn-success">
                                <i class="fas fa-check-circle"></i> Acessar Dashboard
                            </a>
                        <?php elseif ($progressPercentage < 100): ?>
                            <a href="inscricao.php" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Completar Documentação
                            </a>
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    <div style="text-align: center; margin: 50px 0;">
                        <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #dc3545; margin-bottom: 20px;"></i>
                        <p style="font-size: 1.2em; color: #666;">Nenhuma inscrição encontrada.</p>
                        <a href="inscricao.php" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Fazer Inscrição
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Inicialização dos tooltips
        tippy('[data-tooltip]', {
            placement: 'top',
            animation: 'scale',
            theme: 'custom'
        });

        // Funções do Modal de Logoff
        function confirmarLogoff() {
            const modal = document.getElementById('logoffModal');
            modal.style.display = 'block';
        }

        function fecharModalLogoff() {
            const modal = document.getElementById('logoffModal');
            modal.style.display = 'none';
        }

        function realizarLogoff() {
            window.location.href = 'index.php';
        }

        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById('logoffModal');
            if (event.target === modal) {
                fecharModalLogoff();
            }
        }

        // Animação de elementos quando entram na viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                }
            });
        });

        document.querySelectorAll('.status-card, .document-item').forEach((el) => {
            observer.observe(el);
        });
    </script>
</body>
</html>