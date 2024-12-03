<?php
include("config.php"); // Inclui a conexão com o banco de dados

$mensagem = "";
$tipo_alerta = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se todos os campos estão preenchidos
    if (!empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['telefone']) && !empty($_POST['data_nascimento'])) {

        // Captura os valores do formulário
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $data_nascimento = $_POST['data_nascimento'];

        if ($senha === $confirma_senha) {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT); // Hash da senha

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
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Aluno</title>
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
    <div class="container">
        <h2 class="titulo-principal">Cadastrar aluno</h2>
        <form id="cadastroForm" action="" method="post">
            <!-- Campos de formulário -->
            <div>
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" placeholder="Nome Completo" required>
            </div>
            <br>
            <div>
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="E-mail" required>
            </div>
            <br>
            <div>
                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone" placeholder="( ) 00000-0000" required>
            </div>
            <div>
                <label for="data_nascimento">Data de Nascimento</label>
                <input type="date" id="data_nascimento" name="data_nascimento" required>
            </div>
            <div>
            <button type="submit">Cadastrar</button>
        </form>
    </div>

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
</body>
</html>
