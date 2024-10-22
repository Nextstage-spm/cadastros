<?php
include("config.php"); // Inclui a conexão com o banco de dados

$message = ''; // Mensagem para exibir ao usuário

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se os campos obrigatórios foram enviados e não estão vazios
    if (isset($_POST['nome'], $_POST['cargo'], $_POST['email'], $_POST['matricula'], 
        $_POST['telefone'], $_POST['data_nascimento'], $_POST['senha'], $_POST['confirma_senha']) && 
        !empty($_POST['nome']) && !empty($_POST['cargo']) && !empty($_POST['email']) && 
        !empty($_POST['matricula']) && !empty($_POST['telefone']) && !empty($_POST['data_nascimento']) && 
        !empty($_POST['senha']) && !empty($_POST['confirma_senha'])) {
        
        // Captura os valores do formulário
        $nome_funcionario = $_POST['nome'];
        $cargo = $_POST['cargo'];
        $email = $_POST['email'];
        $matricula = $_POST['matricula'];
        $telefone = $_POST['telefone'];
        $data_nascimento = $_POST['data_nascimento'];
        $senha = $_POST['senha'];
        $confirma_senha = $_POST['confirma_senha'];

        // Verifica se a senha e a confirmação de senha são iguais
        if ($senha !== $confirma_senha) {
            $message = "As senhas não coincidem.";
        } else {
            try {
                // Prepara a query para inserir os dados na tabela 'funcionarios'
                $query = $conn->prepare("INSERT INTO funcionarios (nome, cargo, email, matricula, telefone, data_nascimento, senha) 
                VALUES (:nome, :cargo, :email, :matricula, :telefone, :data_nascimento, :senha)");

                // Faz o bind dos parâmetros
                $query->bindParam(':nome', $nome_funcionario);
                $query->bindParam(':cargo', $cargo);
                $query->bindParam(':email', $email);
                $query->bindParam(':matricula', $matricula);
                $query->bindParam(':telefone', $telefone);
                $query->bindParam(':data_nascimento', $data_nascimento);
                
                // Criptografa a senha antes de armazená-la
                $hashed_password = password_hash($senha, PASSWORD_DEFAULT);
                $query->bindParam(':senha', $hashed_password);

                // Executa a query e verifica se foi bem-sucedida
                if ($query->execute()) {
                    $message = "Funcionário cadastrado com sucesso!";
                } else {
                    $message = "Erro ao cadastrar o funcionário.";
                }
            } catch (PDOException $e) {
                // Exibe a mensagem de erro com detalhes da exceção (para debug)
                $message = "Erro ao cadastrar funcionário: " . $e->getMessage();
            }
        }
    } else {
        $message = "Por favor, preencha todos os campos.";
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
        input[type="tel"],
        input[type="date"],
        input[type="password"] {
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

        .message {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

<h1>Cadastrar Funcionário</h1>
<!-- Exibe a mensagem, se houver -->
<?php if (!empty($message)): ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

<!-- Formulário para cadastrar funcionário -->
<form method="post" action="">
    <label for="nome">Nome do Funcionário:</label>
    <input type="text" name="nome" id="nome" required>

    <label for="cargo">Cargo:</label>
    <input type="text" name="cargo" id="cargo" required>

    <label for="email">E-mail:</label>
    <input type="text" name="email" id="email" required>

    <label for="matricula">Matrícula:</label>
    <input type="text" name="matricula" id="matricula" required>

    <label for="telefone">Telefone:</label>
    <input type="tel" name="telefone" id="telefone" required>

    <label for="data_nascimento">Data de Nascimento:</label>
    <input type="date" name="data_nascimento" id="data_nascimento" required>

    <label for="senha">Senha:</label>
    <input type="password" name="senha" id="senha" required>

    <label for="confirma_senha">Confirmação de Senha:</label>
    <input type="password" name="confirma_senha" id="confirma_senha" required>

    <!-- Botão de envio -->
    <button type="submit">Cadastrar</button>
</form>

</body>
</html>
