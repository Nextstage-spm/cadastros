<?php
include("config.php"); // Inclui a conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se os campos 'nome', 'senha', 'email', etc., foram enviados e não estão vazios
    if (isset($_POST['nome']) && !empty($_POST['nome']) &&
        isset($_POST['senha']) && !empty($_POST['senha']) &&
        isset($_POST['confirma_senha']) && !empty($_POST['confirma_senha']) &&
        isset($_POST['email']) && !empty($_POST['email']) &&
        isset($_POST['telefone']) && !empty($_POST['telefone']) &&
        isset($_POST['data_nascimento']) && !empty($_POST['data_nascimento'])) {
        
        // Captura os valores do formulário
        $nome = $_POST['nome'];
        $senha = $_POST['senha'];
        $confirma_senha = $_POST['confirma_senha'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $data_nascimento = $_POST['data_nascimento'];

        // Verifica se a senha e a confirmação de senha são iguais
        if ($senha === $confirma_senha) {
            // Criptografa a senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            try {
                // Prepara a query para inserir os dados na tabela 'aluno_cadastro'
                $query = $conn->prepare("INSERT INTO aluno_cadastro (nome, senha, email, telefone, data_nascimento) 
                VALUES (:nome, :senha, :email, :telefone, :data_nascimento");

                // Faz o bind dos parâmetros
                $query->bindParam(':nome', $nome);
                $query->bindParam(':senha', $senha_hash); // Armazena a senha criptografada
                $query->bindParam(':email', $email);
                $query->bindParam(':telefone', $telefone);
                $query->bindParam(':data_nascimento', $data_nascimento);

                // Executa a query e verifica se foi bem-sucedida
                if ($query->execute()) {
                    echo "Aluno cadastrado com sucesso!";
                } else {
                    echo "Erro ao cadastrar o aluno.";
                }
            } catch (PDOException $e) {
                // Exibe a mensagem de erro com detalhes da exceção (para debug)
                echo "Erro ao cadastrar o aluno: " . $e->getMessage();
            }
        } else {
            echo "As senhas não coincidem. Tente novamente.";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Aluno</title>
    <style>
        body {
            font-family: "Open Sans", sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
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

        input[type="password"] {
            width: 70%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
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
            border: 1px solid #ddd;
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
            border: 1px solid #ddd;
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
            border: 1px solid #ddd;
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
            border: 1px solid #ddd;
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
            border: 1px solid #ddd;
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

        .full-width {
            grid-column: span 2;
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

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #555;
        }

        @media (max-width: 600px) {
            form {
                grid-template-columns: 1fr; /* Coluna única em telas menores */
            }

            button {
                width: 100%; /* Botão ocupa a largura total */
            }
        }

        .error {
            color: red; /* Cor do texto de erro */
            margin-top: 5px; /* Espaçamento acima */
            font-size: 14px; /* Tamanho da fonte de erro */
        }
       

    </style>
</head>
<body>
    <div class="container">
        <h2 class="titulo-principal">Cadastrar aluno</h2>
        <form id="cadastroForm" action="" method="post">
            <div>
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" placeholder="Nome Completo" required>
                <div id="nomeError" class="error"></div> <!-- Mensagem de erro -->
            </div>
            <br>
            <div>
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="E-mail" required>
                <div id="emailError" class="error"></div> <!-- Mensagem de erro -->
            </div>
            <br>
            <div>
                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone" placeholder="( ) 00000-0000" required>
                <div id="telefoneError" class="error"></div> <!-- Mensagem de erro -->
            </div>
            <div>
                <label for="data_nascimento">Data de Nascimento</label>
                <input type="date" id="data_nascimento" name="data_nascimento" required>
                <div id="dataError" class="error"></div> <!-- Mensagem de erro -->
            </div>
            <div>
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required autocomplete="new-password">
                <div id="senhaError" class="error"></div> <!-- Mensagem de erro -->
            </div>
            <div>
                <label for="confirma_senha">Confirme sua senha</label>
                <input type="password" id="confirma_senha" name="confirma_senha" placeholder="Confirme sua senha" required>
                <div id="confirmaSenhaError" class="error"></div> <!-- Mensagem de erro -->
            </div>
            <button type="submit">Cadastrar</button>
        </form>
    </div>

    <div id="toast" class="toast"></div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        document.getElementById("cadastroForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Impede o envio do formulário

            // Limpa mensagens de erro
            document.querySelectorAll(".error").forEach(function(errorDiv) {
                errorDiv.textContent = ""; // Limpa mensagem de erro
            });

            // Valida os campos
            let hasError = false;

            const nome = document.getElementById("nome").value;
            const email = document.getElementById("email").value;
            const telefone = document.getElementById("telefone").value;
            const dataNascimento = document.getElementById("data_nascimento").value;
            const senha = document.getElementById("senha").value;
            const confirmaSenha = document.getElementById("confirma_senha").value;

            if (nome === "") {
                document.getElementById("nomeError").textContent = "O nome é obrigatório.";
                hasError = true;
            }

            if (email === "") {
                document.getElementById("emailError").textContent = "O e-mail é obrigatório.";
                hasError = true;
            }

            if (telefone === "") {
                document.getElementById("telefoneError").textContent = "O telefone é obrigatório.";
                hasError = true;
            }

            if (dataNascimento === "") {
                document.getElementById("dataError").textContent = "A data de nascimento é obrigatória.";
                hasError = true;
            }

            if (senha === "") {
                document.getElementById("senhaError").textContent = "A senha é obrigatória.";
                hasError = true;
            }

            if (confirmaSenha === "") {
                document.getElementById("confirmaSenhaError").textContent = "A confirmação de senha é obrigatória.";
                hasError = true;
            } else if (senha !== confirmaSenha) {
                document.getElementById("confirmaSenhaError").textContent = "As senhas não coincidem. Tente novamente.";
                hasError = true;
            }

            // Se não houver erros, pode simular o envio
            if (!hasError) {
                Swal.fire({
                    icon: 'success',
                    title: 'Cadastro realizado com sucesso!',
                    showConfirmButton: false,
                    timer: 1500
                });
                document.getElementById("cadastroForm").reset(); // Limpa o formulário se o cadastro for bem-sucedido
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Verifique os campos e tente novamente.',
                });
            }
        });

</script>

</body>
</html>
