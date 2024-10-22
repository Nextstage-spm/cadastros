<?php
include("cadastrar_professor.php"); // Inclui a conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se os campos 'nome' e 'titulacao' foram enviados e não estão vazios
    if (isset($_POST['nome']) && !empty($_POST['nome']) && isset($_POST['titulacao']) && !empty($_POST['titulacao'])) {
        // Captura os valores do formulário
        $nome_professor = $_POST['nome'];
        $titulacao_id = $_POST['titulacao'];

        try {
            // Prepara a query para inserir os dados na tabela 'professor'
            $query = $conn->prepare("INSERT INTO professor (nome, titulacao_id) VALUES (:nome, :titulacao_id)");

            // Faz o bind dos parâmetros
            $query->bindParam(':nome', $nome_professor);
            $query->bindParam(':titulacao_id', $titulacao_id);

            // Executa a query e verifica se foi bem-sucedida
            if ($query->execute()) {
                echo "Professor cadastrada com sucesso!";
            } else {
                echo "Erro ao cadastrar o professor.";
            }
        } catch (PDOException $e) {
            // Exibe a mensagem de erro com detalhes da exceção (para debug)
            echo "Erro ao cadastrar o professor: " . $e->getMessage();
        }
    } else {
        echo "Por favor, preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Professor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        form {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: auto;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #218838;
        }


        h1 {
            text-align: center;
        }

    </style>
</head>
<body>

<!-- Formulário para cadastrar professor -->
<form method="post" action="">
    <!-- Campo para inserir o nome do professor -->
    <label for="nome">Nome do Professor:</label>
    <input type="text" name="nome" id="nome" required>

    <!-- Campo para selecionar a titulação do professor -->
    <label for="titulacao">Titulação:</label>
    <select name="titulacao" required>
        <option value="">Selecione</option>
        <?php
        // Consulta para obter as titulações disponível
        $query = $conn->query("SELECT id, nome FROM titulacao ORDER BY nome ASC");
        $registros = $query->fetchAll(PDO::FETCH_ASSOC);

        // Exibe as opções de titulação no select
        foreach ($registros as $option) {
            echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
        }
        ?>
    </select>

    <!-- Botão de envio -->
    <button type="submit">Cadastrar</button>
</form>

</body>
</html>