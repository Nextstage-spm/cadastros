<?php
include("config.php"); // Inclui a conexão com o banco de dados

session_start();

if (isset($_SESSION['user_id'])) {
    // Access the session variable
    $user_id = $_SESSION['user_id'];
} else {
    echo "Session variable is not set.";
}




// Atualização de perfil de funcionário
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    if (!empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['telefone']) &&
        !empty($_POST['data_nascimento']) && !empty($_POST['matricula']) && !empty($_POST['cargo'])) {

        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $data_nascimento = $_POST['data_nascimento'];
        $matricula = $_POST['matricula'];
        $cargo = $_POST['cargo'];

        if (!empty($_POST['senha']) && $_POST['senha'] === $_POST['confirma_senha']) {
            $senha_hash = password_hash($_POST['senha'], PASSWORD_DEFAULT);
            $query = $conn->prepare("UPDATE funcionarios SET nome=:nome, email=:email, telefone=:telefone, data_nascimento=:data_nascimento, cargo=:cargo, matricula=:matricula, senha=:senha WHERE id=:id");
            $query->bindParam(':senha', $senha_hash);
        } else {
            $query = $conn->prepare("UPDATE funcionarios SET nome=:nome, email=:email, telefone=:telefone, data_nascimento=:data_nascimento, cargo=:cargo, matricula=:matricula WHERE id=:id");
        }

        // Vincula os parâmetros da query
        $query->bindParam(':id', $id);
        $query->bindParam(':nome', $nome);
        $query->bindParam(':email', $email);
        $query->bindParam(':telefone', $telefone);
        $query->bindParam(':data_nascimento', $data_nascimento);
        $query->bindParam(':matricula', $matricula);
        $query->bindParam(':cargo', $cargo);

        if ($query->execute()) {
            $mensagem = "Perfil atualizado com sucesso!";
            $tipo_alerta = "success";
        } else {
            $mensagem = "Erro ao atualizar o perfil.";
            $tipo_alerta = "error";
        }
    } else {
        $mensagem = "Por favor, preencha todos os campos.";
        $tipo_alerta = "error";
    }
}

// Consulta para buscar o perfil do funcionário
$id = $_SESSION['id']; // Considerando que o id do funcionário está salvo na sessão
$sql = "SELECT id, nome, email, cargo, matricula, telefone, data_nascimento FROM funcionarios WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->execute();
$funcionario = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil do Funcionário</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>

body {
            font-family: "Open Sans", sans-serif;
            background-color: #f4f4f9;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
            margin-top: -100px;
        }

        h2 {
            background-color: #5aa2b0;
            color: white;
            text-align: center;
            padding: 15px 0;
            font-size: 24px;
            font-weight: bold;
        }

        form {
            display: grid;
            gap: 20px;
            padding: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #5aa2b0;
            border-radius: 8px;
            font-size: 14px;
        }

        button {
            padding: 10px;
            background-color: #174650;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #5aa2b0;
        }

        .alert {
            padding: 10px;
            margin-top: 10px;
            text-align: center;
            border-radius: 8px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        body {
            font-family: "Open Sans", sans-serif;
            background-color: #f4f4f9;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            background-color: #5aa2b0;
            color: white;
            text-align: center;
            padding: 15px 0;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            padding: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="date"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #5aa2b0;
            border-radius: 8px;
            box-sizing: border-box;
        }

        button {
            padding: 12px;
            background-color: #174650;
            color: white;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
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
    <div class="container">
        <h2>Editar Perfil</h2>

        <?php if (!empty($mensagem)): ?>
            <div class="alert <?php echo $tipo_alerta; ?>"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo $funcionario['id']; ?>">

            <div><label for="nome">Nome</label><input type="text" name="nome" id="nome" value="<?php echo $funcionario['nome']; ?>" required></div>
            <div><label for="email">Email</label><input type="email" name="email" id="email" value="<?php echo $funcionario['email']; ?>" required></div>
            <div><label for="telefone">Telefone</label><input type="tel" name="telefone" id="telefone" value="<?php echo $funcionario['telefone']; ?>" required></div>
            <div><label for="data_nascimento">Data de Nascimento</label><input type="date" name="data_nascimento" id="data_nascimento" value="<?php echo $funcionario['data_nascimento']; ?>" required></div>
            <div><label for="cargo">Cargo</label><input type="text" name="cargo" id="cargo" value="<?php echo $funcionario['cargo']; ?>" required></div>
            <div><label for="matricula">Matrícula</label><input type="text" name="matricula" id="matricula" value="<?php echo $funcionario['matricula']; ?>" required></div>
            <div><label for="senha">Senha (Deixe em branco para não alterar)</label><input type="password" name="senha" id="senha"></div>
            <div><label for="confirma_senha">Confirme a Senha</label><input type="password" name="confirma_senha" id="confirma_senha"></div>
            <button type="submit" name="update">Atualizar</button>
        </form>
    </div>
</body>
</html>