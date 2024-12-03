<?php
// Iniciar a sessão
session_start();

// Incluir a conexão com o banco de dados
include("config.php");

// Consulta para buscar os registros
$sql = "SELECT id, nome, email, telefone, data_nascimento FROM aluno_cadastro";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se todos os campos estão preenchidos
    if (!empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['telefone']) && !empty($_POST['data_nascimento'])) {

        // Captura os valores do formulário
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $data_nascimento = $_POST['data_nascimento'];


            try {
                // Prepara a query SQL para inserir os dados
                $query = $conn->prepare("INSERT INTO aluno_cadastro (nome, email, telefone, data_nascimento) 
                                         VALUES (:nome, :email, :telefone, :data_nascimento)");

                // Vincula os parâmetros da query
                $query->bindParam(':nome', $nome);
                $query->bindParam(':email', $email);
                $query->bindParam(':telefone', $telefone);
                $query->bindParam(':data_nascimento', $data_nascimento);

                // Executa a query e verifica se foi bem-sucedida
                if ($query->execute()) {
                    $mensagem = "Cadastro realizado com sucesso!";
                    $tipo_alerta = "success";
                } else {
                    $mensagem = "Erro ao cadastrar o aluno.";
                    $tipo_alerta = "error";
                }
            } catch (PDOException $e) {
                $mensagem = "Erro ao cadastrar o aluno: " . $e->getMessage();
                $tipo_alerta = "error";
            }
        } else {
            $mensagem = "As senhas não coincidem. Tente novamente.";
            $tipo_alerta = "error";
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos.";
        $tipo_alerta = "error";
    }

    // Consulta para buscar os registros
$sql = "SELECT id, nome, email, telefone, data_nascimento FROM aluno_cadastro";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Funcionários - Visualização</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Seu CSS aqui */
        body {
            font-family: Arial, sans-serif;
            background-color:  #e4edfa6c;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #007bff;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 2px solid #007bff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 20px;
            text-align: left;
            border: 1px solid #007bff;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e9ecef;
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 0px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
            margin-top: 1060px;
            margin-left: -1250px;
            margin-bottom: 10px;
        }

        .container h2 {
            background-color: #5aa2b0;
            color: white;
            text-align: center;
            padding: 15px 0;
            border-radius: 0px 0px 0 0;
            box-shadow: 50 10 15px rgba(0, 0, 0, 0.1);
            margin-top: -20px;
            width: calc(100% + 40px); /* Ajustado para largura total */
            margin-left: -20px;
            height: 40px;
            margin-bottom: 30px;

            font-family: 'Arial', sans-serif; /* Fonte personalizada */
            font-size: 35px; /* Tamanho da fonte */
            line-height: 40px; /* Alinhamento vertical do texto */
            font-weight: bold; /* Estilo em negrito */
            font-weight: 300; /* Letra mais fina */
            letter-spacing: 1px; /* Espaçamento entre letras */
        }

        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            padding: 20px;
        }

        label {
            font-weight: bold;
        }

        label[for="nome"] {
            font-weight: bold;
            font-size: 16px;
            color: #000000; /* Exemplo de cor personalizada */
            margin-left: 220px; /* Ajuste para alinhamento */
            font-size: 14px; 
        }


        input[type="password"] {
            width: 70%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box; /* Para garantir que o padding não aumente a largura total */
            min-height: 20px; /* Altura mínima para todos os campos */
            margin-left: 100px;
            font-size: 10px; 
        }

        input#confirma_senha {
            width: 70%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box; /* Para garantir que o padding não aumente a largura total */
            min-height: 20px; /* Altura mínima para todos os campos */
            margin-left: 5px;
            font-size: 10px; 
        }

        input[type="date"] {
            width: 70%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box; /* Para garantir que o padding não aumente a largura total */
            min-height: 20px; /* Altura mínima para todos os campos */
            margin-left: 5px;
            font-size: 10px; 
        }

        input[type="tel"] {
            width: 70%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box; /* Para garantir que o padding não aumente a largura total */
            min-height: 20px; /* Altura mínima para todos os campos */
            margin-left: 100px;
            font-size: 10px; 
        }

        input[type="email"] {
            width: 150%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box; /* Para garantir que o padding não aumente a largura total */
            min-height: 20px; /* Altura mínima para todos os campos */
            margin-left: 100px;
            font-size: 10px; 
        }
        
        input[type="text"] {
            width: 150%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box; /* Para garantir que o padding não aumente a largura total */
            min-height: 20px; /* Altura mínima para todos os campos */
            margin-left: 100px;
            font-size: 10px; 
        }

        label[for="nome"] {
            font-weight: bold;
            font-size: 16px;
            color: #000000; /* Exemplo de cor personalizada */
            margin-left: 110px; /* Ajuste para alinhamento */
            font-size: 14px; 
        }

        label[for="email"] {
            font-weight: bold;
            font-size: 16px;
            color: #000000; /* Exemplo de cor personalizada */
            margin-left: 110px; /* Ajuste para alinhamento */
            font-size: 14px; 
        }

        label[for="telefone"] {
            font-weight: bold;
            font-size: 16px;
            color: #000000; /* Exemplo de cor personalizada */
            margin-left: 110px; /* Ajuste para alinhamento */
            font-size: 14px; 
        }

        label[for="senha"] {
            font-weight: bold;
            font-size: 16px;
            color: #000000; /* Exemplo de cor personalizada */
            margin-left: 110px; /* Ajuste para alinhamento */
            font-size: 14px; 
        }

        label[for="confirma_senha"] {
            font-weight: bold;
            font-size: 16px;
            color: #000000; /* Exemplo de cor personalizada */
            margin-left: 15px; /* Ajuste para alinhamento */
            font-size: 14px; 
        }

        label[for="data_nascimento"] {
            font-weight: bold;
            font-size: 16px;
            color: #000000; /* Exemplo de cor personalizada */
            margin-left: 15px; /* Ajuste para alinhamento */
            font-size: 14px; 
        }

        button {
            grid-column: span 2;
            padding: 15px;
            background-color: #174650;
            color: white;
            border: none;
            border-radius: 30px;
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.1);
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%; /* Largura total para ocupar espaço */
            max-width: 200px; /* Tamanho máximo do botão */
            margin: 20px auto; /* Centralizando o botão */
            height: 45px;
            margin-top: 70px;
            margin-bottom: 70px;
        }

        button:hover {
            background-color: #5aa2b0;
        }

        .error {
            color: red;
            margin-top: 5px;
            font-size: 14px;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #ffffff;
            position: relative;
            max-height: 45px;
            height: auto;
            margin-bottom: 50px;
            margin-top: -20px;
            width: 1477px;
            margin-left: -20px;
        }

        .logo img {
            max-width: 105px;
            height: auto;
            margin-top: 7px;
            margin-right: 20px;
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
            text-decoration: none; /* Remove o sublinhado dos links */;

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
        }

        /* Container para o menu e botão de login */
        .menu-login-container {
            display: flex;
            align-items: center;
            margin-left: auto; /* Para alinhar à direita */
        }

        /* Estilo responsivo */
        @media (max-width: 768px) {
            .nav menu {
                display: none; /* Oculta o menu em telas menores */
                flex-direction: column;
                position: absolute;
                top: 60px;
                left: 0;
                width: 100%;
                background-color: #fff;
                padding: 10px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
                margin-left: 0; /* Remove o margin-left em telas menores */
            }

            .menu-icon {
                display: block; /* Exibe o ícone do menu em telas menores */
                color: #2f2c73;
            }

            .menu a {
                padding: 10px;
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
            <!-- Menu de navegação -->
            <nav class="menu" id="menu">
                <a href="./te.php">Início</a>
                <a href="#">Lista</a>
                <a href="#">Cadastros</a>
                <a href="#">Relatórios</a>
                <a href="#">Configuração</a>
            </nav>
<div>
<div class="container">
        <h2 class="titulo-principal">Cadastrar aluno</h2>
        <form id="cadastroForm" action="" method="post">
<!-- Campos de formulário -->
<div>
    <label for="nome">Nome Completo</label>
    <input type="text" id="nome" name="nome" placeholder="Nome Completo" value="<?php echo isset($_POST['nome']) ? $_POST['nome'] : ''; ?>" required>
</div>
<br>
<div>
    <label for="email">E-mail</label>
    <input type="email" id="email" name="email" placeholder="E-mail" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
</div>
<br>
<div>
    <label for="telefone">Telefone</label>
    <input type="tel" id="telefone" name="telefone" placeholder="( ) 00000-0000" value="<?php echo isset($_POST['telefone']) ? $_POST['telefone'] : ''; ?>" required>
</div>
<div>
    <label for="data_nascimento">Data de Nascimento</label>
    <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo isset($_POST['data_nascimento']) ? $_POST['data_nascimento'] : ''; ?>" required>
</div>
<div>
<button type="submit">Cadastrar</button>
        </form>
    </div>

    <div>
     <!-- Passa as variáveis PHP para o JavaScript -->
     <script>
        var mensagem = "<?php echo $mensagem; ?>";
        var tipoAlerta = "<?php echo $tipo_alerta; ?>";
    </script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Mostra a mensagem de sucesso ou erro quando o formulário for submetido
        document.addEventListener("DOMContentLoaded", function() {
            if (mensagem && tipoAlerta) {
                Swal.fire({
                    icon: tipoAlerta,
                    title: mensagem,
                    showConfirmButton: true
                });
            }
        });
    </script>
    <div>

    <h1>Alunos Cadastrados</h1>
    
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Data Nascimento</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($result as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nome']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['telefone']) ?></td>
                    <td><?= htmlspecialchars($row['data_nascimento']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
