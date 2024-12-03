<?php
include("config.php");

session_start();

$mensagem = "";
$tipo_alerta = "";

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Define as variáveis com segurança
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
    $data_nascimento = filter_input(INPUT_POST, 'data_nascimento', FILTER_SANITIZE_STRING);
    $cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
    $delete_id = filter_input(INPUT_POST, 'delete_id', FILTER_SANITIZE_NUMBER_INT);
    $matricula = filter_input(INPUT_POST, 'matricula', FILTER_SANITIZE_STRING);
    $cargo = filter_input(INPUT_POST, 'cargo', FILTER_SANITIZE_STRING);
    $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);
    $confirma_senha = filter_input(INPUT_POST, 'confirma_senha', FILTER_SANITIZE_STRING);
    
    // Atualização de aluno
    if (isset($id)) {
        try {
            $updateQuery = $conn->prepare("UPDATE aluno_cadastro SET nome = :nome, email = :email, telefone = :telefone, data_nascimento = :data_nascimento, cpf = :cpf WHERE id = :id");
            $updateQuery->bindParam(':nome', $nome);
            $updateQuery->bindParam(':email', $email);
            $updateQuery->bindParam(':telefone', $telefone);
            $updateQuery->bindParam(':data_nascimento', $data_nascimento);
            $updateQuery->bindParam(':cpf', $cpf);
            $updateQuery->bindParam(':id', $id);

            if ($updateQuery->execute()) {
                $mensagem = "Aluno atualizado com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao atualizar o aluno.";
                $tipo_alerta = "error";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao atualizar o aluno: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    } 
    // Cadastro de novo aluno
    elseif (!empty($nome) && !empty($email) && !empty($telefone) && !empty($data_nascimento) && !empty($cpf)) {
        try {
            $query = $conn->prepare("INSERT INTO aluno_cadastro (nome, email, telefone, data_nascimento, cpf) VALUES (:nome, :email, :telefone, :data_nascimento, :cpf)");
            $query->bindParam(':nome', $nome);
            $query->bindParam(':email', $email);
            $query->bindParam(':telefone', $telefone);
            $query->bindParam(':data_nascimento', $data_nascimento);
            $query->bindParam(':cpf', $cpf);

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
    } 
    // Exclusão de aluno
    elseif (!empty($delete_id)) {
        try {
            $deleteQuery = $conn->prepare("DELETE FROM aluno_cadastro WHERE id = :id");
            $deleteQuery->bindParam(':id', $delete_id);
            if ($deleteQuery->execute()) {
                $mensagem = "Aluno excluído com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao excluir o aluno.";
                $tipo_alerta = "error";
            }
        } catch (PDOException $e) {
            $mensagem = "Erro ao excluir o aluno: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    }
}


// Cadastro e atualização de funcionário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        // Atualização do funcionário
        if (!empty($_POST['nome']) && !empty($_POST['email']) && !empty($_POST['telefone']) &&
            !empty($_POST['data_nascimento']) && !empty($_POST['matricula']) && !empty($_POST['cargo'])) {

            $id = $_POST['id'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];
            $data_nascimento = $_POST['data_nascimento'];
            $matricula = $_POST['matricula'];
            $cargo = $_POST['cargo'];

            // Preparando a estrutura da query
            $queryString = "UPDATE funcionarios SET nome=:nome, email=:email, telefone=:telefone, 
                            data_nascimento=:data_nascimento, cargo=:cargo, matricula=:matricula";
            
            if (!empty($_POST['senha']) && $_POST['senha'] === $_POST['confirma_senha']) {
                $senha_hash = password_hash($_POST['senha'], PASSWORD_DEFAULT);
                $queryString .= ", senha=:senha";
            }

            $queryString .= " WHERE id=:id";

            // Prepara a query
            $query = $conn->prepare($queryString);

            // Vincula os parâmetros comuns
            $query->bindParam(':id', $id);
            $query->bindParam(':nome', $nome);
            $query->bindParam(':email', $email);
            $query->bindParam(':telefone', $telefone);
            $query->bindParam(':data_nascimento', $data_nascimento);
            $query->bindParam(':matricula', $matricula);
            $query->bindParam(':cargo', $cargo);

            // Vincula a senha caso não esteja vazia
            if (!empty($_POST['senha']) && $_POST['senha'] === $_POST['confirma_senha']) {
                $query->bindParam(':senha', $senha_hash);
            }

            if ($query->execute()) {
                $mensagem = "Funcionário atualizado com sucesso!";
                $tipo_alerta = "success";
            } else {
                $mensagem = "Erro ao atualizar o funcionário: " . implode(", ", $query->errorInfo());
                $tipo_alerta = "error";
            }
        } else {
            $mensagem = "Por favor, preencha todos os campos.";
            $tipo_alerta = "error";
        }
    } elseif (isset($_POST['delete_id'])) {
        // Exclusão do funcionário
        $delete_id = $_POST['delete_id'];
        $query = $conn->prepare("DELETE FROM funcionarios WHERE id = :delete_id");
        $query->bindParam(':delete_id', $delete_id);

        if ($query->execute()) {
            $mensagem = "Funcionário excluído com sucesso!";
            $tipo_alerta = "success";
        } else {
            $mensagem = "Erro ao excluir o funcionário.";
            $tipo_alerta = "error";
        }
    } else {
        // Cadastro do funcionário
        if (!empty($_POST['nome']) && !empty($_POST['senha']) && !empty($_POST['confirma_senha']) &&
            !empty($_POST['email']) && !empty($_POST['telefone']) && !empty($_POST['data_nascimento']) &&
            !empty($_POST['matricula']) && !empty($_POST['cargo'])) {

            $nome = $_POST['nome'];
            $senha = $_POST['senha'];
            $cargo = $_POST['cargo'];
            $matricula = $_POST['matricula'];
            $confirma_senha = $_POST['confirma_senha'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];
            $data_nascimento = $_POST['data_nascimento'];

            if ($senha === $confirma_senha) {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                try {
                    $query = $conn->prepare("INSERT INTO funcionarios (nome, senha, cargo, matricula, email, telefone, data_nascimento) 
                                             VALUES (:nome, :senha, :cargo, :matricula, :email, :telefone, :data_nascimento)");

                    $query->bindParam(':nome', $nome);
                    $query->bindParam(':senha', $senha_hash);
                    $query->bindParam(':email', $email);
                    $query->bindParam(':telefone', $telefone);
                    $query->bindParam(':data_nascimento', $data_nascimento);
                    $query->bindParam(':matricula', $matricula);
                    $query->bindParam(':cargo', $cargo);

                    if ($query->execute()) {
                        $mensagem = "Cadastro realizado com sucesso!";
                        $tipo_alerta = "success";
                    } else {
                        $mensagem = "Erro ao cadastrar o funcionário: " . implode(", ", $query->errorInfo());
                        $tipo_alerta = "error";
                    }
                } catch (PDOException $e) {
                    $mensagem = "Erro ao cadastrar o funcionário: " . $e->getMessage();
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
}

// Consulta para buscar os registros
$sql = "SELECT id, nome, email, cargo, matricula, telefone, data_nascimento FROM funcionarios";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);



        try {
            // Atualização de funcionário
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['update'])) {
                    // Verifica se todos os campos necessários estão preenchidos
                    if (!empty($_POST['id']) && !empty($_POST['nome']) && !empty($_POST['email']) && 
                        !empty($_POST['telefone']) && !empty($_POST['representante']) && 
                        !empty($_POST['cep']) && !empty($_POST['municipio']) && 
                        !empty($_POST['bairro']) && !empty($_POST['rua'])) {
        
                        // Obtendo dados do POST
                        $id = $_POST['id'];
                        $nome = $_POST['nome'];
                        $email = $_POST['email'];
                        $telefone = $_POST['telefone'];
                        $representante = $_POST['representante'];
                        $cep = $_POST['cep'];
                        $municipio = $_POST['municipio'];
                        $bairro = $_POST['bairro'];
                        $rua = $_POST['rua'];
        
                        // Prepara a query para atualização
                        $query = $conn->prepare("UPDATE instituicao SET 
                                                  nome = :nome,
                                                  email = :email,
                                                  telefone = :telefone,
                                                  representante = :representante,
                                                  cep = :cep,
                                                  municipio = :municipio,
                                                  bairro = :bairro,
                                                  rua = :rua 
                                                  WHERE id = :id");
        
                        // Vincula os parâmetros da query
                        $query->bindParam(':id', $id, PDO::PARAM_INT); // Certificando-se que ID é um inteiro
                        $query->bindParam(':nome', $nome);
                        $query->bindParam(':email', $email);
                        $query->bindParam(':telefone', $telefone);
                        $query->bindParam(':representante', $representante);
                        $query->bindParam(':cep', $cep);
                        $query->bindParam(':municipio', $municipio);
                        $query->bindParam(':bairro', $bairro);
                        $query->bindParam(':rua', $rua);
        
                        // Executa a query e verifica o resultado
                        if ($query->execute()) {
                            $mensagem = "Instituição atualizada com sucesso!";
                            $tipo_alerta = "success";
                        } else {
                            $mensagem = "Erro ao atualizar a Instituição.";
                            $tipo_alerta = "error";
                        }
                    } else {
                        $mensagem = "Por favor, preencha todos os campos.";
                        $tipo_alerta = "error";
                    }
                }
            }
        
            // Consulta para buscar os registros
            $sql = "SELECT id, nome, email, telefone, representante, cep, municipio, bairro, rua FROM instituicao";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        } catch (PDOException $e) {
            $mensagem = "Erro de conexão ao banco de dados: " . $e->getMessage();
            $tipo_alerta = "error";
        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Verifica se o ID do curso a ser excluído foi fornecido
            if (isset($_POST['delete_id'])) {
                // Lógica para exclusão
                $delete_id = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
                if ($delete_id) {
                    $query = $conn->prepare("DELETE FROM cursos WHERE id = :id");
                    $query->bindParam(':id', $delete_id);
        
                    if ($query->execute()) {
                        $mensagem = "Curso excluído com sucesso";
                    } else {
                        $mensagem = "Erro ao excluir o curso";
                    }
                } else {
                    $mensagem = "ID do curso inválido";
                }
            } elseif (isset($_POST['update'])) {
                // Lógica para atualização
                $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
                $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
                
                if ($id && $nome && $descricao) {
                    $query = $conn->prepare("UPDATE cursos SET nome = :nome, descricao = :descricao WHERE id = :id");
                    $query->bindParam(':id', $id);
                    $query->bindParam(':nome', $nome);
                    $query->bindParam(':descricao', $descricao);
        
                    if ($query->execute()) {
                        $mensagem = "Curso atualizado com sucesso";
                    } else {
                        $mensagem = "Erro ao atualizar o curso";
                    }
                } else {
                    $mensagem = "Dados inválidos para atualização";
                }
            } elseif (!empty($_POST['nome']) && !empty($_POST['descricao'])) {
                // Lógica para cadastro
                $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
                $descricao = filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING);
        
                try {
                    $query = $conn->prepare("SELECT COUNT(*) FROM cursos WHERE nome = :nome");
                    $query->bindParam(':nome', $nome);
                    $query->execute();
                    $count = $query->fetchColumn();
        
                    if ($count > 0) {
                        $mensagem = "Curso já cadastrado";
                    } else {
                        $query = $conn->prepare("INSERT INTO cursos (nome, descricao) VALUES (:nome, :descricao)");
                        $query->bindParam(':nome', $nome);
                        $query->bindParam(':descricao', $descricao);
        
                        if ($query->execute()) {
                            $mensagem = "Curso cadastrado com sucesso";
                        } else {
                            $mensagem = "Erro ao cadastrar curso";
                        }
                    }
                } catch (PDOException $e) {
                    $mensagem = "Erro ao cadastrar curso: " . htmlspecialchars($e->getMessage());
                }
            } else {
                $mensagem = "Preencha todos os campos";
            }
        }
        
        // Consulta para buscar os registros
        $sql = "SELECT id, nome, descricao FROM cursos";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Verificar se a titulação existe antes de registrar ou atualizar o professor
            if (isset($_POST['idtitulacao']) && !empty($_POST['idtitulacao'])) {
                $idtitulacao = $_POST['idtitulacao'];
                
                // Verificar se a titulação existe
                $stmt = $conn->prepare("SELECT COUNT(*) FROM titulacao WHERE idtitulacao = :idtitulacao");
                $stmt->bindParam(':idtitulacao', $idtitulacao);
                $stmt->execute();
                $titulacaoCount = $stmt->fetchColumn();
        
                if ($titulacaoCount == 0) {
                    $mensagem = "A titulação selecionada não existe!";
                    $tipo_alerta = "error";
                } else {
                    // Registro do professor
                    if (isset($_POST['nome']) && !empty($_POST['nome']) && isset($_POST['email']) && !empty($_POST['email'])
                    && isset($_POST['telefone']) && !empty($_POST['telefone']) && !isset($_POST['update'])) {
                        
                        // Atribuindo os valores do POST
                        $nome_professor = $_POST['nome'];
                        $email_professor = $_POST['email'];
                        $telefone_professor = $_POST['telefone'];
        
                        try {
                            $query = $conn->prepare("INSERT INTO professor (nome, email, telefone, idtitulacao) VALUES (:nome, :email, :telefone, :idtitulacao)");
                            $query->bindParam(':nome', $nome_professor);
                            $query->bindParam(':email', $email_professor);
                            $query->bindParam(':telefone', $telefone_professor);
                            $query->bindParam(':idtitulacao', $idtitulacao);
        
                            if ($query->execute()) {
                                $mensagem = "Cadastro realizado com sucesso!";
                                $tipo_alerta = "success";
                            } else {
                                $mensagem = "Erro ao cadastrar o professor.";
                                $tipo_alerta = "error";
                            }
                        } catch (PDOException $e) {
                            error_log("Erro ao cadastrar o professor: " . $e->getMessage());
                            $mensagem = "Erro ao cadastrar o professor: " . $e->getMessage();
                            $tipo_alerta = "error";
                        }
                    }
        
                    // Atualização do professor
                    if (isset($_POST['update'])) {
                        $id = $_POST['id'];
                        $nome_professor = $_POST['nome'];
                        $email_professor = $_POST['email'];
                        $telefone_professor = $_POST['telefone'];
        
                        try {
                            $query = $conn->prepare("UPDATE professor SET nome = :nome, email = :email, telefone = :telefone, idtitulacao = :idtitulacao WHERE id = :id");
                            $query->bindParam(':nome', $nome_professor);
                            $query->bindParam(':email', $email_professor);
                            $query->bindParam(':telefone', $telefone_professor);
                            $query->bindParam(':idtitulacao', $idtitulacao);
                            $query->bindParam(':id', $id);
        
                            if ($query->execute()) {
                                $mensagem = "Atualização realizada com sucesso!";
                                $tipo_alerta = "success";
                            } else {
                                $mensagem = "Erro ao atualizar o professor.";
                                $tipo_alerta = "error";
                            }
                        } catch (PDOException $e) {
                            error_log("Erro ao atualizar o professor: " . $e->getMessage());
                            $mensagem = "Erro ao atualizar o professor: " . $e->getMessage();
                            $tipo_alerta = "error";
                        }
                    }
                }
            }
            
            // Deletar professor
            if (isset($_POST['delete_id'])) {
                $id = $_POST['delete_id'];
        
                try {
                    $query = $conn->prepare("DELETE FROM professor WHERE id = :id");
                    $query->bindParam(':id', $id);
        
                    if ($query->execute()) {
                        $mensagem = "Professor excluído com sucesso!";
                        $tipo_alerta = "success";
                    } else {
                        $mensagem = "Erro ao excluir o professor.";
                        $tipo_alerta = "error";
                    }
                } catch (PDOException $e) {
                    error_log("Erro ao excluir o professor: " . $e->getMessage());
                    $mensagem = "Erro ao excluir o professor: " . $e->getMessage();
                    $tipo_alerta = "error";
                }
            }
        }
        
        $sql = "SELECT id, nome, email, telefone, idtitulacao FROM professor";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


        if (isset($_POST['update'])) {
            if (isset($_POST['idturma'], $_POST['nome'], $_POST['idperiodo'], $_POST['idcursos'], $_POST['idturno'])) {
                $idturma = $_POST['idturma'];
                $nome = $_POST['nome'];
                $idperiodo = $_POST['idperiodo']; 
                $idturno = $_POST['idturno'];
                $idcursos = $_POST['idcursos'];  
        
                try {
                    $query = $conn->prepare("UPDATE turma SET nome = :nome, idperiodo = :idperiodo, idcursos = :idcursos, idturno = :idturno WHERE idturma = :idturma");
                    $query->bindParam(':nome', $nome);
                    $query->bindParam(':idperiodo', $idperiodo);  
                    $query->bindParam(':idcursos', $idcursos);  
                    $query->bindParam(':idturno', $idturno);
                    $query->bindParam(':idturma', $idturma);
        
                    if ($query->execute()) {
                        $mensagem = "Turma atualizada com sucesso!";
                    } else {
                        $mensagem = "Erro ao atualizar a turma!";
                    }
                } catch (PDOException $e) {
                    $mensagem = "Erro ao atualizar: " . $e->getMessage(); 
                }
            }
        }
        
        // Excluir turma
        if (isset($_POST['delete_id'])) {
            $idturma = $_POST['delete_id'];
        
            try {
                $query = $conn->prepare("DELETE FROM turma WHERE idturma = :idturma");
                $query->bindParam(':idturma', $idturma);
                
                if ($query->execute()) {
                    $mensagem = "Turma excluída com sucesso!";
                } else {
                    $mensagem = "Erro ao excluir a turma!";
                }
            } catch (PDOException $e) {
                $mensagem = "Erro ao excluir: " . $e->getMessage();
            }
        }
        
        // Buscar dados da turma para edição
        if (isset($_GET['edit_id'])) {
            $idturma = $_GET['edit_id'];
            $editMode = true;
        
            $query = $conn->prepare("SELECT * FROM turma WHERE idturma = :idturma");
            $query->bindParam(':idturma', $idturma);
            $query->execute();
            $turmaData = $query->fetch(PDO::FETCH_ASSOC);
        }
        
        // Cadastrar turma
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['update']) && !isset($_POST['delete_id'])) {
            if (isset($_POST['nome']) && !empty($_POST['nome']) && 
                isset($_POST['idperiodo']) && !empty($_POST['idperiodo']) && 
                isset($_POST['idcursos']) && !empty($_POST['idcursos']) &&
                isset($_POST['idturno']) && !empty($_POST['idturno'])) {
                
                $nome = $_POST['nome'];
                $idperiodo = $_POST['idperiodo']; 
                $idturno = $_POST['idturno'];
                $idcursos = $_POST['idcursos'];  
        
                try {
                    $query = $conn->prepare("INSERT INTO turma (nome, idperiodo, idcursos, idturno) VALUES (:nome, :idperiodo, :idcursos, :idturno)");
                    $query->bindParam(':nome', $nome);
                    $query->bindParam(':idperiodo', $idperiodo);  
                    $query->bindParam(':idcursos', $idcursos);  
                    $query->bindParam(':idturno', $idturno);
        
                    if ($query->execute()) {
                        $mensagem = "Turma cadastrada com sucesso!"; 
                    } else {
                        $mensagem = "Erro ao cadastrar turma"; 
                    }
                } catch (PDOException $e) {
                    $mensagem = "Erro ao cadastrar: " . $e->getMessage(); 
                }
            } else {
                $mensagem = "Campos inválidos!";
            }
        }
        
        // Consulta para exibir registros com nomes
        $sql = "SELECT t.idturma, t.nome, p.nome AS periodo, c.nome AS curso, tu.nome AS turno 
                FROM turma t
                JOIN periodo p ON t.idperiodo = p.id
                JOIN cursos c ON t.idcursos = c.id
                JOIN turno tu ON t.idturno = tu.id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['delete_iddisciplina'])) {
                // Lógica para exclusão
                $delete_id = $_POST['delete_iddisciplina'];
                $query = $conn->prepare("DELETE FROM disciplina WHERE iddisciplina = :iddisciplina");
                $query->bindParam(':iddisciplina', $delete_id);
        
                if ($query->execute()) {
                    $mensagem = "Disciplina excluída com sucesso";
                } else {
                    $mensagem = "Erro ao excluir a disciplina";
                }
            } elseif (isset($_POST['update']) && !empty($_POST['id']) && !empty($_POST['nome']) && !empty($_POST['idprofessor'])) {
                // Lógica para atualização
                $id = $_POST['id'];
                $nome = $_POST['nome'];
                $idprofessor = $_POST['idprofessor'];
        
                $query = $conn->prepare("UPDATE disciplina SET nome = :nome, idprofessor = :idprofessor WHERE iddisciplina = :id");
                $query->bindParam(':id', $id);
                $query->bindParam(':nome', $nome);
                $query->bindParam(':idprofessor', $idprofessor);
        
                if ($query->execute()) {
                    $mensagem = "Disciplina atualizada com sucesso";
                } else {
                    $mensagem = "Erro ao atualizar a disciplina.";
                }
            } elseif (!empty($_POST['nome']) && !empty($_POST['idprofessor'])) {
                // Lógica para cadastro
                $nome = $_POST['nome'];
                $idprofessor = $_POST['idprofessor'];
        
                try {
                    // Verifica se o professor existe
                    $query = $conn->prepare("SELECT COUNT(*) FROM professor WHERE id = :idprofessor");
                    $query->bindParam(':idprofessor', $idprofessor);
                    $query->execute();
                    $professorExists = $query->fetchColumn();
        
                    if ($professorExists > 0) {
                        // Verifica se a disciplina já está cadastrada
                        $query = $conn->prepare("SELECT COUNT(*) FROM disciplina WHERE nome = :nome AND idprofessor = :idprofessor");
                        $query->bindParam(':nome', $nome);
                        $query->bindParam(':idprofessor', $idprofessor);
                        $query->execute();
                        $count = $query->fetchColumn();
        
                        if ($count > 0) {
                            $mensagem = "Disciplina já cadastrada";
                        } else {
                            // Cadastra a nova disciplina
                            $query = $conn->prepare("INSERT INTO disciplina (nome, idprofessor) VALUES (:nome, :idprofessor)");
                            $query->bindParam(':nome', $nome);
                            $query->bindParam(':idprofessor', $idprofessor);
        
                            if ($query->execute()) {
                                $mensagem = "Disciplina cadastrada com sucesso";
                            } else {
                                $mensagem = "Erro ao cadastrar disciplina.";
                            }
                        }
                    } else {
                        $mensagem = "Professor selecionado não encontrado.";
                    }
                } catch (PDOException $e) {
                    $mensagem = "Erro ao cadastrar disciplina: " . $e->getMessage();
                }
            } else {
                $mensagem = "Preencha todos os campos";
            }
        }
        
        // Consulta para buscar os registros com o nome do professor
        $sql = "SELECT disciplina.iddisciplina, disciplina.nome, professor.nome AS professor_nome
                FROM disciplina
                JOIN professor ON disciplina.idprofessor = professor.id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(isset($_POST['update_status'])) {
            $id = $_POST['inscription_id'];
            $status = $_POST['status'];
            
            $sql = "UPDATE aluno_cadastro SET status = :status WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['status' => $status, 'id' => $id]);
        }
        
        // Buscar todas as inscrições com informações do curso
        $sql = "SELECT a.*, c.nome as nome_curso 
                FROM aluno_cadastro a 
                LEFT JOIN cursos c ON a.idcursos = c.id 
                ORDER BY a.data_matricula DESC";
        $stmt = $conn->query($sql);
        $inscricoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Função para buscar detalhes da inscrição via AJAX
        if(isset($_GET['get_details'])) {
            $id = $_GET['id'];
            $sql = "SELECT a.*, c.nome as nome_curso 
                    FROM aluno_cadastro a 
                    LEFT JOIN cursos c ON a.idcursos = c.id 
                    WHERE a.id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['id' => $id]);
            $detalhes = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Buscar documentos do aluno
            $sql_docs = "SELECT * FROM documentos_aluno WHERE id_aluno = :id_aluno";
            $stmt_docs = $conn->prepare($sql_docs);
            $stmt_docs->execute(['id_aluno' => $id]);
            $documentos = $stmt_docs->fetchAll(PDO::FETCH_ASSOC);
            
            $detalhes['documentos'] = $documentos;
            
            echo json_encode($detalhes);
            exit;
        }


        error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função para aprovar via AJAX
if(isset($_POST['aprovar_inscricao'])) {
    header('Content-Type: application/json');
    try {
        $id = $_POST['id'];
        
        $sql = "UPDATE aluno_cadastro SET status = 'aprovado' WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        echo json_encode(['success' => true, 'message' => 'Inscrição aprovada com sucesso!']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao aprovar inscrição: ' . $e->getMessage()]);
    }
    exit;
}

// Buscar todas as inscrições com informações do curso
$sql = "SELECT a.*, c.nome as nome_curso 
        FROM aluno_cadastro a 
        LEFT JOIN cursos c ON a.idcursos = c.id 
        ORDER BY a.data_matricula DESC";
$stmt = $conn->query($sql);
$inscricoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Função para buscar detalhes da inscrição via AJAX
if(isset($_GET['get_details'])) {
    header('Content-Type: application/json');
    
    $id = $_GET['id'];
    error_log("Buscando detalhes para ID: " . $id);
    
    try {
        $sql = "SELECT 
                    a.*,
                    c.nome as nome_curso,
                    t.nome as nome_turno,
                    s.nome as nome_sexo,
                    e.nome as nome_etnia,
                    ec.nome as nome_estado_civil,
                    p.nome as nome_serie
                FROM aluno_cadastro a 
                LEFT JOIN cursos c ON a.idcursos = c.id 
                LEFT JOIN turno t ON a.idturno = t.id
                LEFT JOIN sexo s ON a.idsexo = s.id
                LEFT JOIN etnia e ON a.idetnia = e.id
                LEFT JOIN estado_civil ec ON a.idestado_civil = ec.id
                LEFT JOIN periodo p ON a.idserie = p.id
                WHERE a.id = :id";
                
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        $detalhes = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Adicionar informações das imagens
        $detalhes['imagens'] = [
            'identidade_frente' => $detalhes['identidade_frente'] ?? null,
            'identidade_verso' => $detalhes['identidade_verso'] ?? null,
            'cpf_frente' => $detalhes['cpf_frente'] ?? null,
            'historico' => $detalhes['historico'] ?? null,
            'foto' => $detalhes['foto'] ?? null
        ];
        
        echo json_encode($detalhes);
    } catch (Exception $e) {
        error_log("Erro ao buscar detalhes: " . $e->getMessage());
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Fixa com Carregamento Dinâmico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
         * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open-sans', sans-serif;
            background-color: #e4edfa6c;
        }

        /* Header fixa */
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #ffffff;
            height: 60px;
            border-bottom: 1px solid #ddd;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .logo img {
            max-width: 110px;
            height: 60px;
            margin-top: 3px;
        }

        .menu {
            display: flex;
            gap: 70px;
            margin-left: auto;
        }

        .menu a {
            font-family:  "Open Sans", sans-serif;
            color: rgb(129, 121, 121);
            text-decoration: none;
            font-size: 16px;
        }

        .user-menu {
            position: relative;
        }

        .user-button {
            background-color: #06357b;
            color: #ffffff;
            padding: 8px 15px;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-left: 40px;
            height: 45px;
            width: 45px;
        }

        .user-button i {
            font-size: 19px;
            margin-left: -1px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #ffffff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            min-width: 180px;
            z-index: 1000;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            font-size: 14px;
        }

        .dropdown-menu a:hover {
            background-color: #f4f4f9;
        }

        .user-menu:hover .dropdown-menu {
            display: block;
        }

        /* Sidebar fixa */
        .sidebar {
            position: fixed;
            top: 60px;
            height: calc(100vh - 60px);
            width: 255px;
            background-color: #06357b;
            color: #ffffff;
            overflow-y: auto;
            padding-top: 20px;
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #06357b;
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 1.5em;
            text-align: center;
            background-color: #06357b;
        }

        .nav {
            list-style: none;
            padding: 0;
            margin: 0;
            background-color: #06357b;
        }

        .nav li {
            width: 100%;
            background-color: #06357b;
        }

        .nav a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #ffffff;
            text-decoration: none;
            font-size: 15px;
        }

        .nav a:hover, .nav a.active {
            background: #6da3be3d;
            border-radius: 20px 5px 45px 20px;
        }

        .dropdown {
            display: none;
            background: #06357b;
            padding-left: 20px;
        }

        .dropdown-toggle:hover + .dropdown, .dropdown:hover {
            display: block;
        }

        /* Área de conteúdo */
        #conteudo {
            margin-left: 255px;
            margin-top: 60px;
            padding: 20px;
            width: calc(100% - 255px);
            min-height: calc(100vh - 60px);
            background-color: #e9f2f5;
        }

        hr {
            font-style: normal;
        }


        .nav hr {
            border: 0;
            border-top: 1px solid #5aa2b0;
            align-self: center;
            margin: 10px 0;
        }

        /* Estilo do dropdown */
        .dropdown {
            position: relative;
        }
        .dropdown-toggle {
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        .dropdown-content {
            display: none;
            background-color: #06357b;
            padding-left: 20px;
            margin-top: 5px;
        }
        .dropdown-content a {
            color: #fff;
            padding: 15px 0;
            display: block;
        }
        .dropdown-content a:hover {
            color: #5aa2b0;
        }
        .show-dropdown .dropdown-content {
            display: block;

        }

        h3 menu{
            margin-left: 200px;
        }




        .button-container {
            display: grid;
            grid-template-areas: 
                "btn1 btn2 btn3"
                ". btn4 ."
                "btn5 btn6 btn7";
            grid-gap: 20px;
            max-width: 800px;
            margin: 0 auto;
            margin-top: 120px;
            justify-content: center;
            align-items: center;
            margin-left: 220px;
        }

        /* Estilos individuais dos botões */
        a.button {
            width: 340px;
            height: 95px;
            padding: 20px;
            font-size: 18px;
            font-weight: bold;
            font-family: "Open Sans", sans-serif;
            font-style: normal;
            color: #fff;
            border: none;
            border-radius: 45px;
            cursor: pointer;
            box-shadow: 0px 20px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            display: space-between;
        }

        .button i {
            margin-left: 10px; 
            font-size: 24px;
        }

        .btn-orange-light-top {
            background: linear-gradient(to bottom, #ed9bf4, #5c1465);
            grid-area: btn1;
            margin-top: -50px;
            margin-bottom: 100px;
        }
        
        .btn-purple-light-top {
            background: linear-gradient(to bottom, #a08ef9, #1d0966);
            grid-area: btn2;
            margin-top: -50px;
            margin-bottom: 100px;
        }

        .btn-green-light-top {
            background: linear-gradient(to bottom, #afcc8d, #16581a);
            grid-area: btn3;
            margin-top: -50px;
            margin-bottom: 100px;
        }

        .btn-red-light-top {
            background: linear-gradient(to bottom, #f15d71, #b81701);
            grid-area: btn4;
            margin-top: -50px;
            margin-bottom: 100px;
        }

        .btn-orange-light-bottom {
            background: linear-gradient(to bottom, #adadad, #615d5d);
            grid-area: btn5;
            margin-top: -50px;
            margin-bottom: 100px;
        }
        
        .btn-purple-light-bottom {
            background: linear-gradient(to bottom, #57c8d0, #0a5b4f);
            grid-area: btn6;
            margin-top: -50px;
            margin-bottom: 100px;
        }

        .btn-green-light-bottom {
            background: linear-gradient(to bottom, #fa92d5, #a91a56);
            grid-area: btn7;
            margin-top: -50px;
            margin-bottom: 100px;
        }
        .seta {
            font-size: 15px;
            margin-left: 7px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        /* Transição para a seta para cima */
        .seta.para-cima {
            transform: rotate(180deg);
        }

        .modal-logoff {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    animation: fadeIn 0.3s ease-out;
}

.modal-content-logoff {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border-radius: 5px;
    width: 400px;
    text-align: center;
    position: relative;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.modal-content-logoff h2 {
    color: #174650;
    margin-bottom: 15px;
    font-size: 1.5em;
}

.modal-content-logoff p {
    margin-bottom: 20px;
    color: #666;
}

.modal-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
}

.btn-confirmar, .btn-cancelar {
    padding: 8px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.btn-confirmar {
    background-color: #174650;
    color: white;
}

.btn-confirmar:hover {
    background-color: #5aa2b0;
}

.btn-cancelar {
    background-color: #dc3545;
    color: white;
}

.btn-cancelar:hover {
    background-color: #c82333;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

    </style>
</head>
<body>
<header class="header-container">
        <div class="logo">
            <img src="./img/Slide S.P.M. (13).png" alt="Logotipo">
        </div>
        <div class="menu" id="menu">
            <a href="3.3.php">Início</a>
            <a href="#">Lista</a>
            <a href="#" class="carregar-conteudo" data-url="botao.cad.php">Cadastros</a>
            <a href="#" class="carregar-conteudo" data-url="botao.relatorio.html">Relatórios</a>
        </div>
        <div class="user-menu">
            <button class="user-button"><i class="fas fa-user"></i></button>
            <div class="dropdown-menu">
                <a href="#" class="carregar-conteudo" data-url="editarfuncionario.php">Perfil</a>
                <a href="#" class="carregar-conteudo" data-url="3.php">Funcionário</a>
                <a href="#" class="carregar-conteudo" data-url="int2.php">Instituição</a>
                <a href="javascript:void(0);" onclick="confirmarLogoff()">Sair</a>
            </div>
        </div>
        </header>

 <!-- Modal de Confirmação de Logoff -->
 <div id="logoffModal" class="modal-logoff">
        <div class="modal-content-logoff">
            <h2>Confirmar Saída</h2>
            <p>Tem certeza que deseja sair do sistema?</p>
            <div class="modal-buttons">
                <button onclick="realizarLogoff()" class="btn-confirmar">Confirmar</button>
                <button onclick="fecharModalLogoff()" class="btn-cancelar">Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Menu</h3>
        </div>
        <ul class="nav">
            <li><a href="3.3.php" class=""><i class="fas fa-home" style="margin-right: 15px;"> </i> <span> Início</span></a></li>
            <li><a href="" class="carregar-conteudo" data-url="wait.lt.php"><i class="fas fa-clock icon" style="margin-right: 15px;"></i> <span>Lista de Espera</span></a></li>
            <li>
            <hr>
                    <a href="func.php" class="carregar-conteudo" data-url="turma3.php"></i> Turma</a></li>
                    
                    <a href="" class="carregar-conteudo" data-url="cur.php"></i> Curso</a></li>
                    <a href="prof.php" class="carregar-conteudo" data-url="prof6.php"></i> Professor</a></li>
                    <a href="#" class="carregar-conteudo" data-url="listar_disciplinas.php"></i> Disciplina</a></li>
                    <a href="3.2.php" class="carregar-conteudo" data-url="3.2.php"> Aluno</a></li>
    </li>

    <hr>
                <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event)">
                    <i class="fas fa-chart-bar icon" style="margin-right: 15px;"></i> <span>Relatórios Gerenciais</span>
                    <i class="fas fa-chevron-down seta" id="seta"></i>
                </a>
                <div class="dropdown-content">
                <a href="#" class="carregar-conteudo" data-url="relatorio.turma2.php">Turma</a>
                    <a href="#" class="carregar-conteudo" data-url="relatorio.php">Curso</a>
                    <a href="#" class="carregar-conteudo" data-url="relatorio.prof.php">Professor</a>
                    <a href="#" class="carregar-conteudo" data-url="relatorio.dic.php">Disciplina</a>
                    <a href="#" class="carregar-conteudo" data-url="relatorio.func1.php">Funcionário</a>
                    <a href="#" class="carregar-conteudo" data-url="relatorio.int.php">Instituição</a>
                    <a href="#" class="carregar-conteudo" data-url="relatorio.aluno.php">Aluno</a>
                </div>
            </li>

        </ul>
    </div>


    <!-- Conteúdo -->
    <div id="conteudo">

    <div class="button-container">
    <a href="#" data-url="#" class="button btn-orange-light-top">Lista de Espera<i class="fas fa-clock icon"></i></a>
    <a href="#" data-url="cur.php" class="carregar-conteudo button btn-purple-light-top">Curso<i class="fas fa-book-open"></i></a>
    <a href="#" data-url="prof6.php" class="carregar-conteudo button btn-green-light-top">Professor<i class="fas fa-chalkboard-teacher"></i></a>
    <a href="#" data-url="turma3.php" class="carregar-conteudo button btn-red-light-top">Turma<i class="fas fa-users"></i></a>
    <a href="#" data-url="dic.php" class="carregar-conteudo button btn-orange-light-bottom">Disciplina<i class="fas fa-book"></i></a>
    <a href="#" data-url="3.2.php" class="carregar-conteudo button btn-purple-light-bottom">Aluno<i class="fas fa-user-graduate"></i></a>
    <a href="#" class="button btn-green-light-bottom">Relatórios Gerenciais<i class="fas fa-chart-bar icon"></i></a>
</div>
    
    </div>
    <script>
   document.getElementById('seta').addEventListener('click', function() {
            // Alterna a classe "para-cima", que irá girar a seta
            this.classList.toggle('para-cima');
        });

         function showEditModal(curso) {
            document.getElementById("editId").value = curso.id;
            document.getElementById("editNome").value = curso.nome;
            document.getElementById("editDescricao").value = curso.descricao;
            document.getElementById("editModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("editModal").style.display = "none";
        }

        // Carregar conteúdo dinamicamente com AJAX e manter funcionalidades
        document.querySelectorAll('.carregar-conteudo').forEach(item => {
            item.addEventListener('click', function(event) {
                event.preventDefault();
                const url = this.getAttribute('data-url');

                fetch(url)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById('conteudo').innerHTML = data;
                        ativarScriptsDinamicos();
                    })
                    .catch(error => {
                        document.getElementById('conteudo').innerHTML = '<p>Erro ao carregar o conteúdo.</p>';
                    });
            });
        });

        // Recarregar scripts para funcionalidade do conteúdo carregado
        function ativarScriptsDinamicos() {
            const scripts = document.getElementById('conteudo').getElementsByTagName('script');
            for (let script of scripts) {
                const novoScript = document.createElement('script');
                novoScript.textContent = script.textContent;
                document.body.appendChild(novoScript);
                document.body.removeChild(novoScript);
            }
            
            // Ativa o evento de submit do formulário carregado dinamicamente
            const formulario = document.querySelector('#conteudo form');
            if (formulario) {
                formulario.addEventListener('submit', function(event) { event.preventDefault(); const formData = new FormData(formulario);

                    fetch(formulario.action, {
                    method: formulario.method,
                    body: formData
                })
                .then(response => response.text())
                .then(responseData => {
                    document.getElementById('conteudo').innerHTML = responseData;
                    ativarScriptsDinamicos();
                })
                .catch(error => console.log('Erro ao enviar o formulário:', error));
            });
        }
    }






    document.addEventListener("DOMContentLoaded", function() {
    adicionarEventos(); // Adiciona os eventos ao carregar a página
});

function adicionarEventos() {
    document.querySelectorAll('.carregar-conteudo').forEach(item => {
        item.addEventListener('click', function(event) {
            event.preventDefault();
            const url = this.getAttribute('data-url');

            fetch(url)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('conteudo').innerHTML = data;
                    ativarScriptsDinamicos();
                })
                .catch(error => {
                    document.getElementById('conteudo').innerHTML = '<p>Erro ao carregar o conteúdo.</p>';
                });
        });
    });
}

function ativarScriptsDinamicos() {
    const scripts = document.getElementById('conteudo').getElementsByTagName('script');
    for (let script of scripts) {
        const novoScript = document.createElement('script');
        novoScript.textContent = script.textContent;
        document.body.appendChild(novoScript);
        document.body.removeChild(novoScript); // Remove o script após a execução
    }
}




function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.currentTarget.parentNode;
            dropdown.classList.toggle('show-dropdown');
        }







        function confirmarLogoff() {
            document.getElementById('logoffModal').style.display = 'block';
        }

        // Função para fechar o modal
        function fecharModalLogoff() {
            document.getElementById('logoffModal').style.display = 'none';
        }

        // Função para realizar o logoff
        function realizarLogoff() {
            window.location.href = 'index.php';
        }

        // Controle do menu dropdown
        document.querySelector('.user-button').addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdownMenu = document.querySelector('.dropdown-menu');
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
        });

        // Fechar menu dropdown e modal ao clicar fora
        window.addEventListener('click', function(e) {
            const dropdownMenu = document.querySelector('.dropdown-menu');
            const modal = document.getElementById('logoffModal');
            
            if (!e.target.matches('.user-button') && !e.target.matches('.fa-user')) {
                if (dropdownMenu.style.display === 'block') {
                    dropdownMenu.style.display = 'none';
                }
            }

            if (e.target === modal) {
                fecharModalLogoff();
            }
        })
    </script>

</body>
</html>