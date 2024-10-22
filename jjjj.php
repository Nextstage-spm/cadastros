<?php  
include("config.php"); // Inclui a conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verifica se os campos obrigatórios foram enviados e não estão vazios
    if (isset($_POST['nome'], && !empty($_POST['nome']), $_POST['email'], $_POST['matricula'], 
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
        if ($senha === $confirma_senha) {
            // Criptografa a senha
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

            try {
                // Prepara a query para verificar se o e-mail já existe
                $checkEmail = $conn->prepare("SELECT * FROM funcionarios WHERE email = :email");
                $checkEmail->bindParam(':email', $email);
                $checkEmail->execute();

                if ($checkEmail->rowCount() > 0) {
                    echo json_encode(["status" => "error", "message" => "E-mail já cadastrado."]);
                    exit;
                }

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
                $query->bindParam(':senha', $senha_hash);
                
                // Executa a query e verifica se foi bem-sucedida
                if ($query->execute()) {
                    echo json_encode(["status" => "success", "message" => "Cadastro realizado com sucesso!"]);
                } else {
                    $errorInfo = $query->errorInfo();
                    echo json_encode(["status" => "error", "message" => "Erro ao cadastrar o funcionário: " . implode(", ", $errorInfo)]);
                }
            } catch (PDOException $e) {
                // Exibe a mensagem de erro com detalhes da exceção (para debug)
                echo json_encode(["status" => "error", "message" => "Erro ao cadastrar o funcionário: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "As senhas não coincidem. Tente novamente."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Por favor, preencha todos os campos."]);
    }
}
?>

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

        // Se não houver erros, pode enviar o formulário
        if (!hasError) {
            const formData = new FormData(this);

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    document.getElementById("cadastroForm").reset(); // Limpa o formulário se o cadastro for bem-sucedido
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: data.message,
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Ocorreu um erro ao processar o formulário. Tente novamente.',
                });
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Verifique os campos e tente novamente.',
            });
        }
    });
</script>
