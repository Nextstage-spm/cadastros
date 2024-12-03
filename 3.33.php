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
        

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Fixa com Carregamento Dinâmico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="3.3.css">
    <style>

    #content-frame {
        margin-left: 255px; /* mantém o espaço para a sidebar */
        width: calc(100% - 200px); /* reduz um pouco a largura para dar margem à direita */
        height: calc(100vh - 80px); /* ajusta a altura para dar uma pequena margem */
        border: none;
        background-color: #e9f2f5;
        position: fixed;
        top: 70px; /* aumenta um pouco o espaço do topo */
        display: none;
        padding: 0 30px; /* adiciona padding nas laterais */
        left: 0; /* alinha com a esquerda */
        right: 0; /* alinha com a direita */

    #body { 
        
            max-height: 100vh; /* Limita a altura ao tamanho da tela */
            overflow-y: auto; /* Ativa a rolagem vertical */
            margin: 0;
            font-family: Arial, sans-serif;
}
    

/* Para navegadores baseados em WebKit (Chrome, Safari, etc.) */
body::-webkit-scrollbar {
  display: auto;
}
    }
    
    /* Remove a barra de rolagem no Chrome/Safari/Newer Edge */
    #content-frame::-webkit-scrollbar {
        display: none;
    }
    
    .home-content {
        margin-left: 255px;
        padding: 20px;
        position: relative;
        z-index: 1;
        max-width: 1200px; /* limita a largura máxima */
        margin: 0 auto 0 255px; /* centraliza o conteúdo mantendo espaço para sidebar */
    }

    .hidden {
        display: none !important;
    }

    .visible {
        display: block !important;
    }
    /* Remove todas as barras de rolagem */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body {
  overflow: hidden; /* Impede o scroll em todas as direções */
  height: 100%;
  width: 100%;
}

    /* Adicione estas classes para melhor alinhamento do conteúdo */
    .button-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    #conteudo {
        display: flex;
        justify-content: center;
        width: 100%;
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
            margin-top: 90px;
            justify-content: center;
            align-items: center;
            margin-left: -50px;
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
</style>
</style>
    </style>
</head>
<body>
     
    <header class="header-container">
        <div class="logo">
            <img src="./img/Slide S.P.M. (13).png" alt="Logotipo">
        </div>
        <div class="menu" id="menu">
            <a href="#" onclick="showHome(); return false;">Início</a>
            <a href="wait.lt.php" target="content-frame" onclick="showIframe()">Lista</a>
            <a href="botao.cad.php" target="content-frame" onclick="showIframe()">Cadastros</a>
            <a href="botao.relatorio.php" target="content-frame" onclick="showIframe()">Relatórios</a>
        </div>
        <div class="user-menu">
            <button class="user-button"><i class="fas fa-user"></i></button>
            <div class="dropdown-menu">
                <a href="editarfuncionario.php" target="content-frame" onclick="showIframe()">Perfil</a>
                <a href="3.php" target="content-frame" onclick="showIframe()">Funcionário</a>
                <a href="int2.php" target="content-frame" onclick="showIframe()">Instituição</a>
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
            <li>
                <a href="#" onclick="showHome(); return false;">
                    <i class="fas fa-home" style="margin-right: 15px;"></i><span>Início</span>
                </a>
            </li>
            <li>
            <a href="wait.lt.php" target="content-frame" onclick="showIframe()">
                    <i class="fas fa-clock icon" style="margin-right: 15px;"></i>Lista de Espera 
                </a>
            <li><hr></li>
            <li>
                <a href="turma3.php" target="content-frame" onclick="showIframe()">
                    <i class="fas fa-users" style="margin-right: 15px;"></i>Turma
                </a>
            </li>
            <li>
                <a href="cur.php" target="content-frame" onclick="showIframe()">
                    <i class="fas fa-book-open" style="margin-right: 15px;"></i>Curso
                </a>
            </li>
            <li>
                <a href="prof6.php" target="content-frame" onclick="showIframe()">
                    <i class="fas fa-chalkboard-teacher" style="margin-right: 15px;"></i>Professor
                </a>
            </li>
            <li>
                <a href="dic.php" target="content-frame" onclick="showIframe()">
                    <i class="fas fa-book" style="margin-right: 15px;"></i>Disciplina
                </a>
            </li>
            <li>
                <a href="aluno2.php" target="content-frame" onclick="showIframe()">
                    <i class="fas fa-user-graduate" style="margin-right: 15px;"></i>Aluno
                </a>
            </li>
            <li><hr></li>
            <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event)">
                    <i class="fas fa-chart-bar icon" style="margin-right: 15px;"></i> <span>Relatórios Gerenciais</span>
                    <i class="fas fa-chevron-down seta" id="seta"></i>
                </a>
                <div class="dropdown-content">
                <a href="relatorio.turma2.php"  target="content-frame" onclick="showIframe()">Turma</a>
                    <a href="relatorio.php" target="content-frame" onclick="showIframe()" >Curso</a>
                    <a href="relatorio.prof.php" target="content-frame" onclick="showIframe()">Professor</a>
                    <a href="relatorio.dic.php" target="content-frame" onclick="showIframe()">Disciplina</a>
                    <a href="relatorio.func1.php" target="content-frame" onclick="showIframe()">Funcionário</a>
                    <a href="relatorio.int.php" target="content-frame" onclick="showIframe()">Instituição</a>
                    <a href="relatorio.aluno.php" target="content-frame" onclick="showIframe()" >Aluno</a>
                </div>
        
            </li>
        </ul>
    </div>

    

    <!-- Conteúdo -->
    <div id="conteudo">
        <!-- Conteúdo inicial (página home) -->
        <div id="home-content" class="home-content">
            <div class="button-container">
                <a href="wait.lt.php" target="content-frame" class="button btn-orange-light-top" onclick="showIframe()">
                    Lista de Espera<i class="fas fa-clock icon"></i>
                </a>
                <a href="cur.php" target="content-frame" class="button btn-purple-light-top" onclick="showIframe()">
                    Curso<i class="fas fa-book-open"></i>
                </a>
                <a href="prof6.php" target="content-frame" class="button btn-green-light-top" onclick="showIframe()">
                    Professor<i class="fas fa-chalkboard-teacher"></i>
                </a>
                <a href="turma3.php" target="content-frame" class="button btn-red-light-top" onclick="showIframe()">
                    Turma<i class="fas fa-users"></i>
                </a>
                <a href="dic.php" target="content-frame" class="button btn-orange-light-bottom" onclick="showIframe()">
                    Disciplina<i class="fas fa-book"></i>
                </a>
                <a href="3.2.php" target="content-frame" class="button btn-purple-light-bottom" onclick="showIframe()">
                    Aluno<i class="fas fa-user-graduate"></i>
                </a>
                <a href="#" class="button btn-green-light-bottom">
                    Relatórios Gerenciais<i class="fas fa-chart-bar icon"></i>
                </a>
            </div>
        </div>

        <!-- iframe para conteúdo dinâmico -->
        <iframe id="content-frame" name="content-frame"></iframe>
    </div>

    <!-- Scripts -->
    <script>
        // Funções para controlar a visibilidade do iframe e conteúdo inicial
        function showIframe() {
            document.getElementById('home-content').style.display = 'none';
            document.getElementById('content-frame').style.display = 'block';
        }

        function showHome() {
            document.getElementById('home-content').style.display = 'block';
            document.getElementById('content-frame').style.display = 'none';
        }

        // Funções para o modal de logoff
        function confirmarLogoff() {
            document.getElementById('logoffModal').style.display = 'block';
        }

        function fecharModalLogoff() {
            document.getElementById('logoffModal').style.display = 'none';
        }

        function realizarLogoff() {
            window.location.href = 'index.php';
        }

        // Controle do menu dropdown
        document.querySelector('.user-button').addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdownMenu = document.querySelector('.dropdown-menu');
            dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
        });

        // Fechar menu dropdown ao clicar fora
        window.addEventListener('click', function(e) {
            const dropdownMenu = document.querySelector('.dropdown-menu');
            if (!e.target.matches('.user-button') && !e.target.matches('.fa-user')) {
                if (dropdownMenu.style.display === 'block') {
                    dropdownMenu.style.display = 'none';
                }
            }
        });

        // Toggle para o dropdown na sidebar
        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.currentTarget.parentNode;
            dropdown.classList.toggle('show-dropdown');
        }

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            // Garante que a página inicial seja mostrada ao carregar
            showHome();
        });
    </script>

    
</body>
</html>