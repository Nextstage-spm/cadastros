<?php 
include("config.php"); // Inclui a conexão com o banco de dados

$mensagem = ""; // Inicializa a variável de mensagem

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['nome']) && !empty($_POST['nome']) && 
        isset($_POST['cursos']) && !empty($_POST['cursos']) && 
        isset($_POST['periodo']) && !empty($_POST['periodo'])) {
        
        // Captura os valores do formulário
        $nome = $_POST['nome'];
        $periodo = $_POST['periodo'];
        $cursos_cod = $_POST['cursos'];

        try {
            // Verifica se a turma já está cadastrada
            $query = $conn->prepare("SELECT COUNT(*) FROM turma WHERE nome = :nome AND periodo = :periodo AND cursos_cod = :cursos_cod");
            $query->bindParam(':nome', $nome);
            $query->bindParam(':periodo', $periodo);
            $query->bindParam(':cursos_cod', $cursos_cod);
            $query->execute();
            $count = $query->fetchColumn();

            if ($count > 0) {
                $mensagem = "turma_existente"; // Turma já cadastrada
            } else {
                // Prepara a query para inserir os dados na tabela 'turma'
                $query = $conn->prepare("INSERT INTO turma (nome, periodo, cursos_cod) VALUES (:nome, :periodo, :cursos_cod)");

                // Faz o bind dos parâmetros
                $query->bindParam(':nome', $nome);
                $query->bindParam(':periodo', $periodo);
                $query->bindParam(':cursos_cod', $cursos_cod);

                // Executa a query e verifica se foi bem-sucedida
                if ($query->execute()) {
                    $mensagem = "sucesso"; // Cadastro bem-sucedido
                } else {
                    $mensagem = "erro"; // Erro ao cadastrar
                }
            }
        } catch (PDOException $e) {
            $mensagem = "erro"; // Erro ao executar a query
        }
    } else {
        $mensagem = "campos_invalidos"; // Campos não preenchidos corretamente
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Turma</title>
    <style>
        /* Estilos do formulário */
        body {
            font-family: "Open Sans", sans-serif;
            background-color: #f4f4f9;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 0px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
            margin-top: 730px;
            margin-left: -1155px;
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

        input[type="text"] {
            width: 90%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box; /* Para garantir que o padding não aumente a largura total */
            min-height: 20px; /* Altura mínima para todos os campos */
            margin-left: 208px;
            font-size: 10px; 
        }

        input[type="text"],
        select {
            width: 90%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box; /* Para garantir que o padding não aumente a largura total */
            min-height: 20px; /* Altura mínima para todos os campos */
            margin-left: 208px;
            font-size: 10px; 
        }

        label[for="nome"] {
            font-weight: bold;
            font-size: 16px;
            color: #000000; /* Exemplo de cor personalizada */
            margin-left: 220px; /* Ajuste para alinhamento */
            font-size: 14px; 
        }

        label[for="periodo"] {
            font-weight: bold;
            font-size: 16px;
            color: #000000; /* Exemplo de cor personalizada */
            margin-left: 220px; /* Ajuste para alinhamento */
            font-size: 14px; 
        }

        label[for="cursos"] {
            font-weight: bold;
            font-size: 16px;
            color: #000000; /* Exemplo de cor personalizada */
            margin-left: -170px; /* Ajuste para alinhamento */
            font-size: 14px; 
            margin-top: 80px;
            margin-bottom: -20px ;
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
            margin-bottom: -5px;
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
            margin-top: -8px;
            width: 1492px;
            margin-left: -5px;
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
        <h2 class="titulo-principal">Cadastrar turma</h2>
        <form id="cadastroForm" action="" method="post">
            <div>
                <label for="nome">Turma</label>
                <input type="text" id="nome" name="nome" placeholder="Curso" required>
                <div id="nomeError" class="error"></div> <!-- Mensagem de erro -->
            </div>
            <br>
            <div>
                <label for="periodo">Período</label>
                <input type="text" id="periodo" name="periodo" placeholder="Período" required>
                <div id="periodoError" class="error"></div> <!-- Mensagem de erro -->
            </div>

            <!-- Campo para selecionar a titulação do professor -->
                <label for="cursos">Cursos</label>
                <select name="cursos" required>
                   <option value="">Selecione</option>
            <?php
          // Consulta para obter as titulações disponível
             $query = $conn->query("SELECT id, nome FROM cursos ORDER BY nome ASC");
             $registros = $query->fetchAll(PDO::FETCH_ASSOC);

        // Exibe as opções de titulação no select
        foreach ($registros as $option) {
            echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
        }
        ?>
    </select>
            <button type="submit">Cadastrar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <script>
        const mensagem = "<?php echo $mensagem; ?>";

    // Exibe o popup apropriado de acordo com o valor da mensagem
    if (mensagem === "sucesso") {
        Swal.fire({
            icon: 'success',
            title: 'Cadastro realizado com sucesso!',
            showConfirmButton: false,
            timer: 1500,
            position: 'center'
        });
    } else if (mensagem === "turma_existente") {
        Swal.fire({
            icon: 'warning',
            title: 'Turma já cadastrada!',
            text: 'Este curso já existe no sistema.',
            showConfirmButton: true,
            position: 'center'
        });
    } else if (mensagem === "erro") {
        Swal.fire({
            icon: 'error',
            title: 'Erro ao cadastrar!',
            text: 'Ocorreu um erro ao cadastrar a turma.',
            showConfirmButton: true,
            position: 'center'
        });
    } else if (mensagem === "campos_invalidos") {
        Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: 'Por favor, preencha todos os campos.',
            showConfirmButton: true,
            position: 'center'
        });
    }
</script>

</body>
</html>