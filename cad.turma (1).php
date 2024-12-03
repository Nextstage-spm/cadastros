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
        $cursos_idd = $_POST['cursos']; // Alterado para cursos_idd

        try {
            // Verifica se a turma já está cadastrada
            $query = $conn->prepare("SELECT COUNT(*) FROM turma WHERE nome = :nome AND periodo = :periodo AND cursos_idd = :cursos_idd");
            $query->bindParam(':nome', $nome);
            $query->bindParam(':periodo', $periodo);
            $query->bindParam(':cursos_idd', $cursos_idd); // Alterado para cursos_idd
            $query->execute();
            $count = $query->fetchColumn();

            if ($count > 0) {
                $mensagem = "turma_existente"; // Turma já cadastrada
            } else {
                // Prepara a query para inserir os dados na tabela 'turma'
                $query = $conn->prepare("INSERT INTO turma (nome, periodo, cursos_idd) VALUES (:nome, :periodo, :cursos_idd)");

                // Faz o bind dos parâmetros
                $query->bindParam(':nome', $nome);
                $query->bindParam(':periodo', $periodo);
                $query->bindParam(':cursos_idd', $cursos_idd); // Alterado para cursos_idd

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

        #select1 {
            width: 90%;
            padding: 10px;
            margin-top: 21px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box; /* Para garantir que o padding não aumente a largura total */
            min-height: 20px; /* Altura mínima para todos os campos */
            margin-left: -183px;
            font-size: 10px; 
        }

        #select2 {
            width: 90%;
            padding: 10px;
            margin-top: 21px;
            border: 1px solid #5aa2b0;
            border-radius: 12px;
            box-sizing: border-box; /* Para garantir que o padding não aumente a largura total */
            margin-left: -183px;
            font-size: 10px; 
            height: 35px;
        }

        label[for="nome"], label[for="periodo"], label[for="cursos"] {
            font-weight: bold;
            font-size: 16px;
            color: #000000; /* Exemplo de cor personalizada */
            margin-left: 220px; /* Ajuste para alinhamento */
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
    </style>
</head>
<body>
<header class="header-container">
    <div class="logo">
        <img src="./img/Slide S.P.M. (13).png" alt="Logotipo">
    </div>

    <div class="menu-login-container">
        <nav class="menu" id="menu">
            <a href="./te.php">Início</a>
            <a href="#">Lista</a>
            <a href="#">Cadastros</a>
            <a href="#">Relatórios</a>
            <a href="#">Configuração</a>
        </nav>
    </div>
</header>

<div class="container">
    <h2 class="titulo-principal">Cadastrar turma</h2>
    <form id="cadastroForm" action="" method="post">
        <div>
            <label for="nome">Turma</label>
            <input type="text" id="nome" name="nome" placeholder="Turma" required>
            <div id="nomeError" class="error"></div> <!-- Mensagem de erro -->
        </div>
        <br>
        <label for="periodo">Período</label>
        <select name="periodo" id="select1" required>
            <option value="">Selecione</option>
            <?php
            // Consulta para obter os períodos disponíveis
            $query = $conn->query("SELECT id, nome FROM periodo ORDER BY nome ASC");
            $registros = $query->fetchAll(PDO::FETCH_ASSOC);

            // Exibe as opções de período no select
            foreach ($registros as $option) {
                echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
            }
            ?>
        </select>
        
        <label for="cursos">Cursos</label>
        <select name="cursos" id="select2" required>
            <option value="">Selecione</option>
            <?php
            // Consulta para obter os cursos disponíveis
            $query = $conn->query("SELECT id, nome FROM cursos ORDER BY nome ASC");
            $registros = $query->fetchAll(PDO::FETCH_ASSOC);

                    // Exibe as opções de cursos no select
        foreach ($registros as $option) {
            echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
        }
        ?>
    </select>
    <button type="submit">Cadastrar</button>
</form>

<?php
// Exibe mensagens de erro ou sucesso
if ($mensagem == "turma_existente") {
    echo "<div class='error'>Essa turma já está cadastrada!</div>";
} elseif ($mensagem == "sucesso") {
    echo "<div class='success'>Cadastro realizado com sucesso!</div>";
} elseif ($mensagem == "erro") {
    echo "<div class='error'>Ocorreu um erro ao cadastrar!</div>";
} elseif ($mensagem == "campos_invalidos") {
    echo "<div class='error'>Por favor, preencha todos os campos!</div>";
}
?>
</div> 
</body> 
</html>
