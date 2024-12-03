<?php
include("config.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Primeiro, vamos processar os uploads de arquivos
        $pasta = "uploads/documentos/";
        if (!file_exists($pasta)) {
            mkdir($pasta, 0777, true);
        }

        // Função para salvar arquivo e retornar o caminho
        function salvarArquivo($arquivo, $pasta) {
            if (!empty($_FILES[$arquivo]['tmp_name']) && is_uploaded_file($_FILES[$arquivo]['tmp_name'])) {
                $nome_arquivo = uniqid() . '-' . basename($_FILES[$arquivo]['name']);
                $caminho_final = $pasta . $nome_arquivo;
                if (move_uploaded_file($_FILES[$arquivo]['tmp_name'], $caminho_final)) {
                    return $caminho_final;
                }
            }
            return null;
        }

        // Salvando os arquivos
        $identidade_frente_path = salvarArquivo('identidade_frente', $pasta);
        $identidade_verso_path = salvarArquivo('identidade_verso', $pasta);
        $cpf_frente_path = salvarArquivo('cpf_frente', $pasta);
        $historico_path = salvarArquivo('historico', $pasta);
        $foto_path = salvarArquivo('foto', $pasta);

        // Preparando a query de inserção
        $sql = "INSERT INTO aluno_cadastro (
            unidade_ensino, data_matricula, idserie_semestre, turma, idturno, 
            idcurso, idtipo_vaga, nome, email, data_nascimento, idsexo, 
            idetnia, idnecessidades_educacionais, idestado_civil,
            cep, logradouro, complemento, numero, bairro, municipio, uf,
            telefone, celular, nome_pai, rg_pai, nome_mae, rg_mae,
            nome_responsavel, rg_responsavel, responsavel_menor,
            unidade_ensino_origem, idforma_ingresso, idforma_organizacao,
            ideducacao, idtipo_certidao, termo, circunscricao, livro,
            folha, cidade_certidao, uf_certidao, identidade, data_identidade,
            orgao_expedidor, uf_identidade, cpf, nacionalidade,
            identidade_frente_path, identidade_verso_path, cpf_frente_path,
            historico_path, foto_path
        ) VALUES (
            :unidade_ensino, :data_matricula, :serie_semestre, :turma, :turno_id,
            :curso_id, :tipo_vaga_id, :nome, :email, :data_nascimento, :sexo_id,
            :etnia_id, :necessidades_educacionais_especiais_id, :estado_civil_id,
            :cep, :logradouro, :complemento, :numero, :bairro, :municipio, :uf,
            :telefone, :celular, :nome_pai, :rg_pai, :nome_mae, :rg_mae,
            :nome_responsavel, :rg_responsavel, :responsavel_menor,
            :unidade_ensino_origem, :forma_ingresso_id, :forma_organizacao_id,
            :educacao_id, :tipo_certidao_id, :termo, :circunscricao, :livro,
            :folha, :cidade_certidao, :uf_certidao, :identidade, :data_identidade,
            :orgao_expedidor, :uf_identidade, :cpf, :nacionalidade,
            :identidade_frente_path, :identidade_verso_path, :cpf_frente_path,
            :historico_path, :foto_path
        )";

        $stmt = $conn->prepare($sql);

        // Binding dos parâmetros
        $stmt->bindParam(':unidade_ensino', $_POST['unidade-de-ensino']);
        $stmt->bindParam(':data_matricula', $_POST['data-matricula']);
        $stmt->bindParam(':serie_semestre', $_POST['serie-semestre']);
        $stmt->bindParam(':turma', $_POST['turma']);
        $stmt->bindParam(':turno_id', $_POST['turno']);
        $stmt->bindParam(':curso_id', $_POST['cursos']);
        $stmt->bindParam(':tipo_vaga_id', $_POST['tipo_vaga']);
        $stmt->bindParam(':nome', $_POST['nome']);
        $stmt->bindParam(':email', $_POST['email']);
        $stmt->bindParam(':data_nascimento', $_POST['nascimento']);
        $stmt->bindParam(':sexo_id', $_POST['sexo']);
        $stmt->bindParam(':etnia_id', $_POST['etnia']);
        $stmt->bindParam(':necessidades_educacionais_especiais_id', $_POST['necessidades_educacionais_especiais']);
        $stmt->bindParam(':estado_civil_id', $_POST['estado_civil']);
        $stmt->bindParam(':cep', $_POST['cep']);
        $stmt->bindParam(':logradouro', $_POST['logradouro']);
        $stmt->bindParam(':complemento', $_POST['complemento']);
        $stmt->bindParam(':numero', $_POST['numero']);
        $stmt->bindParam(':bairro', $_POST['bairro']);
        $stmt->bindParam(':municipio', $_POST['municipio']);
        $stmt->bindParam(':uf', $_POST['uf']);
        $stmt->bindParam(':telefone', $_POST['telefone']);
        $stmt->bindParam(':celular', $_POST['celular']);
        $stmt->bindParam(':nome_pai', $_POST['nome_do_pai']);
        $stmt->bindParam(':rg_pai', $_POST['rg_do_pai']);
        $stmt->bindParam(':nome_mae', $_POST['nome_da_mae']);
        $stmt->bindParam(':rg_mae', $_POST['rg_da_mae']);
        $stmt->bindParam(':nome_responsavel', $_POST['nome_do_responsavel']);
        $stmt->bindParam(':rg_responsavel', $_POST['rg_do_responsavel']);
        $stmt->bindParam(':responsavel_menor', isset($_POST['responsavel']) ? 1 : 0);
        $stmt->bindParam(':unidade_ensino_origem', $_POST['unidade_de_ensino_de_origem']);
        $stmt->bindParam(':forma_ingresso_id', $_POST['forma_ingresso']);
        $stmt->bindParam(':forma_organizacao_id', $_POST['forma_organizacao']);
        $stmt->bindParam(':educacao_id', $_POST['educacao']);
        $stmt->bindParam(':tipo_certidao_id', $_POST['tipo_certidao']);
        $stmt->bindParam(':termo', $_POST['termo']);
        $stmt->bindParam(':circunscricao', $_POST['circunscricao']);
        $stmt->bindParam(':livro', $_POST['livro']);
        $stmt->bindParam(':folha', $_POST['folha']);
        $stmt->bindParam(':cidade_certidao', $_POST['cidade']);
        $stmt->bindParam(':uf_certidao', $_POST['uf']);
        $stmt->bindParam(':identidade', $_POST['identidade']);
        $stmt->bindParam(':data_identidade', $_POST['data-identidade']);
        $stmt->bindParam(':orgao_expedidor', $_POST['orgao-expedidor']);
        $stmt->bindParam(':uf_identidade', $_POST['uf-identidade']);
        $stmt->bindParam(':cpf', $_POST['cpf']);
        $stmt->bindParam(':nacionalidade', $_POST['nacionalidade']);
        $stmt->bindParam(':identidade_frente_path', $identidade_frente_path);
        $stmt->bindParam(':identidade_verso_path', $identidade_verso_path);
        $stmt->bindParam(':cpf_frente_path', $cpf_frente_path);
        $stmt->bindParam(':historico_path', $historico_path);
        $stmt->bindParam(':foto_path', $foto_path);

        if ($stmt->execute()) {
            echo "<div class='sucesso'>Cadastro realizado com sucesso!</div>";
        } else {
            throw new Exception("Erro ao cadastrar aluno");
        }

    } catch (Exception $e) {
        echo "<div class='erro'>Erro: " . $e->getMessage() . "</div>";
    }
}

// Habilitar exibição de erros para debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Função para salvar arquivos
function salvarArquivo($campo, $pasta) {
    if (isset($_FILES[$campo]['error']) && $_FILES[$campo]['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = array(
            UPLOAD_ERR_INI_SIZE => "O arquivo é maior que o limite permitido pelo servidor",
            UPLOAD_ERR_FORM_SIZE => "O arquivo é maior que o limite permitido pelo formulário",
            UPLOAD_ERR_PARTIAL => "O upload foi interrompido",
            UPLOAD_ERR_NO_FILE => "Nenhum arquivo foi enviado",
            UPLOAD_ERR_NO_TMP_DIR => "Pasta temporária não encontrada",
            UPLOAD_ERR_CANT_WRITE => "Falha ao escrever o arquivo",
            UPLOAD_ERR_EXTENSION => "Upload bloqueado por extensão"
        );
        echo "Erro no upload do arquivo $campo: " . 
             (isset($errorMessages[$_FILES[$campo]['error']]) ? 
             $errorMessages[$_FILES[$campo]['error']] : 'Erro desconhecido');
        return null;
    }

    if (!empty($_FILES[$campo]['tmp_name']) && is_uploaded_file($_FILES[$campo]['tmp_name'])) {
        $allowed = array('jpg', 'jpeg', 'png');
        $ext = strtolower(pathinfo($_FILES[$campo]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            echo "Erro: Tipo de arquivo não permitido para o campo $campo. Apenas jpg, jpeg, png são permitidos.<br>";
            return null;
        }

        if ($_FILES[$campo]['size'] > 5 * 1024 * 1024) {
            echo "Erro: O arquivo $campo é muito grande. Tamanho máximo permitido: 5MB<br>";
            return null;
        }

        $nome_original = basename($_FILES[$campo]['name']);
        $novo_nome = uniqid() . "-" . preg_replace("/[^a-zA-Z0-9.\-_]/", "_", $nome_original);
        $caminho_final = $pasta . $novo_nome;

        if (!is_writable($pasta)) {
            echo "Erro: A pasta $pasta não tem permissão de escrita<br>";
            return null;
        }

        if (move_uploaded_file($_FILES[$campo]['tmp_name'], $caminho_final)) {
            return $caminho_final;
        } else {
            echo "Erro ao mover o arquivo $campo. Erro: " . error_get_last()['message'] . "<br>";
            return null;
        }
    }
    return null;
}

$mensagem = '';
$documentos_enviados = null;

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $pasta = "uploads/documentos/";
        if (!file_exists($pasta)) {
            if (!mkdir($pasta, 0777, true)) {
                throw new Exception("Erro ao criar a pasta de uploads.");
            }
        }

        chmod($pasta, 0777);

        $identidade_frente = salvarArquivo('identidade_frente', $pasta);
        $identidade_verso = salvarArquivo('identidade_verso', $pasta);
        $cpf_frente = salvarArquivo('cpf_frente', $pasta);
        $historico = salvarArquivo('historico', $pasta);
        $foto = salvarArquivo('foto', $pasta);

        if (!$identidade_frente || !$identidade_verso || !$cpf_frente || !$historico || !$foto) {
            throw new Exception("Um ou mais arquivos não foram enviados corretamente.");
        }

        $sql = "INSERT INTO documentos (identidade_frente, identidade_verso, cpf_frente, historico, foto) 
                VALUES (:identidade_frente, :identidade_verso, :cpf_frente, :historico, :foto)";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':identidade_frente', $identidade_frente);
        $stmt->bindParam(':identidade_verso', $identidade_verso);
        $stmt->bindParam(':cpf_frente', $cpf_frente);
        $stmt->bindParam(':historico', $historico);
        $stmt->bindParam(':foto', $foto);

        if ($stmt->execute()) {
            $mensagem = "<div class='sucesso'>Documentos enviados e salvos com sucesso!</div>";
            // Recupera os documentos recém enviados
            $documentos_enviados = [
                'identidade_frente' => $identidade_frente,
                'identidade_verso' => $identidade_verso,
                'cpf_frente' => $cpf_frente,
                'historico' => $historico,
                'foto' => $foto
            ];
        } else {
            throw new Exception("Erro ao salvar no banco de dados");
        }
    }

    // Busca todos os documentos
    $sql = "SELECT * FROM documentos ORDER BY data_upload DESC";
    $stmt = $conn->query($sql);
    $todos_documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $mensagem = "<div class='erro'>Erro: " . $e->getMessage() . "</div>";
} finally {
    if (isset($stmt)) {
        $stmt = null;
    }
}
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário de Inscrição Completo - SPM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #e4edfa6c;
        }
     
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
    margin-bottom: 3px;;
    align-items: center; 
    padding: 0px; 
    height: 55px;
    background-color: #f9f9f9; 
}

.logo img {
    position: fixed;
    z-index: 10; /* Garante que o logotipo fique acima do menu */
    height: 55px; /* Ajuste conforme o tamanho do logotipo */
    top: 10px; /* Ajuste conforme a distância do topo */
    left: 10px; /* Ajuste conforme a posição à esquerda */
    right: 100px;
    margin-top: 0; /* Remova o margin-top negativo */
}

/* Menu de navegação */
.menu {
    width: 100%;
    position: fixed;
    top: 0; /* Mantém o menu na mesma linha do logotipo */
    left: 0px;
    background-color: #f9f9f9;
    padding: 18px 0; /* Padding para ajuste do conteúdo dentro do menu */
    text-align: center;
    display: flex; /* Coloca os itens do menu em linha */
    align-items: -100px; /* Alinha os itens do menu verticalmente ao centro */
    justify-content: center; /* Centraliza os itens do menu horizontalmente */
    box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1); /* Sombra para o menu */
    z-index: 1; /* Garante que o menu fique abaixo do logotipo */
}

/* Itens do menu */
.menu a {
    text-decoration: none; /* Remove sublinhado padrão */
    color: rgb(129, 121, 121); /* Cor do texto */
    padding: 10px 25px; /* Espaçamento interno dos links */
    font-family: 'Open-sans', sans-serif;
    font-size: 16px; /* Tamanho da fonte */
    font-weight: normal;
    transition: 0.3s ease; /* Efeito suave ao passar o mouse */
    position: relative;
    display: inline-block;
    margin: 0 15px; /* Adiciona espaçamento lateral */
    left: 350px;
}

/* Cor de fundo ao passar o mouse */
.menu a:hover {
    background-color: #f9f9f9;
}

/* Sublinhado animado */
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
    width: 100%; /* Aumenta o sublinhado ao passar o mouse */
}

/* Itens específicos do menu */
.menu a.inicio::after {
    background-color: #011772; /* Cor do sublinhado para Início */
}

.menu a.inscricao::after {
    background-color: #32cd32; /* Cor do sublinhado para Inscrição */
}

.menu a.regras::after {
    background-color: #9400d3; /* Cor do sublinhado para Regras */
}

.menu a.ajuda::after {
    background-color: #ff4500; /* Cor do sublinhado para Ajuda */
}

.menu a.sobre::after {
    background-color: #ffa500; /* Cor do sublinhado para Sobre */
}

.upload-section {
  display: flex;
  flex-direction: column;
  gap: 20px;
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

.instruction {
  margin-top: 20px;
}

.instruction center {
  font-size: 16px;
  color: #333;
}
    .instruction {
        margin-top: 20px;
    }

    button {
        margin: 10px;
        }        
h1 {
    font-size: 25px;
    color: #292c2d;
    font-family: 'nunito', sans-serif;
    font-weight: normal;
    margin-left: 25px;
    margin-top: 40px;
    margin-bottom: 15px;
}

h2 {
    font-size: 20px;
    color: #000000;
    font-family: 'nunito', sans-serif;
    font-weight: bold;
    margin-left: 80px;
    margin-top: 40px;
    margin-bottom: 15px; 
    padding: 10px;
    border-radius: 5px;
}

hr {
            border: none;
            border-top: 2px solid #fa9828; /* Espessura e cor */
            width: 45%; /* Largura da linha */
            margin-left: 25px;
            margin-bottom: 40px;
            margin-top: -10px;
}
    .user-button {
            background-color: #4CAF50; /* Cor de fundo do botão */
            color: white;
            border: none;
            border-radius: 50%; /* Botão redondo */
            width: 60px; /* Largura do botão */
            height: 60px; /* Altura do botão */
            font-size: 24px; /* Tamanho do ícone */
            cursor: pointer;
            position: relative; /* Para o posicionamento do menu */
            outline: none; /* Remove a borda de foco */
            transition: background-color 0.3s; /* Transição suave ao passar o mouse */
    }
        .user-button:hover {
            background-color: #388E3C; /* Cor ao passar o mouse */
        }

    header img {
      height: 40px;
    }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        .container {
            background-color: rgb(255, 255, 255);
            padding: 20px;
            max-width: 1300px;
            margin: 0 auto;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .form-section {
            display: none; /* Inicialmente, todas as seções estão ocultas */
        }

        .form-section.active {
            display: block; /* Exibe a seção ativa */
        }

        fieldset{
            max-width: 1330px;
            margin-bottom: 20px;
            margin-top: 0px;
            font-weight: normal;
            margin-left: 79px;
            border-radius: 10px;
            padding-top: 20px;
            
           
        }
        label {
            margin-bottom: 2px;
            margin-bottom: 2px;
            font-weight: bold;
            margin-left: 10px;
        }

        input[type="tel"],
        select {
            width: 100%;
        padding: 9px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 10px;
        font-size: 14px;

}
        
        input[type="file"], input[type="text"], input[type="email"], input[type="date"], input[type="tel"] select {
            padding: 8px;
            font-size: 13px;
            margin-bottom: 15px;
            margin-left: 15px;
            border: 1px solid #ccc;
            border-radius: 10px;
            width: 16%;

        }

        #unidade-de-ensino
         {
            width: 1205px;
            height: 37px;
            background-color: #f9f9f9; /* Fundo levemente acinzentado */
            color: #333; /* Cor do texto */
            font-weight: normal; /* Texto em negrito */
            margin-left: 80px;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        #data-matricula {
            width: 150px;
            height: 37px;
            background-color: #f9f9f9; /* Fundo levemente acinzentado */
            color: #333; /* Cor do texto */
            font-weight: normal; /* Texto em negrito */
            margin-left: 15px;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        
        #serie-semestre {
            width: 120px;
            height: 37px;
            background-color: #f9f9f9; /* Fundo levemente acinzentado */
            color: #333; /* Cor do texto */
            font-weight: normal; /* Texto em negrito */
            margin-left: 15px;
            margin-top: -50px;
            margin-bottom: 10px;
        }

        #turma {
            width: 120px;
            height: 37px;
            background-color: #f9f9f9; /* Fundo levemente acinzentado */
            color: #333; /* Cor do texto */
            font-weight: normal; /* Texto em negrito */
            margin-left: 15px;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        #turno {
            width: 120px;
            height: 37px;
            background-color: #f9f9f9; /* Fundo levemente acinzentado */
            color: #333; /* Cor do texto */
            font-weight: normal; /* Texto em negrito */
            margin-left: 15px;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        select {
            width: 17%;
            height: 29%;
            background-color: #f9f9f9; /* Fundo levemente acinzentado */
            color: #333; /* Cor do texto */
            font-weight: normal; /* Texto em negrito */
            margin-left: 10px;
            margin-top: -10px;
            margin-bottom: 10px;


        }

        #curso {
            width: 210px;
            height: 40px;
            background-color: #f9f9f9; /* Fundo levemente acinzentado */
            color: #333; /* Cor do texto */
            font-weight: normal; /* Texto em negrito */
            margin-left: 15px;
            margin-top: -10px;
            margin-bottom: 10px;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .arrow-button {
            background-color: transparent;
            border: none;
            font-size: 24px; /* Tamanho do ícone */
            cursor: pointer;
            color: #4CAF50; /* Cor da seta */
            transition: color 0.3s;
        }

        .arrow-button:hover {
            color: #388E3C; /* Cor da seta ao passar o mouse */
        }

        .error {
            color: red;
            font-size: 14px;
            margin-top: -15px;
            margin-bottom: 10px;
        }
        .header-container {
            max-width: 1480px; /* Largura máxima do container */
            width: 1480px;
            height: 70px;
            margin: center; /* Centraliza o container na página */
            margin-top: -21px;
            margin-bottom: 0px;
            margin-left: -15px;
            margin-right: -22px;
            border: 0.1px solid #e4e4e4; /* Borda do container */
            border-radius: 0px; /* Bordas arredondadas */
            padding: 20px; /* Espaçamento interno do container */
            background-color: #f9f9f9; /* Cor de fundo do container */
            box-shadow: 0px 1px 1px 1px rgba(193, 193, 193, 0.1);
        }


.buttons {
    display: flex;
    justify-content: flex-end;
}

button {
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button.cancelar {
    background-color: #E74C3C;
    color: white;
    margin-right: 10px;
    margin-top: -15px;
}

button.limpar {
    background-color: #7F8C8D;
    color: white;
    margin-right: 15px;
    margin-top: -15px;
    
}

button.enviar {
    background-color: #319946;
    color: white;
    margin-bottom: 15px;
    margin-right: 30px;
}
.round-button {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 80px;
            height: 80px;
            background-color: #4CAF50;
            border-radius: 50%;
            border: none;
            color: white;
            font-size: 30px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .round-button:hover {
            background-color: #45a049;
        }

        .required::after {
            content: " ";
        }
        .checkbox-container {
            display: flex;
            align-items: center;
            font-family: Arial, sans-serif;
            font-size: 16px;
        }

        /* Estilizando o checkbox padrão (escondendo) */
        .checkbox-container input[type="checkbox"] {
            display: none;
        }

        /* Estilo do quadrado customizado */
        .custom-checkbox {
            width: 20px;
            height: 20px;
            border: 2px solid #4CAF50;
            border-radius: 4px;
            display: inline-block;
            position: relative;
            cursor: pointer;
        }

        /* Marca de seleção dentro do quadrado */
        .custom-checkbox::after {
            content: '';
            position: absolute;
            left: 4px;
            top: 1px;
            width: 7px;
            height: 12px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            opacity: 0;
        }

        /* Quando o checkbox está marcado */
        .checkbox-container input[type="checkbox"]:checked + .custom-checkbox {
            background-color: #535b53;
        }

        /* Mostrar a marca de seleção apenas quando estiver marcado */
        .checkbox-container input[type="checkbox"]:checked + .custom-checkbox::after {
            opacity: 1;
        }

        /* Estilo do texto ao lado do checkbox */
        .checkbox-label {
            margin-left: 10px;
        }
        .form > div {
            flex: 1;
            margin-right: 10px;
            min-width: 150px; /* Define a largura mínima para cada campo */
        }

        .form > div:last-child {
            margin-right: 0;
        }
        @media (max-width: 1000px) {
            .form {
                flex-direction: column;
            }
        }
        .form-group {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 11px; /* Espaço entre os campos */
}

.form-group label {
    width: 100px; /* Faz o label ocupar toda a linha */
    margin-bottom: 2px; /* Espaço entre o label e o campo */
}

.form-group input, .form-group select {
    width: calc(25% - 50px); /* 25% do espaço para 4 campos por linha */
    padding: 10px;
    box-sizing: border-box; /* Para garantir que padding não quebre o layout */
}


    </style>
</head>
<body>
    <div class="header-container"></div>
        <div class="logo">
            <img src="./img/Slide S.P.M. (13).png" alt="Logotipo" height="100" width="125" a href="./html.index">
            </a>
        </div>
        <nav class="menu">
            <a href="./logadoaluno.php">Início</a>
            <a href="./inc1.php">Inscrição</a>
            <a href="#consulta">Consulta</a>
            <a href="./duvidaa.html">Dúvidas</a>
            <a href="./rregras1.html">Regras</a>
            <a href="./sobre1.html">Sobre </a>
        </div>
        </nav>      
        <h1>Preencha seus dados abaixo para realizar a inscrição</h1>
        <hr>
        <form id="formInscricao" action="inscricao.php" method="POST">
            <!-- Primeira Parte: Upload de Documentos -->
            <form id="formInscricao" action="inscricao.php" method="POST">
                <!-- Primeira Parte: Upload de Documentos -->
                <div class="form-section active" id="section1">
                    <h2><label for="unidade-de-ensino">Unidade de Ensino</label></h2>
                     <input type="text" id="unidade-de-ensino" name="unidade-de-ensino" placeholder="Unidade de Ensino">
                     <div class="form-group">
                     <fieldset>
                        <div>
                     <label for="data-matricula">Data da Matrícula</label>
                     <input type="date" id="data-matricula" name="data-matricula">
                     <label for="serie-semestre">Série/Semestre</label>
                     <input type="text" id="serie-semestre" name="serie-semestre" placeholder="Série/Semestre">
                            
     
                     <label for="turma">Turma</label>
                     <input type="text" id="turma" name="turma" placeholder="Turma">
     
                     <label for="turno">Turno</label>
                     <select name="turno" id="turno" required>
                    <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM turno ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
                     
                      
                     <div>
                     <label for="cursos">Cursos</label>
                     <select name="cursos" id="cursos" required>
                    <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM cursos ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>


                     <label for="tipo_vaga" class="required">Tipo de Vaga</label>
                     <select name="tipo_vaga" id="tipo_vaga" required>
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

                      <div class="form-group">
                     <div class="form-row">
                     <label for="nome">Nome Completo</label>
                     <input type="text" id="nome" name="nome" placeholder="Nome Completo">
     
                     <label for="email">E-mail</label>
                     <input type="email" id="email" name="email" placeholder="E-mail">
     
                     <label for="nascimento">Data de Nascimento</label>
                     <input type="date" id="nascimento" name="nascimento">

                     <label for="sexo">Sexo</label>
                     <select name="sexo" id="sexo" required>
                    <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM sexo ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>

     
                     <label for="etnia">Etnia</label>
                     <select name="etnia" id="etnia" required>
                     <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM etnia ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
                     
     
                     <label for="necessidades_educacionais_especiais">Necessidades Educacionais Especiais</label>
                     <select id="necessidades_educacionais_especiais" name="necessidades_educacionais_especiais">
                     <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM necessidades_educacionais_especiais ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
     
                     <label for="estado_civil">Estado Civil</label>
                     <select id="estado_civil" name="estado_civil">
                     <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM estado_civil ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                     </select>
                </fieldset>
                
                     <align-item><h2><legend>Endereço</legend></align-items></h2>
                        <fieldset>
                
                                <label for="cep">CEP</label>
                            <input type="text" id="cep" placeholder="Digite o CEP" required>
                            
                            <label for="logradouro">(Rua/Travessa/Estrada, etc.)</label>
                            <input type="text" id="logradouro" placeholder="Rua/Travessa/Estrada, etc." disabled>
                
                            <label for="complemento">Complemento</label>
                            <input type="text" id="complemento" name="complemento" placeholder="Complemento">
                
                            <label for="numero">Número</label>
                            <input type="text" id="numero" name="numero" placeholder="Número">
                
                            <label for="bairro">Bairro</label>
                            <input type="text" id="bairro" placeholder="Bairro" disabled>
                
                            <label for="municipio">Município</label>
                            <input type="text" id="municipio" placeholder="Município" disabled>
                             <div>
                            <label for="uf">UF</label>
                            <input type="text" id="uf" placeholder="UF" disabled>
                
                            <label for="telefone">Telefone</label>
                            <input type="tel" id="telefone" name="telefone" placeholder="(00) 0000-0000">
                
                            <label for="celular">Celular</label>
                            <input type="tel" id="celular" name="celular" placeholder="(00) 00000-0000">

  <button type="button" class="arrow-button" onclick="nextSection()">
    <i class="fas fa-chevron-right"></i>
                                </button>
                                <div class="buttons">
                                    <button type="submit" class="limpar">Limpar</button>
                                    <button type="submit" class="cancelar">Cancelar</button>
                           
                        </fielset>
                            </div>
                            </div>
                            </div>
                       
                
                            <div class="form-section" id="section2">
                                <h2><legend>Filiação</legend></h2>
                                <fieldset>
                                <label for="Nome do Pai">Nome completo do Pai</label>
                                <input type="text" id="nome do pai" name="nome do pai" placeholder="Nome do Pai">
                    
                                <label for="RG do Pai">RG do Pai</label>
                                <input type="text" id="rg do pai" name="rg do pai" placeholder="RG do Pai">
                
                                <div class="form-row"></div>
                                <label for="Nome da Mãe">Nome completo da Mãe</label>
                                <input type="text" id="nome da mae" name="nome da mãe" placeholder="Nome da Mãe">
                    
                                <label for="RG da Mãe">RG da Mãe</label>
                                <input type="text" id="rg da mae" name="rg da mãe" placeholder="RG da Mãe">
                
                                <div class="form-row"></div>
                                <label for="Nome do Responsável">Nome completo do Responsável</label>
                                <input type="text" id="nome do responsavel" name="nome do responsavel" placeholder="Nome do Responsável">
                    
                                <label for="RG do Responsável">RG do Responsável</label>
                                <input type="text" id="rg do responsaavel" name="rg do responsavel" placeholder="RG do Responsável">
                    
                                <div class="checkbox-container">
                                    <input type="checkbox" id="responsavel">
                                    <label for="responsavel" class="custom-checkbox"></label>
                                    <span class="checkbox-label">Você é menor de idade e precisa de um responsável?</span>
                                </div>
                                </fieldset>
                    <br>
                                <h2><legend>Dados Acadêmicos</legend></h2>
                                <fieldset>
                                    <label for="unidade-origem">Unidade de Ensino de Origem</label>
<input type="text" id="unidade-origem" name="unidade de ensino de origem" placeholder="Unidade de Ensino de Origem">

<label for="forma_ingresso">Forma de Ingresso</label>
<select id="forma_ingresso" name="forma_ingresso">
<option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM forma_ingresso ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>

<div class="form-row">
    <label for="forma_organizacao">Forma de Organização</label>
    <select id="forma_organizacao" name="forma_organizacao"
    <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM forma_organizacao ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
        

    <label for="educacao">Educação</label>
    <select id="educacao" name="educacao">
    <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM educacao ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
</div>
                                       <button type="button" class="arrow-button" onclick="prevSection()">
                                          <i class="fas fa-chevron-left"></i>
                                      </button>
  
                                      <button type="button" class="arrow-button" onclick="nextSection()">
                                          <i class="fas fa-chevron-right"></i>
                                      </button>
                                          <div class="buttons">
                                              <button type="submit" class="limpar">Limpar</button>
                                              <button type="submit" class="cancelar">Cancelar</button>
                                      </button>
                                  </div>
                                 </div> 
                              </fieldset>
                              <!-- Terceira Parte: Filiação e Dados Acadêmicos -->
            <div class="form-section" id="section3">
                <h2><legend>Documentação</legend></h2>
                <fieldset>
                <label for="tipo_certidão">Tipo de Certidão</label>
                <select id="tipo_certidao" name="tipo_certidao"> 
                <option value="">Selecione</option>
                    <?php
                    $query = $conn->query("SELECT id, nome FROM tipo_certidao ORDER BY nome ASC");
                    $registros = $query->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($registros as $option) {
                        echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
                    }
                    ?>
                </select>
                
                <label for="termo">Termo</label>
                <input type="text" id="termo" name="termo" placeholder="Termo">

                <label for="circunscricao">Cirscunscrição</label>
                <input type="text" id="circunscricao" name="circunscricao" placeholder="Circunscrição">

                <label for="livro">Livro</label>
                <input type="text" id="livro" name="livro" placeholder="Livro">

                <label for="folha">Folha</label>
                <input type="text" id="folha" name="folha" placeholder="Folha">

                <label for="cidade">Cidade</label>
                <input type="text" id="cidade" name="cidade" placeholder="Cidade">
                
                 <label for="uf-certidao">UF</label>
                <input type="text" id="uf-certidao" name="uf" placeholder="UF">
                <div>
                <label for="identidade">Identidade</label>
                <input type="text" id="identidade" name="identidade" placeholder="RG do Aluno">
                

                <label for="data-identidade">Data</label>
                <input type="date" id="data-identidade" name="data-identidade">

                <label for="orgao-expedidor">Orgão Expedidor</label>
                <input type="text" id="orgao-expedidor" name="orgao-expedidor" placeholder="Orgão Expedidor">

                <label for="uf-identidade">UF</label>
                <input type="text" id="uf-identidade" name="uf-identidade" placeholder="UF">
                </div>
            
                <label for="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" placeholder="CPF do Aluno">

                <label for="nacionalidade">Nacionalidade</label>
                <input type="text" id="nacionalidade" name="nacionalidade" placeholder="Nacionalidade">

                <button type="button" class="arrow-button" onclick="prevSection()">
                    <i class="fas fa-chevron-left"></i>
                </button>

                <button type="button" class="arrow-button" onclick="nextSection()">
                    <i class="fas fa-chevron-right"></i>
                </button>
              
                        <div class="buttons">
                            <button type="submit" class="limpar">Limpar</button>
                            <button type="submit" class="cancelar">Cancelar</button>
                            
                    </button>
                </div>
            </div>
            </fieldset>
            <div class="form-section" id="section4">
                <h2>Upload de Documentos</h2>
                <fieldset>
                    
                <form action="" method="POST" enctype="multipart/form-data">
    <div class="form-container">
        <div class="form-group">
            <div class="document-title">RG (Frente)</div>
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

        <div class="form-group">
            <div class="document-title">RG (Verso)</div>
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

        <div class="form-group">
            <div class="document-title">CPF</div>
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

        <div class="form-group">
            <div class="document-title">Histórico</div>
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

        <div class="form-group">
            <div class="document-title">Foto</div>
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

    <button type="submit">Enviar Documentos</button>
</form>
              
                    <!-- Instruções -->
                    <div class="instruction">
                      <center>Certifique-se que todos seus documentos estão corretos.</center>
                      
                      <button type="button" class="arrow-button" onclick="prevSection()">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="submit" class="cancelar">Cancelar</button>
                    <button type="submit" class="enviar">Enviar</button>
                    </div>
                  </div>
                </fieldset>
              </div>              
    <script>
        function toggleMenu() {
            const menu = document.getElementById('userMenu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }

        // Fecha o menu se clicar fora dele
        window.onclick = function(event) {
            const menu = document.getElementById('userMenu');
            const button = document.querySelector('.user-button');

            if (!button.contains(event.target) && menu.style.display === 'block') {
                menu.style.display = 'none';
            }
        }

        let currentSection = 0; // Índice da seção atual
        const sections = document.querySelectorAll('.form-section');

        // Mostra a seção atual
        function showSection(index) {
            sections.forEach((section, i) => {
                section.classList.toggle('active', i === index);
            });
        }

        // Função para ir para a próxima seção
        function nextSection() {
            if (currentSection < sections.length - 1) {
                currentSection++;
                showSection(currentSection);
            }
        }

        // Função para voltar para a seção anterior
        function prevSection() {
            if (currentSection > 0) {
                currentSection--;
                showSection(currentSection);
            }
        }

        // Exibe a primeira seção
        showSection(currentSection);




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
    </script>
</body>
</html>