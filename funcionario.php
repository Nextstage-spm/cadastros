<?php
include("cadastrar_professor.php"); // Inclui a conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se os campos 'nome' e 'titulacao' foram enviados e não estão vazios
    if (isset($_POST['nome']) && !empty($_POST['nome']) && isset($_POST['cargo']) && !empty($_POST['cargo'])
    && isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['matricula']) && !empty($_POST['matricula']) 
    && isset($_POST['telefone']) && !empty($_POST['telefone']) && isset($_POST['data_nascimento']) && !empty($_POST['data_nascimento'])) {
        
        // Captura os valores do formulário
        $nome_funcionario = $_POST['nome'];
        $cargo = $_POST['cargo'];
        $email = $_POST['email'];
        $matricula = $_POST['matricula'];
        $telefone = $_POST['telefone'];
        $data_nascimento = $_POST['data_nascimento'];

        try {
            // Prepara a query para inserir os dados na tabela 'professor'
            $query = $conn->prepare("INSERT INTO funcionarios (nome, cargo, email, matricula, telefone, data_nascimento) 
            VALUES (:nome, :cargo, :email, :matricula, :telefone, :data_nascimento)");

            // Faz o bind dos parâmetros
            $query->bindParam(':nome', $nome_funcionario);
            $query->bindParam(':cargo', $cargo);
            $query->bindParam(':email', $email);
            $query->bindParam(':matricula', $matricula);
            $query->bindParam(':telefone', $telefone);
            $query->bindParam(':data_nascimento', $data_nascimento);

            // Executa a query e verifica se foi bem-sucedida
            if ($query->execute()) {
                echo "Funcionário cadastrado com sucesso!";
            } else {
                echo "Erro ao cadastrar o funcionário.";
            }
        } catch (PDOException $e) {
            // Exibe a mensagem de erro com detalhes da exceção (para debug)
            echo "Erro ao cadastrar funcoionário: " . $e->getMessage();
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
    <title>Cadastrar Funcionário</title>
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
    <label for="nome">Nome do Funcionário:</label>
    <input type="text" name="nome" id="nome" required>

    <label for="cargo">Cargo:</label>
    <input type="text" name="cargo" id="cargo" required>

    <label for="email">E-mail:</label>
    <input type="text" name="email" id="email" required>

    <label for="matricula">:</label>
    <input type="text" name="matricula" id="matricula" required>

    <label for="telefone">Telefone:</label>
    <input type="tel" name="telefone" id="telefone" required>

    <label for="data_nascimento">Data de Nascimento:</label>
    <input type="date" name="data_nascimento" id="data_nascimento" required>

    <!-- Botão de envio -->
    <button type="submit">Cadastrar</button>
</form>

</body>
</html>