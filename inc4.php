<?php
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

include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        error_log("POST Data: " . print_r($_POST, true));
        
        $conn->beginTransaction();

        // Array com valores padrão apenas para os campos que existem na tabela
        $defaultValues = [
            'nome' => null,
            'email' => null,
            'data_nascimento' => null,
            'telefone' => null,
            'cpf' => null,
            'idserie' => null,
            'idturno' => null,
            'turma' => null,
            'idcursos' => null,
            'idsexo' => null,
            'idetnia' => null,
            'idtipo_vaga' => null,
            'idnecessidades' => null,
            'idestado_civil' => null,
            'idforma_organizacao' => null,
            'idforma_ingresso' => null,
            'idtipo_certidao' => null,
            'unidade_ensino_origem' => null,
            'nome_pai' => null,
            'nome_mae' => null,
            'rg_pai' => null,
            'rg_mae' => null,
            'nome_responsavel' => null,
            'rg_responsavel' => null,
            'cep' => null,
            'endereco' => null,
            'numero' => null,
            'bairro' => null,
            'municipio' => null,
            'uf' => null,
            'orgao_expedidor' => null,
            'uf_identidade' => null,
            'nacionalidade' => null,
            'folha' => null,
            'termo' => null,
            'livro' => null,
            'cidade' => null,
            'circunscricao' => null,
            'data_matricula' => null,
            'data_identidade' => null,
            'complemento' => null,
            'unidade_ensino' => null,
            'status' => null,
            'identidade' => null
        ];

        // Filtrar apenas os campos que existem na tabela
        $dados = array_intersect_key($_POST, $defaultValues);
        $dados = array_merge($defaultValues, $dados);

        // Verificar se o registro já existe usando CPF ou email
        $checkSql = "SELECT id FROM aluno_cadastro WHERE cpf = :cpf OR email = :email LIMIT 1";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([
            ':cpf' => $dados['cpf'],
            ':email' => $dados['email']
        ]);
        $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingRecord) {
            // Se o registro existe, fazer UPDATE
            $updateFields = [];
            foreach ($dados as $key => $value) {
                if (array_key_exists($key, $defaultValues)) {
                    $updateFields[] = "$key = :$key";
                }
            }
            
            $sql = "UPDATE aluno_cadastro SET " . implode(', ', $updateFields) . " 
                   WHERE id = :existing_id";
            
            $stmt = $conn->prepare($sql);
            
            // Adicionar o ID existente aos parâmetros
            $dados['existing_id'] = $existingRecord['id'];
            
            // Bind dos parâmetros
            foreach ($dados as $key => $value) {
                if (strpos($sql, ":$key") !== false) {
                    $stmt->bindValue(":$key", $value ?: null);
                }
            }
            
            $stmt->execute();
            $id = $existingRecord['id'];
        } else {
            // Se o registro não existe, fazer INSERT
            $insertFields = array_keys($dados);
            $values = array_map(function($field) { return ":$field"; }, $insertFields);
            
            $sql = "INSERT INTO aluno_cadastro (" . implode(', ', $insertFields) . ") 
                   VALUES (" . implode(', ', $values) . ")";
            
            $stmt = $conn->prepare($sql);
            
            // Bind dos parâmetros
            foreach ($dados as $key => $value) {
                if (strpos($sql, ":$key") !== false) {
                    $stmt->bindValue(":$key", $value ?: null);
                }
            }
            
            $stmt->execute();
            $id = $conn->lastInsertId();
        }

        // Processar upload de arquivos
        if (!empty($_FILES)) {
            $uploadDir = 'uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $arquivos = ['identidade_frente', 'identidade_verso', 'cpf_frente', 'historico', 'foto'];
            $updateFields = [];
            $updateValues = [':id' => $id];

            foreach ($arquivos as $arquivo) {
                if (isset($_FILES[$arquivo]) && $_FILES[$arquivo]['error'] === UPLOAD_ERR_OK) {
                    $tempName = $_FILES[$arquivo]['tmp_name'];
                    $fileName = uniqid() . '_' . $_FILES[$arquivo]['name'];
                    $destination = $uploadDir . $fileName;

                    if (move_uploaded_file($tempName, $destination)) {
                        $updateFields[] = "$arquivo = :$arquivo";
                        $updateValues[":$arquivo"] = $destination;
                    }
                }
            }

            if (!empty($updateFields)) {
                $updateSql = "UPDATE aluno_cadastro SET " . implode(', ', $updateFields) . " WHERE id = :id";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->execute($updateValues);
            }
        }

        $conn->commit();
        $message = $existingRecord ? 'Registro atualizado com sucesso!' : 'Registro inserido com sucesso!';
        echo json_encode(['status' => 'success', 'message' => $message]);
        
    } catch (Exception $e) {
        $conn->rollBack();
        error_log($e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Erro ao processar registro: ' . $e->getMessage()]);
    }
}

// Função para debug
function debug($data) {
    error_log(print_r($data, true));
}

try {
    $sql = "SELECT nome, email, data_nascimento, telefone, cpf FROM aluno_cadastro WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id' => $_SESSION['id']]);
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$aluno) {
        echo "<script>alert('Erro ao buscar dados do aluno!');</script>";
    }
} catch (PDOException $e) {
    echo "<script>alert('Erro ao conectar com o banco de dados!');</script>";
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Inscrição</title>
    <style>
         body {
            font-family: Arial, sans-serif;
            background-color: #fffefa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }



      

        .container {
            width: 100%;
            max-width: 1200px;
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            display: none;
            border-radius: 8px;
            margin-top: -30px;
           
        }

        .container.active {
            display: block;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .section-title {
            font-weight: bold;
            margin: 25px 0 15px 0;
            color: #444;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 8px;
        }

        fieldset {
            border: 1px solid #e0e0e0;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 13px;
            height: 200px;
        }

        legend {
            color: #666;
            font-weight: 500;
            padding: 0 10px;
        }

        .form-group {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group-half {
            flex: 1;
            min-width: 200px;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-size: 14px;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 80%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
        }

        .form-group input::placeholder {
            color: #999;
        }

        .buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
        }

        .buttons button {
            padding: 12px 24px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .cancel {
            background-color: #f73628;
            color: #fff;
        }

        .clear {
            background-color: #d2d4d2;
        }

        .submit {
            background-color: #38c73d;
            color: #fff;
            width: 100%;
        }

        .buttons button:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .document-group {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
            margin: 20px 0;
        }

        .document {
            width: 150px;
            text-align: center;
        }

        .upload-box {
            background-color: #e7f0f8;
            border: 1px dashed #ccc;
            border-radius: 8px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 8px;
        }

        .upload-box:hover {
            background-color: #d9eaf3;
            border-color: #4a90e2;
        }

        .upload-label {
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
            font-weight: 500;
        }

        /* Responsividade para telas menores */
        @media (max-width: 768px) {
            .form-group-half {
                min-width: 100%;
            }

            .buttons {
                flex-direction: column;
            }

            .buttons button {
                width: 100%;
            }
        }



        #data_matricula {
            border: 1px solid;
            color: #ccc;
            width: 80%;
            margin-top: -8px;
}

#data-matricula:focus {
    box-shadow: 0 0 0 0px rgba(0, 86, 179, 0.2);
}

/* Série/Semestre */
#idserie {
    border: 1px solid;
    color: #ccc;
    width: 80%;
    margin-top: -8px;
}

#serie:focus {
    box-shadow: 0 0 0 2px rgba(133, 100, 4, 0.2);
}

/* Turma */
#turma {
    border: 1px solid;
    color: #ccc;
    width: 80%;
    margin-top: -8px;
}

#turma:focus {
    box-shadow: 0 0 0 2px rgba(21, 87, 36, 0.2);
}

/* Turno */
#idturno {
    border: 1px solid;
    color: #ccc;
    margin-top: -8px;
}

#turno:focus {
    border-color: #383d41;
    box-shadow: 0 0 0 2px rgba(56, 61, 65, 0.2);
}

#idcursos {
    border: 1px solid #d6d8db;
    color: #ccc;
    width: 100%;
    margin-top: -8px;
}

/* Nome do Aluno */
#nome {
    border: 1px solid;
    color: #ccc;
    font-weight: 500;
    width: 60%;
    margin-right: 500px;
    margin-top: -8px;
    margin-bottom: -100px;
}

#nome:focus {
    box-shadow: 0 0 0 2px rgba(0, 64, 133, 0.2);
}

/* Email */
#email {
    border: 1px solid;
    color: #ccc;
    width: 418%;
    margin-left: -240px;
    margin-top: -8px;
    margin-bottom: -100px;
}

#email:focus {
    border-color: #721c24;
    box-shadow: 0 0 0 2px rgba(114, 28, 36, 0.2);
}

/* Data de Nascimento */
#data_nascimento {
    background-color: #f8f9fa;
    border: 2px solid #dae0e5;
    color: #495057;
    margin-left: 110px;
    width: 88%;
    margin-top: -8px;
    margin-bottom: -100px;
}

#data_nascimento:focus {
    border-color: #495057;
    box-shadow: 0 0 0 2px rgba(73, 80, 87, 0.2);
}

/* Sexo */
#idsexo {
    border: 1px solid;
    color: #ccc;
    width: 80%;
    margin-right: 20px;
    margin-left: 55px;
    margin-top: -32px;
    margin-top: -8px;

}

#sexo:focus {
    border-color: #6f42c1;
    box-shadow: 0 0 0 2px rgba(111, 66, 193, 0.2);
}

/* Etnia */
#idetnia {
    border: 1px solid;
    color: #ccc;
    width: 80%;
    margin-left: 40px;
    margin-bottom: -50px;
    margin-top: -32px;
    margin-top: -8px;

}

#etnia:focus {
    border-color: #804d00;
    box-shadow: 0 0 0 2px rgba(128, 77, 0, 0.2);
}

/* Tipo de Vaga */
#idtipo_vaga
 {
    border: 1px solid;
    color: #ccc;
    width: 80%;
    margin-right: 60px;
    margin-bottom: -50px;
    margin-top: -100px;
    margin-top: -8px;

}

#tipo-vaga:focus {
    border-color: #006644;
    box-shadow: 0 0 0 2px rgba(0, 102, 68, 0.2);
}

/* Necessidades Educacionais */
#idnecessidades {
    border: 1px solid;
    color: #ccc;
    width: 120%;
    margin-right: 40px;
    margin-left: -15px;
    margin-bottom: -50px;
    margin-top: -32px;
    margin-top: -8px;
}

#necessidades:focus {
    border-color: #990000;
    box-shadow: 0 0 0 2px rgba(153, 0, 0, 0.2);
}

/* Estado Civil */
#idestado_civil {
    border: 1px solid;
    color: #ccc;
    width: 90%;
    margin-right: 80px;
    margin-left: 55px;
    margin-top: -500px;
    margin-top: -8px;

}

#estado_civil:focus {
    border-color: #006080;
    box-shadow: 0 0 0 2px rgba(0, 96, 128, 0.2);
}

/* Endereço */
#endereco {
    border: 1px solid;
    color: #ccc;
    width: 45%;
    height: 35px;
    margin-left: -270px;
    margin-top: -53px;
}

#endereco:focus {
    border-color: #333333;
    box-shadow: 0 0 0 2px rgba(51, 51, 51, 0.2);
}

/* Bairro */
#bairro {
    border: 1px solid;
    color: #ccc;
    width: 150%;
    margin-top: -7px;
    margin-left: -135px;
    margin-bottom: -100px;
}

#bairro:focus {
    border-color: #804000;
    box-shadow: 0 0 0 2px rgba(128, 64, 0, 0.2);
}

/* Município */
#municipio {
    border: 1px solid;
    color: #ccc;
    width: 130%;
    margin-top: -7px;
    margin-left: -20px; 
    margin-bottom: -100px;
}

#municipio:focus {
    border-color: #006600;
    box-shadow: 0 0 0 2px rgba(0, 102, 0, 0.2);
}

/* CEP */
#cep {
    border: 1px solid;
    color: #ccc;
    width: 20%;
    margin-left: -55px;
    margin-top: 12px;
}

#cep:focus {
    border-color: #4d0099;
    box-shadow: 0 0 0 2px rgba(77, 0, 153, 0.2);
}

/* UF */
#uf {
    border: 1px solid;
    color: #ccc;
    width: 40%;
    margin-top: -7px;
    margin-left: 52px;
}

#uf:focus {
    border-color: #990033;
    box-shadow: 0 0 0 2px rgba(153, 0, 51, 0.2);
}

/* Número */
#numero {
    border: 1px solid;
    color: #ccc;
    width: 35%;
    margin-top: -5px;
    margin-bottom: -100px;
}

#numero:focus {
    border-color: #333333;
    box-shadow: 0 0 0 2px rgba(51, 51, 51, 0.2);
}

/* Complemento */
#complemento {
    border: 1px solid;
    color: #ccc;
    width: 142%;
    height: 35px;
    margin-left: -90px;
    margin-top: -8px;
}

#complemento:focus {
    border-color: #404040;
    box-shadow: 0 0 0 2px rgba(64, 64, 64, 0.2);
}

/* Telefone */
#telefone {
    border: 1px solid;
    color: #ccc;
    width: 24.6%;
    margin-top: -6px;
    margin-left: 870px;
    margin-bottom: -100px;
}

#telefone:focus {
    border-color: #006666;
    box-shadow: 0 0 0 2px rgba(0, 102, 102, 0.2);
}

#unidade_ensino{
    width: 99.5%;
    margin-left: 2px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 13px;
    font-size: 14px;
    transition: all 0.3s ease;
    box-sizing: border-box;
   
}

#nome_pai{
    width: 800px;
    margin-top: -7px;

}

#rg_pai{
    width: 315px;
    margin-left: 250px;
    margin-top: -7px;
}

#nome_mae{
    width: 800px;
    margin-top: -7px;
}

#rg_mae{
    width: 315px;
    margin-left: 250px;
    margin-top: -7px; 
}

#nome_responsavel{
    width: 800px;
    margin-top: -7px;
}

#rg_responsavel{
    width: 315px;
    margin-left: 250px;
    margin-top: -7.7px;
}

#precisa_responsavel {
   margin-top: 45px;
   margin-left: 400px;
}

/* Checkbox hover effect */
#precisa-responsavel:hover {
    border-color: #357abd;
}

/* Checkbox checked state */
#precisa-responsavel:checked {
    background-color: #4a90e2;
    border-color: #4a90e2;
}

/* Checkmark symbol */
#precisa-responsavel:checked::after {
    content: '✓';
    position: absolute;
    color: white;
    font-size: 14px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

#unidade_ensino_origem{
    width: 135%;
    margin-top: -6px;
}

#idforma_ingresso{
    width: 80%;
    margin-left: 140px;
    margin-top: -6px;
}

#idforma_organizacao{
    width: 80%;
    margin-left: 75px;
    margin-top: -6px;
}

#idtipo_certidao{
    width: 30%;
    margin-top: 25px;
    margin-left: -122px;
}

#termo{
    width: 20%;
    margin-left: 380px;
}

#livro{
    width: 30%;
    margin-top: -7px;
    margin-left: 130px;
}


#folha{
    width: 40%;
    margin-top: -7px;
}

#cidade{
    width: 40%;
    margin-left: 340px;
    margin-top: -8px;
}

#circunscricao{
    width: 50%;
    margin-top: -8px;
    margin-left: 390px;
}

#uf_certidao{
    width: 20%;
    margin-top: -20px;
    margin-left: 390px;
}

#uf_identidade{
   width: 61%;
   margin-top: -7px;
   margin-left: 100px;

}

#identidade{
   width: 110%;
   margin-top: -8px;
   
}

#data_identidade{
    width: 80%;
    margin-left: 50px;
    margin-top: -8px;
}

#orgao_expedidor {
    width: 130%;
    margin-top: -7px;

}

#cpf {
    width: 100%;
    margin-top: -7px;
}

#nacionalidade {
    width: 100%;
    margin-top: -7px;
}

input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
    margin-top: -400px;
    accent-color: #2196F3;
}



/* Estilos hover comuns */
input:hover,
select:hover {
    transform: translateY(-1px);
    transition: all 0.3s ease;
}

/* Placeholder personalizado para cada input */
input::placeholder {
    opacity: 0.7;
    font-style: normal;
}

/* Efeito de transição suave para todos os inputs */
input,
select {
    transition: all 0.3s ease;
}

/* Estilo para inputs desabilitados */
input:disabled,
select:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Estilo para inputs obrigatórios */
input:required,
select:required {
    border-left-width: 3px;
}

.label-tipo-vaga {
    border-left: 3px solid #e67e22;
    margin-top: -3px;
}

.label-estado-civil {
    margin-left: 62px;
    margin-top: -3px;
}

.label-etnia {

    margin-left: 50px;
    margin-top: -3px;
}


.label-nascimento{

    margin-left: 118px;
}


.label-nome{

    margin-left: 8px;

}

.label-data-matricula{

    margin-left: 8px;
}

.label-tipo-vaga{

    margin-left: 8px;
}

.label-serie{

    margin-left: 8px;
}

.label-turma{

    margin-left: 8px;
}

.label-turno{

    margin-left: 8px;
}

.label-cursos{

    margin-left: 8px;
}

.label-sexo{
    margin-left: 62px;
    margin-top: -3px;
}

.label-necessidades{

    margin-left: -5px;
    margin-top: -3px;
}

.label-endereco {
 margin-left: 290px;
 margin-top: -70px;
}


.label-cep{
    margin-top: -5px;
    margin-left: 5px;
}

.label-complemento{
    margin-top: -90px;
   margin-left: -80px;
}

.label-numero{
    margin-top: -20px;
    margin-left: 7px;
}

.label-bairro{
    margin-top: -18px;
    margin-left: -127px;
}

.label-municipio{
    margin-top: -18px;
    margin-left: -11px;
}

.label-uf{
    margin-left: 60px;
    margin-top: -16px;
    margin-bottom: 10px;
}

.label-telefone{
    margin-left: 880px;
    margin-top: -95px;
}

.label-nome_pai{
    margin-left: 8px;
    margin-bottom: -8px;
    margin-top: -7px;
}

.label-rg_pai{
    margin-left: 260px;
    margin-top: -5px;
    margin-bottom: 1px;

}

.label-nome_mae{
    margin-left: 8px;
    margin-top: -5px;

}

.label-rg_mae{
    margin-left: 260px;
    margin-top: -5px;
}

.label-rg_responsavel{
    margin-left: 260px;
    margin-top: 1px;
    margin-top: -2px;
}

.label-nome_responsavel{
     margin-top: -2px;
     margin-left: 8px;
     margin-bottom: -70px;
}

.label-forma_ingresso{
    margin-left: 150px;
    margin-top: -7px;
}

.label-unidade_ensino_origem {
    margin-left: 8px;
    margin-top: -7px;
}

.label-forma_organizacao{
    margin-left: 83px;
    margin-top: -7px;
}

.label-tipo_certidao{
    margin-top: 7px;
}

.label-termo{
    margin-top: 20px;
    margin-left: 700px;
}

.label-circunscricao{
    margin-top: -70px;
    margin-left: 400px;
}

.label-folha{
   margin-top: -20px;
   margin-left: 9px;
}

.label-livro{
    margin-top: -70px;
   margin-left: 140px ;
}

.label-cidade{
    margin-top: -90px;
   margin-left: 350px ;

}

.label-data_identidade {
    margin-top: -9px;
    margin-left: 61px;
}

.label-identidade {
    margin-top: -9px;
    margin-left: 10px;
}

.label-orgao_expedidor{
    margin-top: -5px;
    margin-left: 8px;
}

.label-uf_identidade {
    margin-top: -3px;
    margin-left: 107px;
}

.label-cpf {
    margin-top: 25px;
    margin-left: 5px;
}

.label-nacionalidade {
    margin-top: 25px;
    margin-left: 5px;
}

/* Hover effect for all labels */
[class^="label-"]:hover {
    transform: translateX(2px);
    transition: transform 0.2s ease;
}

h3{
    font-style: bold;
    font-size: 17px;
    margin-bottom: -1px;
    margin-left: 15px;
}

.upload-section {
    display: flex;
    flex-direction: row; /* Changed from column to row */
    gap: 20px; /* Increased gap for better spacing */
    justify-content: center; /* Centers the items horizontally */
    flex-wrap: wrap; /* Allows items to wrap on smaller screens */
}

.document-group {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.document {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.upload-label {
  font-weight: bold;
  margin-bottom: 7px;
  text-align: center;
}

.upload-box {
  display: block;
  background-color: #f1f1f1;
  padding: 10px;
  border: 3px dashed #ccc;
  text-align: center;
  cursor: pointer;
}

.upload-box input[type="file"] {
  display: none;
}

.upload-box::before {
  content: "Clique para selecionar";
  font-size: 14px;
  color: #888;
}


.file-input-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    flex: 0 1 auto; /* Allows containers to maintain their size */
}

    .preview-container {
        width: 150px; /* Reduzido o tamanho */
        height: 150px; /* Reduzido o tamanho */
        border: 2px dashed #ccc;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        background-color: #f8f8f8;
        transition: border-color 0.3s, background-color 0.3s;
    }

    .preview-container:hover {
        border-color: #007bff;
        background-color: #f0f7ff;
    }

    .preview-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .upload-icon {
        width: 40px; /* Reduzido o tamanho */
        height: 40px; /* Reduzido o tamanho */
        fill: #666;
    }

    .preview-text {
        color: #666;
        text-align: center;
        font-size: 12px; /* Reduzido o tamanho */
        margin-top: 5px;
    }

    .custom-file-input {
        display: none;
    }

    .custom-file-label {
        display: inline-block;
        padding: 6px 12px; /* Reduzido o padding */
        background-color: #007bff;
        color: white;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
        text-align: center;
        width: 90%;
        font-size: 12px; /* Reduzido o tamanho da fonte */
    }

    .custom-file-label:hover {
        background-color: #0056b3;
    }


    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    .preview-container:hover .upload-icon {
        animation: bounce 1s infinite;
        fill: #007bff;
    }

    /* Adicione media query para telas menores */
    @media (max-width: 1200px) {
        .form-container {
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .form-group {
            flex: 0 1 auto;
        }
    }


    .document-title {
        font-size: 14px;
        color: #333;
        margin-bottom: 10px;
        font-weight: 600;
        text-align: center;
    }

    .preview-container {
        width: 150px;
        height: 150px;
        border: 2px dashed #ccc;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        background-color: #f8f8f8;
        transition: border-color 0.3s, background-color 0.3s;
        margin: 0 auto; /* Centraliza a caixa */
    }

    .upload-icon {
        width: 50px; /* Aumentado um pouco já que agora é só o ícone */
        height: 50px;
        fill: #666;
    }

    .custom-file-label {
        margin-top: 10px;
        padding: 6px 12px;
        font-size: 12px;
    }


    #fieldset_2 {
        height: 110px;
    }

   
    #fieldset_3 {
        height: 215px;
    } 

    #fieldset_4 {
        height: 50px;
    } 

    #fieldset_5 {
        height: 120px;
    } 

    #fieldset_6{
        height: 120px;
        width: 45%;
    } 


    #fieldset_7 {
        height: 120px;
        width: 46%;
        margin-top: -180px;
        margin-left: 604px;

     }
     .header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color: #ededed;
    position: relative;
    max-height: 45px;
    height: auto;
}

.logo img {
    max-width: 105px;
    height: auto;
    margin-top: 7px;
}

.menu-login-container {
    display: flex;
    align-items: center;
    margin-left: auto;
}

.menu {
    margin-top: -7px;
    display: flex;
    flex-direction: row;
    gap: 15px;
    position: static;
    background-color: transparent;
    margin-left: auto;
    font-family: "Open Sans", sans-serif;
}

.menu a {
    text-decoration: none;
    padding: 0 15px;
    display: block;
    color: rgb(129, 121, 121);
    font-size: 17px;
    font-weight: normal;
    transition: 0.3s ease;
    position: relative;
    margin: 0 15px;
    font-family: 'Open-sans', sans-serif;
    margin-top: 11px;
}

.menu-icon {
    font-size: 24px;
    background: none;
    border: none;
    cursor: pointer;
    display: none;
    margin-top: 10px;
    margin-right: 15px;
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
    margin-left: 10px;
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

.modal-logoff {
    display: none;
    position: fixed;
    z-index: 9999;
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
    width: 300px;
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
    margin-top: 20px;
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
/* Ajuste o body para dar espaço para o menu fixo */
body {
    margin: 0;
    padding-top: 65px; /* Altura do header + um pouco de espaço */
}

/* Modifique o header-container */
.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color: #f9f9f9;
    position: fixed; /* Mudança importante aqui */
    top: 0; /* Fixa no topo */
    left: 0; /* Começa da esquerda */
    right: 0; /* Vai até a direita */
    max-height: 90px;
    height: 70px;
    width: 100%;
    z-index: 1000; /* Garante que fique acima de outros elementos */
    box-sizing: border-box;
    box-shadow: 1px 1px 4px rgba(0, 0, 0, 0.2);
}

/* Ajuste o container principal do formulário */
.container {
    margin-top: 20px; /* Dá um espaço adicional abaixo do menu */
    position: relative;
    z-index: 1; /* Menor que o z-index do header */
}

.popup-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    animation: fadeIn 0.3s ease-out;
}

/* Estilo para o conteúdo do pop-up */
.popup-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 90%;
    max-width: 400px;
    text-align: center;
}

/* Estilo para o título do pop-up */
.popup-content h2 {
    margin-top: 0;
    color: #333;
    font-size: 1.5em;
    margin-bottom: 15px;
}

/* Estilo para o texto do pop-up */
.popup-content p {
    color: #666;
    margin-bottom: 20px;
    font-size: 1.1em;
    line-height: 1.4;
}

/* Container para os botões */
.popup-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
}

/* Estilo para os botões */
.popup-buttons button {
    padding: 10px 25px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

/* Botão de confirmar */
.btn-confirmar {
    background-color: #174650;
    color: white;
}

.btn-confirmar:hover {
    background-color: #5aa2b0;
}

/* Botão de cancelar */
.btn-cancelar {
    background-color: #dc3545;
    color: white;
}

.btn-cancelar:hover {
    background-color: #c82333;
}

/* Animação de fade in */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
     


.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    animation: fadeIn 0.3s;
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 0;
    width: 400px;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    animation: slideIn 0.3s;
}

.modal-header {
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px 8px 0 0;
    text-align: center;
}

.modal-header.success {
    background-color: #28a745;
    color: white;
}

.modal-header.success i {
    font-size: 40px;
    margin-bottom: 10px;
}

.modal-body {
    padding: 20px;
    text-align: center;
}

.modal-footer {
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 0 0 8px 8px;
    display: flex;
    justify-content: center;
    gap: 10px;
}

.btn-confirm, .btn-cancel, .btn-success {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s;
}

.btn-confirm {
    background-color: #007bff;
    color: white;
}

.btn-confirm:hover {
    background-color: #0056b3;
}

.btn-cancel {
    background-color: #dc3545;
    color: white;
}

.btn-cancel:hover {
    background-color: #c82333;
}

.btn-success {
    background-color: #28a745;
    color: white;
}

.btn-success:hover {
    background-color: #218838;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from {
        transform: translateY(-100px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Estilos existentes dos botões */
.buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 20px;
}

.buttons button {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s;
}

.buttons .send {
    background-color: #28a745;
    color: white;
}

.buttons .cancel {
    background-color: #dc3545;
    color: white;
}

.instruction {
    margin: 20px 0;
    color: #666;
    font-weight: bold;
}

    </style>
    <script>
        let currentForm = 0;
        
        function showForm(formIndex) {
            const forms = document.querySelectorAll('.container');
            forms.forEach((form, index) => {
                form.classList.toggle('active', index === formIndex);
            });
        }
        
        function nextForm() {
            currentForm++;
            if (currentForm > 3) {
                currentForm = 3; // Limita o índice máximo
            }
            showForm(currentForm);
        }
        
        function previousForm() {
            currentForm--;
            if (currentForm < 0) {
                currentForm = 0; // Limita o índice mínimo
            }
            showForm(currentForm);
        }
        
        window.onload = function() {
            showForm(currentForm); // Exibe o primeiro formulário ao carregar
        };
        function confirmarLogoff() {
    const modal = document.getElementById('logoffModal');
    modal.style.display = 'block';
}

function fecharModalLogoff() {
    const modal = document.getElementById('logoffModal');
    modal.style.display = 'none';
}

function realizarLogoff() {
    window.location.href = 'index.php';
}

window.onclick = function(event) {
    const modal = document.getElementById('logoffModal');
    if (event.target === modal) {
        fecharModalLogoff();
    }
}


    </script>
</head>
<body>
<header class="header-container">
    <div class="logo">
        <img src="./img/Slide S.P.M. (13).png" alt="Logotipo">
    </div>

    <div class="menu-login-container">
        <nav class="menu" id="menu">
            <a href="./logadoaluno.php">Início</a>
            <a href="./inc4.php">Inscrição</a>
            <a href="./conn.php">Consulta</a>
            <a href="./duvidaa.html">Dúvidas</a>
            <a href="./rregras1.html">Regras</a>
            <a href="./sobre1.html">Sobre</a>
        </nav>

        <div class="user-menu">
            <button class="user-button" id="menu-toggle"><i class="fas fa-user"></i></button>
            <div class="dropdown-menu">
                <a href="edit.it.php">Perfil</a>
                <a href="javascript:void(0);" onclick="confirmarLogoff()">Sair</a>
            </div>
        </div>
    </div>
</header>

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


    <div class="container" id="form1">
    <form action="" method="POST" enctype="multipart/form-data">
        <h2><u style="text-decoration-color: orange;">Preencha seus dados abaixo para realizar a inscrição</u></h2>
        <h3><div class="">Unidade de Ensino</div></h3>
        <input type="unidade_ensino" id="unidade_ensino" name="unidade_ensino">
        <fieldset id="fieldset_1">
            <div class="form-group">
                <div class="form-group-half">
                    <label class="label-data-matricula" for="data_matricula">Data da Matrícula</label>
                    <input type="date" id="data_matricula" name="data_matricula">
                </div>
                <div class="form-group-half">
                    <label class="label-serie" for="idserie">Série/Semestre</label>
                    <input type="text" id="idserie" placeholder="Série/Semestre" name="idserie">
                </div>
                <div class="form-group-half">
                    <label class="label-turma" for="turma">Turma</label>
                    <input type="text" id="turma" placeholder="Turma" name="turma">
                </div>
                <div class="form-group-half">
                    <label class="label-turno" for="idturno">Turno</label>
                    <select name="idturno" id="idturno" required>
                    <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM turno ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
                </div>
               <div class="form-group-half">
            <label class="label-cursos" for="idcursos">Curso</label>
            <select name="idcursos" id="idcursos" required>
           <option value="">Selecione</option>
           <?php
           $query = $conn->query("SELECT id, nome FROM cursos ORDER BY nome ASC");
           $registros = $query->fetchAll(PDO::FETCH_ASSOC);
           foreach ($registros as $option) {
               echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
           }
           ?>
       </select>
       </div>
           <!-- Substitua o bloco dos campos nome, email e nascimento por: -->
<div class="form-group">
    <div class="form-group-half">
        <label class="label-nome" for="nome">Nome/ Nome Social do Aluno</label>
        <input type="text" id="nome" name="nome" placeholder="Nome Completo" value="<?php echo htmlspecialchars($aluno['nome'] ?? ''); ?>" readonly>
    </div>
    <div class="form-group-quarter">
        <label class="label-email" for="email">E-Mail do Aluno</label>
        <input type="email" id="email" name="email" placeholder="E-Mail"  value="<?php echo htmlspecialchars($aluno['email'] ?? ''); ?>" readonl>
    </div>
    <div class="form-group-quarter">
        <label class="label-nascimento" for="data_nascimento">Data de Nascimento</label>
        <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($aluno['data_nascimento'] ?? ''); ?>" readonly>
    </div>
</div>

<div class="form-group">
    <div class="form-group-third">
        <label for="tipovaga">Tipo de Vaga</label>
        <select name="idtipo_vaga" id="idtipo_vaga" name="idtipo_vaga"  required>
                    <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM tipo_vaga ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
    </div>
    <div class="form-group-third">
        <label class="label-necessidades" for="idnecessidades">Necessidades Educacionais Especiais</label>
        <select id="idnecessidades" name="idnecessidades" name="idnecessidades" >
                     <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM necessidades_educacionais_especiais ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
    </div>
    <div class="form-group-third">
        <label class="label-estado-civil" for="idestado_civil">Estado Civil</label>
        <select id="idestado_civil" name="idestado_civil">
                     <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM estado_civil ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                     </select>
    </div>
</div>
                <div class="form-group-half">
                    <label class="label-sexo" for="idsexo">Sexo</label>
                    <select name="idsexo" id="idsexo" name="idsexo" required>
                    <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM sexo ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
                </div>
                <div class="form-group-half">
                    <label class="label-etnia" for="idetnia">Etnia</label>
                    <select name="idetnia" id="idetnia" name="idetnia" required>
                     <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM etnia ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
                </div>
        </fieldset>
           <h3><div class="">Endereço</div></h3>
            <fieldset id="fieldset_2">

            <div class="form-group">
                    <label class="label-cep" for="cep">CEP</label>
                    <input type="text" id="cep" name="cep" placeholder="Digite o CEP">
                </div>
            <div class="form-group">
                    <label class="label-endereco" for="endereco">Endereço (Rua/Travessa/Estrada, etc.)</label>           
                <input type="text" id="endereco" name="endereco" placeholder="Preencha Corretamente">
            </div>
            <div class="form-group">
                <div class="form-group-half">
                    <label class="label-numero" for="numero">N°</label>
                    <input type="text" id="numero" name="numero" placeholder="N°">
                </div>
            <div class="form-group">
                <div class="form-group-half">
                    <label class="label-bairro" for="bairro">Bairro</label>
                    <input type="text" id="bairro" name="bairro" placeholder="Bairro">
                </div>
                <div class="form-group-half">
                    <label class="label-municipio" for="municipio">Município</label>
                    <input type="text" id="municipio" name="municipio" placeholder="Município">
                </div>
                <div class="form-group-half">
                    <label class="label-uf" for="uf">UF</label>
                    <input type="text" id="uf" name="uf" placeholder="UF">
                </div>
            </div>
                <div class="form-group-half">
                    <label class="label-complemento" for="complemento">Complemento</label>
                    <input type="text" id="complemento" name="complemento" placeholder="Complemento">
                </div>
                <div class="form-group-half">
                    <label class="label-telefone" for="telefone">Telefone</label>
                    <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" value="<?php echo htmlspecialchars($aluno['telefone'] ?? ''); ?>" readonly>
                </div>
            </div>
        </fieldset>
        <div class="buttons">
            <button onclick="nextForm()">Próximo</button>
            <button class="cancel">Cancelar</button>
            <button class="clear">Limpar</button>
        </div>
    </div>

    <div class="container" id="form2">
        <h2><u style="text-decoration-color: orange;">Preencha seus dados abaixo para realizar a inscrição</u></h2>
        <h3><div class="">Filação</div></h3>
        <fieldset id="fieldset_3">
            <div class="form-group">
                <div class="form-group-half">
                    <label class="label-nome_pai" for="nome_pai">Nome do Pai</label>
                    <input type="text" id="nome_pai" name="nome_pai" placeholder="Digite o nome do pai">
                </div>
                <div class="form-group-half">
                    <label class="label-rg_pai" for="rg_pai">RG do Pai</label>
                    <input type="text" id="rg_pai" name="rg_pai" placeholder="Digite o RG do pai">
                </div>
            </div>

            <div class="form-group">
                <div class="form-group-half">
                    <label class="label-nome_mae" for="nome_mae">Nome da Mãe</label>
                    <input type="text" id="nome_mae" name="nome_mae" placeholder="Digite o nome da mãe">
                </div>
                <div class="form-group-half">
                    <label class="label-rg_mae"for="rg_mae">RG da Mãe</label>
                    <input type="text" id="rg_mae" name="rg_mae" placeholder="Digite o RG da mãe">
                </div>
            </div>


            <div class="responsavel-section" id="responsavel-section">
                <div class="form-group">
                    <div class="form-group-half">
                        <label class="label-nome_responsavel" for="nome_responsavel">Nome do Responsável</label>
                        <input type="text" id="nome_responsavel" name="nome_responsavel" placeholder="Digite o nome do responsável">
                    </div>
                    <div class="form-group-half">
                        <label class="label-rg_responsavel" for="rg_responsavel">RG do Responsável</label>
                        <input type="text" id="rg_responsavel" name="rg_responsavel" placeholder="Digite o RG do responsável">
                    </div>
                </div>
            </div>
        </fieldset>
    </fieldset>

    <h3><div class="">Dados Acadêmicos</div></h3>
    <fieldset id="fieldset_4">
        
        <div class="form-group">
            <div class="form-group-half">
                <label class="label-unidade_ensino_origem" for="unidade_ensino_origem">Unidade de Ensino de Origem</label>
                <input type="text" id="unidade_ensino_origem" name="unidade_ensino_origem" placeholder="Digite a unidade de ensino de origem">
            </div>

            <div class="form-group-half">
            <label class="label-forma_ingresso" for="idforma_ingresso">Forma de Ingresso</label>
            <select id="idforma_ingresso" name="idforma_ingresso">
            <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM forma_ingresso ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
                </div>

            <div class="form-group-half">
                <label class="label-forma_organizacao" for="idforma_organizacao">Forma de Organização</label>
                <select id="idforma_organizacao" name="idforma_organizacao"
    <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM forma_organizacao ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
    </fieldset>
        <div class="buttons">
            <button onclick="previousForm()">Anterior</button>
            <button onclick="nextForm()">Próximo</button>
            <button class="cancel">Cancelar</button>
            <button class="clear">Limpar</button>
        </div>
    </div>

    <div class="container" id="form3">
        <h2><u style="text-decoration-color: orange;">Preencha seus dados abaixo para realizar a inscrição</u></h2>
        <h3><div class="">Documentação</div></h3>
        <fieldset id="fieldset_5">
                <div class="form-group">
                    <label class="label-tipo_certidao" for="idtipo_certidao">Tipo de Certidão</label>
                    <select id="idtipo_certidao" name="idtipo_certidao"> 
                <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM tipo_certidao ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
                </div>
    
                <div class="form-group">
                    <div class="form-group-half">
                        <label class="label-circunscricao" for="circunscricao">Circunscrição</label>
                        <input type="text" id="circunscricao" name="circunscricao" placeholder="Circunscrição">
                    </div>
                    <div class="form-group-half">
                        <label class="label-livro" for="livro">Livro</label>
                        <input type="text" id="livro" name="livro" placeholder="Livro">
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group-half">
                        <label class="label-folha" for="folha">Folha</label>
                        <input type="text" id="folha" name="folha" placeholder="Folha">
                    </div>
                    <div class="form-group-half">
                        <label class="label-cidade" for="cidade">Cidade</label>
                        <input type="text" id="cidade" name="cidade" placeholder="Cidade">
                    </div>
                </div>
            </fieldset>

            <fieldset id="fieldset_6">
                <legend>Identidade</legend>
                <div class="form-group">
                    <div class="form-group-half">
                        <label class="label-identidade" for="identidade">Identidade</label>
                        <input type="text" id="identidade" name="identidade" placeholder="RG do Aluno">
                    </div>
                    <div class="form-group-half">
                        <label class="label-data_identidade" for="data_identidade">Data</label>
                        <input type="date" id="data_identidade" name="data_identidade">
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group-half">
                        <label class="label-orgao_expedidor" for="orgao_expedidor">Órgão Expedidor</label>
                        <input type="text" id="orgao_expedidor" name="orgao_expedidor" placeholder="Órgão Expedidor">
                    </div>
                    <div class="form-group-half">
                        <label class="label-uf_identidade" for="uf_identidade">UF</label>
                        <input type="text" id="uf_identidade" name="uf_identidade" placeholder="UF">
                    </div>
                </div>
            </fieldset>

            <fieldset id="fieldset_7">
                <legend>CPF</legend>
                <div class="form-group">
                    <div class="form-group-half">
                        <label class="label-cpf" for="cpf">CPF</label>
                        <input type="text" id="cpf" name="cpf" placeholder="CPF do Aluno" value="<?php echo htmlspecialchars($aluno['cpf'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-group-half">
                        <label class="label-nacionalidade" for="nacionalidade">Nacionalidade</label>
                        <input type="text" id="nacionalidade" name="nacionalidade" placeholder="Nacionalidade">
                    </div>
                </div>
            </fieldset>
        <div class="buttons">
            <button onclick="previousForm()">Anterior</button>
            <button onclick="nextForm()">Próximo</button>
            <button class="cancel">Cancelar</button>

        </div>
    </div>

    <div class="container" id="form4">
    <div class="upload-section">
        <!-- RG Frente -->
        <div class="document-group">
            <div class="document">
                <div class="upload-label">RG (Frente)</div>
                <div class="file-input-container">
                    <div class="preview-container" id="preview-identidade-frente">
                        <div class="preview-content">
                            <svg class="upload-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 4L12 16M12 4L8 8M12 4L16 8M4 17L4 19C4 19.5304 4.21071 20.0391 4.58579 20.4142C4.96086 20.7893 5.46957 21 6 21L18 21C18.5304 21 19.0391 20.7893 19.4142 20.4142C19.7893 20.0391 20 19.5304 20 19L20 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                    <label for="identidade_frente" class="custom-file-label">Selecionar arquivo</label>
                    <input type="file" name="identidade_frente" id="identidade_frente" class="custom-file-input" required accept="image/jpeg,image/png">
                </div>
            </div>
        </div>

        <!-- RG Verso -->
        <div class="document-group">
            <div class="document">
                <div class="upload-label">RG (Verso)</div>
                <div class="file-input-container">
                    <div class="preview-container" id="preview-identidade-verso">
                        <div class="preview-content">
                            <svg class="upload-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 4L12 16M12 4L8 8M12 4L16 8M4 17L4 19C4 19.5304 4.21071 20.0391 4.58579 20.4142C4.96086 20.7893 5.46957 21 6 21L18 21C18.5304 21 19.0391 20.7893 19.4142 20.4142C19.7893 20.0391 20 19.5304 20 19L20 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                    <label for="identidade_verso" class="custom-file-label">Selecionar arquivo</label>
                    <input type="file" name="identidade_verso" id="identidade_verso" class="custom-file-input" required accept="image/jpeg,image/png">
                </div>
            </div>
        </div>

        <!-- CPF -->
        <div class="document-group">
            <div class="document">
                <div class="upload-label">CPF</div>
                <div class="file-input-container">
                    <div class="preview-container" id="preview-cpf-frente">
                        <div class="preview-content">
                            <svg class="upload-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 4L12 16M12 4L8 8M12 4L16 8M4 17L4 19C4 19.5304 4.21071 20.0391 4.58579 20.4142C4.96086 20.7893 5.46957 21 6 21L18 21C18.5304 21 19.0391 20.7893 19.4142 20.4142C19.7893 20.0391 20 19.5304 20 19L20 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                    <label for="cpf_frente" class="custom-file-label">Selecionar arquivo</label>
                    <input type="file" name="cpf_frente" id="cpf_frente" class="custom-file-input" required accept="image/jpeg,image/png">
                </div>
            </div>
        </div>

        <!-- Histórico -->
        <div class="document-group">
            <div class="document">
                <div class="upload-label">Histórico</div>
                <div class="file-input-container">
                    <div class="preview-container" id="preview-historico">
                        <div class="preview-content">
                            <svg class="upload-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 4L12 16M12 4L8 8M12 4L16 8M4 17L4 19C4 19.5304 4.21071 20.0391 4.58579 20.4142C4.96086 20.7893 5.46957 21 6 21L18 21C18.5304 21 19.0391 20.7893 19.4142 20.4142C19.7893 20.0391 20 19.5304 20 19L20 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                    <label for="historico" class="custom-file-label">Selecionar arquivo</label>
                    <input type="file" name="historico" id="historico" class="custom-file-input" required accept="image/jpeg,image/png">
                </div>
            </div>
        </div>

        <!-- Foto -->
        <div class="document-group">
            <div class="document">
                <div class="upload-label">Foto 3x4</div>
                <div class="file-input-container">
                    <div class="preview-container" id="preview-foto">
                        <div class="preview-content">
                            <svg class="upload-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 4L12 16M12 4L8 8M12 4L16 8M4 17L4 19C4 19.5304 4.21071 20.0391 4.58579 20.4142C4.96086 20.7893 5.46957 21 6 21L18 21C18.5304 21 19.0391 20.7893 19.4142 20.4142C19.7893 20.0391 20 19.5304 20 19L20 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                    </div>
                    <label for="foto" class="custom-file-label">Selecionar arquivo</label>
                    <input type="file" name="foto" id="foto" class="custom-file-input" required accept="image/jpeg,image/png">
                </div>
            </div>
        </div>
    </div>
<br>
<br>
<br>
    <div class="instruction"><center>Certifique-se que todos seus documentos estão corretos.</center></div>
    <div class="buttons">
        <button onclick="previousForm()">Anterior</button>
        <button class="cancel" onclick="mostrarPopupCancelar()">Cancelar</button>
        <button type="submit" class="send" onclick="mostrarPopupConfirmacao(event)">Enviar</button>
    </div>
</div>
  


  <!-- Adicione os modais -->
<div id="confirmacaoModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Confirmação de Envio</h2>
        </div>
        <div class="modal-body">
            <p>Deseja realmente enviar sua inscrição?</p>
        </div>
        <div class="modal-footer">
            <button onclick="confirmarEnvio()" class="btn-confirm">Confirmar</button>
            <button onclick="fecharModal('confirmacaoModal')" class="btn-cancel">Cancelar</button>
        </div>
    </div>
</div>

<div id="sucessoModal" class="modal">
    <div class="modal-content">
        <div class="modal-header success">
            <i class="fas fa-check-circle"></i>
            <h2>Sucesso!</h2>
        </div>
        <div class="modal-body">
            <p>Inscrição enviada com sucesso!</p>
        </div>
        <div class="modal-footer">
            <button onclick="redirecionarParaConsulta()" class="btn-success">Ir para Consulta</button>
        </div>
    </div>
</div>


        <!-- Pop-up de confirmação -->
<div id="popupCancelar" class="popup-overlay">
    <div class="popup-content">
        <h2>Confirmação</h2>
        <p>Tem certeza que deseja cancelar? Todos os dados não salvos serão perdidos.</p>
        <div class="popup-buttons">
            <button onclick="confirmarCancelamento()" class="btn-confirmar">Confirmar</button>
            <button onclick="fecharPopup()" class="btn-cancelar">Voltar</button>
        </div>
    </div>
</div>
        <script>

function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" style="max-width: 100%; max-height: 100%; object-fit: contain;">`;
            }
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = `
                <div class="preview-content">
                    <svg class="upload-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 4L12 16M12 4L8 8M12 4L16 8M4 17L4 19C4 19.5304 4.21071 20.0391 4.58579 20.4142C4.96086 20.7893 5.46957 21 6 21L18 21C18.5304 21 19.0391 20.7893 19.4142 20.4142C19.7893 20.0391 20 19.5304 20 19L20 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>`;
        }
    }

    // Função para criar preview da imagem
    function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Limpa o conteúdo atual
                    preview.innerHTML = '';
                    // Cria e adiciona a imagem
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = `<span>Preview ${input.name}</span>`;
            }
        }

        // Adiciona os listeners para cada input
        document.getElementById('identidade_frente').addEventListener('change', function() {
            previewImage(this, 'preview-identidade-frente');
        });

        document.getElementById('identidade_verso').addEventListener('change', function() {
            previewImage(this, 'preview-identidade-verso');
        });

        document.getElementById('cpf_frente').addEventListener('change', function() {
            previewImage(this, 'preview-cpf-frente');
        });

        document.getElementById('historico').addEventListener('change', function() {
            previewImage(this, 'preview-historico');
        });

        document.getElementById('foto').addEventListener('change', function() {
            previewImage(this, 'preview-foto');
        });




        function confirmarLogoff() {
    const modal = document.getElementById('logoffModal');
    modal.style.display = 'block';
}

function fecharModalLogoff() {
    const modal = document.getElementById('logoffModal');
    modal.style.display = 'none';
}

function realizarLogoff() {
    window.location.href = 'index.php';
}


window.onclick = function(event) {
    const modal = document.getElementById('logoffModal');
    if (event.target === modal) {
        fecharModalLogoff();
    }
}


// Função para mostrar o pop-up
function mostrarPopupCancelar() {
    document.getElementById('popupCancelar').style.display = 'block';
}

// Função para fechar o pop-up
function fecharPopup() {
    document.getElementById('popupCancelar').style.display = 'none';
}

// Função para confirmar o cancelamento
function confirmarCancelamento() {
    // Recarrega a página
    window.location.reload();
    // Ou redireciona para outra página
    // window.location.href = 'sua_pagina.php';
}

// Fecha o pop-up se clicar fora dele
window.onclick = function(event) {
    var popup = document.getElementById('popupCancelar');
    if (event.target == popup) {
        fecharPopup();
    }
}


function mostrarPopupConfirmacao(event) {
    event.preventDefault(); // Previne o envio do formulário
    document.getElementById('confirmacaoModal').style.display = 'block';
}

function confirmarEnvio() {
    // Aqui você pode adicionar o código para enviar o formulário
    document.getElementById('confirmacaoModal').style.display = 'none';
    
    // Simula o envio e mostra o modal de sucesso
    setTimeout(() => {
        document.getElementById('sucessoModal').style.display = 'block';
    }, 500);
}

function fecharModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function redirecionarParaConsulta() {
    window.location.href = 'conn.php';
}

// Fecha os modais quando clicar fora deles
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Se você precisar enviar os dados via AJAX, use esta função
function enviarFormulario() {
    const form = document.querySelector('form');
    const formData = new FormData(form);
    
    fetch('seu_script_de_processamento.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('confirmacaoModal').style.display = 'none';
            document.getElementById('sucessoModal').style.display = 'block';
        } else {
            alert('Erro ao enviar formulário: ' + data.message);
        }
    })
    .catch(error => {
        alert('Erro ao enviar formulário: ' + error);
    });
}

// Funções existentes
function previousForm() {
    // Sua lógica para voltar ao formulário anterior
}

function mostrarPopupCancelar() {
    // Sua lógica para mostrar o popup de cancelamento
}
        </script>