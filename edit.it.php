<?php
include("config.php");
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_SESSION['id'];

// Debug
error_log("ID do usuário na sessão: " . $id);
error_log("Dados da sessão: " . print_r($_SESSION, true));

// Inicializa as variáveis para mensagens
$mensagem = "";
$tipo_alerta = "";

// Atualização do perfil do aluno
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    try {
        if (!empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['telefone']) &&
            !empty($_POST['data_nascimento']) && !empty($_POST['cpf'])) {

            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];
            $data_nascimento = $_POST['data_nascimento'];
            $cpf = $_POST['cpf'];

            $query = $conn->prepare("UPDATE aluno_cadastro SET 
                nome = :nome,
                email = :email,
                telefone = :telefone,
                data_nascimento = :data_nascimento,
                cpf = :cpf
                WHERE id = :id");

            $query->bindParam(':id', $id);
            $query->bindParam(':nome', $nome);
            $query->bindParam(':email', $email);
            $query->bindParam(':telefone', $telefone);
            $query->bindParam(':data_nascimento', $data_nascimento);
            $query->bindParam(':cpf', $cpf);

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
    } catch (PDOException $e) {
        $mensagem = "Erro ao atualizar: " . $e->getMessage();
        $tipo_alerta = "error";
        error_log("Erro na atualização: " . $e->getMessage());
    }
}

// Consulta para buscar os dados do aluno
try {
    $sql = "SELECT id, nome, email, telefone, data_nascimento, cpf FROM aluno_cadastro WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    error_log("Dados do aluno: " . print_r($aluno, true));

    if (!$aluno) {
        $mensagem = "Aluno não encontrado.";
        $tipo_alerta = "error";
        error_log("Aluno não encontrado para o ID: " . $id);
    }
} catch (PDOException $e) {
    $mensagem = "Erro ao buscar dados: " . $e->getMessage();
    $tipo_alerta = "error";
    error_log("Erro na consulta: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        header {
            background-color: #f9f9f9;
            padding: 0px 0;
        }
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: right;
            width: 99%;
            margin: 0px 0px;
        }
        header {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
            margin-bottom: 3px;
            align-items: center;
            padding: 0px;
            height: 55px;
            background-color: #f9f9f9;
        }
        .logo img {
            position: fixed;
            z-index: 10;
            height: 55px;
            top: 4px;
            left: 10px;
            margin-top: 0;
        }

        .logo img {
    max-width: 105px;
    height: auto;
    margin-top: 7px;
}
        .menu {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #f9f9f9;
            padding: 18px 0;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1;
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
        .menu a:hover::after {
            width: 100%;
        }
        .menu a.inicio::after { background-color: #011772; }
        .menu a.inscricao::after { background-color: #32cd32; }
        .menu a.regras::after { background-color: #9400d3; }
        .menu a.ajuda::after { background-color: #ff4500; }
        .menu a.sobre::after { background-color: #ffa500; }

        header img {
            height: 40px;
        }

        body {
            font-family: "Open Sans", sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 90px auto 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.1);
        }

        h2 {
            background-color: #5aa2b0;
            color: white;
            text-align: center;
            padding: 15px 0;
            font-size: 24px;
            font-weight: bold;
            margin: -20px -20px 20px;
            border-radius: 8px 8px 0 0;
        }

        form {
            display: grid;
            gap: 20px;
            padding: 20px;
        }

        label {
            font-weight: bold;
            color: #174650;
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
            box-sizing: border-box;
        }

        button {
            padding: 12px;
            background-color: #174650;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 30%;
            margin-right: -95px;
        }

        button:hover {
            background-color: #5aa2b0;
        }

        .alert {
            padding: 15px;
            margin: 20px 0;
            text-align: center;
            border-radius: 8px;
            font-weight: bold;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .action-buttons {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.btn-voltar {
    display: inline-block;
    padding: 10px 50px;
    background-color: #b30000; /* Vermelho claro */
    border: none;
    border-radius: 8px;
    text-decoration: none;
    color: #ffff;
    transition: background-color 0.3s ease;
    font-size: 16px;
    margin-right: 180px;
    margin-left: -8px;
    align-items: center;
    text-align: center;
    justify-content: center;
}

.btn-voltar:hover {
    background-color: #ff4d4d;
}
    </style>
</head>
<body>
    <div class="header-container">
        <div class="logo">
            <img src="./img/Slide S.P.M. (13).png" alt="Logotipo" height="100" width="125">
        </div>
        <nav class="menu">
            <a href="./logadoaluno.php">Início</a>
            <a href="./inc1.php">Inscrição</a>
            <a href="#" id="consulta">Consulta</a>
            <a href="./dduvida1.html">Dúvidas</a>
            <a href="./rregras1.html">Regras</a>
            <a href="./sobre1.html">Sobre</a>
        </nav>
    </div>

    <div class="container">
        <h2>Editar Perfil</h2>
        
        <?php if (!empty($mensagem)): ?>
            <div class="alert <?php echo $tipo_alerta; ?>"><?php echo $mensagem; ?></div>
        <?php endif; ?>

        <?php if ($aluno): ?>
            <form method="POST" action="">
                <input type="hidden" name="update" value="1">
                <input type="hidden" name="id" value="<?php echo $aluno['id']; ?>">
                
                <div>
                    <label for="nome">Nome:</label>
                    <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($aluno['nome']); ?>" disabled>
                </div>
                
                <div>
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($aluno['email']); ?>"disabled>
                </div>
                
                <div>
                    <label for="telefone">Telefone:</label>
                    <input type="tel" name="telefone" id="telefone" value="<?php echo htmlspecialchars($aluno['telefone']); ?>" required>
                </div>
                
                <div>
                    <label for="cpf">CPF:</label>
                    <input type="text" name="cpf" id="cpf" value="<?php echo htmlspecialchars($aluno['cpf']); ?>" required>
                </div>
                
                <div>
                    <label for="data_nascimento">Data de Nascimento:</label>
                    <input type="date" name="data_nascimento" id="data_nascimento" value="<?php echo htmlspecialchars($aluno['data_nascimento']); ?>" disabled>
                </div>

                <div class="action-buttons">
                <a href="./logadoaluno.php" class="btn-voltar">Voltar</a>
                <button type="submit">Atualizar Perfil</button>
            </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        // Máscara para CPF
        document.getElementById('cpf').addEventListener('input', function (e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,3})(\d{0,2})/);
            e.target.value = !x[2] ? x[1] : x[1] + '.' + x[2] + (x[3] ? '.' + x[3] : '') + (x[4] ? '-' + x[4] : '');
        });

        // Máscara para telefone
        document.getElementById('telefone').addEventListener('input', function (e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    </script>
</body>
</html>