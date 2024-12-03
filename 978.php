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
            background-color: #ffffff;
        }

        hr {
            font-style: normal;
        }


        .nav hr {
            border: 0;
            border-top: 1px solid #5aa2b0;
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
            <a href="#">Cadastros</a>
            <a href="#">Relatórios</a>
        </div>
        <div class="user-menu">
            <button class="user-button"><i class="fas fa-user"></i></button>
            <div class="dropdown-menu">
                <a href="#">Perfil</a>
                <a href="#" class="carregar-conteudo" data-url="3.php">Funcionário</a>
                <a href="#" class="carregar-conteudo" data-url="int.php">Instituição</a>
                <a href="index.php">Sair</a>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3>Menu</h3>
        </div>
        <ul class="nav">
            <li><a href="3.3.php" class=""><i class="fas fa-home" style="margin-right: 15px;"> </i> <span> Início</span></a></li>
            <li><a href="#"><i class="fas fa-clock icon" style="margin-right: 15px;"></i> <span>Lista de Espera</span></a></li>
            <li>
            <hr>
                    <a href="func.php" class="carregar-conteudo" data-url="turma2.php"></i> Turma</a></li>
                    
                    <a href="" class="carregar-conteudo" data-url="curso1.php"></i> Curso</a></li>
                    <a href="prof.php" class="carregar-conteudo" data-url="prof1.php"></i> Professor</a></li>
                    <a href="#" class="carregar-conteudo" data-url="cad.dic.php"></i> Disciplina</a></li>
                    <a href="3.2.php" class="carregar-conteudo" data-url="3.2.php"> Aluno</a></li>
    </li>

    <hr>
                <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event)">
                    <i class="fas fa-chart-bar icon" style="margin-right: 15px;"></i> <span>Relatórios Gerenciais</span>
                </a>
                <div class="dropdown-content">
                <a href="#" class="carregar-conteudo" data-url="relatorio.turma2.php">Turma</a>
                    <a href="#" class="carregar-conteudo" data-url="relatorio.php">Curso</a>
                    <a href="#" class="carregar-conteudo" data-url="relatorio.prof2.php">Professor</a>
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
        <!-- Conteúdo das páginas carregadas dinamicamente -->
    </div>

    <script>
    function showEditModal(curso) {
    document.getElementById("editId").value = curso.id;
    document.getElementById("editNome").value = curso.nome;
    document.getElementById("editDescricao").value = curso.descricao;
    document.getElementById("editModal").style.display = "block";
}

// Função para fechar o modal
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
        document.body.removeChild(novoScript); // Remove o script após a execução
    }

    // Ativa o evento de submit do formulário carregado dinamicamente
    const formulario = document.querySelector('#conteudo form');
    if (formulario) {
        formulario.addEventListener('submit', function(event) { 
            event.preventDefault(); // Evita o envio padrão do formulário
            const formData = new FormData(formulario);

            fetch(formulario.action, {
                method: formulario.method,
                body: formData
            })
            .then(response => response.text())
            .then(responseData => {
                // Atualiza o conteúdo sem recarregar a página
                document.getElementById('conteudo').innerHTML = responseData;
                ativarScriptsDinamicos(); // Ativa scripts no novo conteúdo
            })
            .catch(error => console.log('Erro ao enviar o formulário:', error));
        });
    }
}

// Função para atualizar a tabela sem recarregar a página (após edição ou cadastro)
function atualizarTabela(curso) {
    // Aqui, você pode atualizar a linha da tabela ou adicionar um novo item.
    const tabela = document.getElementById('tabelaCursos');
    const linha = document.createElement('tr');
    linha.innerHTML = `
        <td>${curso.id}</td>
        <td>${curso.nome}</td>
        <td>${curso.descricao}</td>
        <td><button onclick="showEditModal(${JSON.stringify(curso)})">Editar</button></td>
    `;
    
    tabela.appendChild(linha); // Adiciona a nova linha na tabela
}

// Função para salvar/editar curso via AJAX sem fechar o modal
function salvarCurso(event) {
    event.preventDefault();  // Impede o comportamento padrão do formulário

    const form = document.getElementById('formCurso');
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Se a resposta for bem-sucedida, atualiza a tabela
        if (data.success) {
            atualizarTabela(data.curso); // Atualiza a tabela com os dados
            closeModal(); // Fecha o modal após salvar
        } else {
            alert("Erro ao salvar curso.");
        }
    })
    .catch(error => console.log("Erro:", error));
}

// Evento para carregar os scripts ao carregar a página
document.addEventListener("DOMContentLoaded", function() {
    adicionarEventos(); // Adiciona os eventos ao carregar a página
});

// Adicionar eventos para carregamento de conteúdo dinâmico
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

// Função para alternar o dropdown (se necessário)
function toggleDropdown(event) {
    event.preventDefault();
    const dropdown = event.currentTarget.parentNode;
    dropdown.classList.toggle('show-dropdown');
}
</script>

</body>
</html>
