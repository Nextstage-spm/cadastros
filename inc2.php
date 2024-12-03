<?php
session_start(); 

include("config.php"); 

$mensagem = "";
$tipo_alerta = "";


if (!isset($_SESSION['id'])) {
    $mensagem = "Você precisa estar logado para editar o perfil.";
    $tipo_alerta = "error";
} else {
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['nome']) && isset($_POST['email']) && isset($_POST['telefone']) && isset($_POST['data_nascimento']) && isset($_POST['cpf']) && isset($_POST['unidade_ensino']) && isset($_POST['id'])) {
            $id = $_POST['id'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];
            $data_nascimento = $_POST['data_nascimento'];
            $unidade_ensino = $_POST['unidade_ensino'];
            $cpf = $_POST['cpf'];

            try {
                $updateQuery = $conn->prepare("UPDATE aluno_cadastro SET nome = :nome, email = :email, telefone = :telefone, data_nascimento = :data_nascimento, cpf = :cpf, unidade_ensino = :unidade_ensino WHERE id = :id");
                $updateQuery->bindParam(':nome', $nome);
                $updateQuery->bindParam(':email', $email);
                $updateQuery->bindParam(':telefone', $telefone);
                $updateQuery->bindParam(':data_nascimento', $data_nascimento);
                $updateQuery->bindParam(':cpf', $cpf);
                $updateQuery->bindParam(':unidade_ensino', $unidade_ensino);
                $updateQuery->bindParam(':id', $id);

                if ($updateQuery->execute()) {
                    $mensagem = "Perfil atualizado com sucesso!";
                    $tipo_alerta = "success";
                } else {
                    $mensagem = "Erro ao atualizar o perfil.";
                    $tipo_alerta = "error";
                }
            } catch (PDOException $e) {
                $mensagem = "Erro ao atualizar o perfil: " . $e->getMessage();
                $tipo_alerta = "error";
            }
        }
    }

    // Recuperação dos dados do aluno
    $id = $_SESSION['id']; // ID do aluno logado
    $sql = "SELECT id, nome, email, telefone, data_nascimento, cpf, unidade_ensino FROM aluno_cadastro WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    // Verificação para garantir que o aluno foi encontrado
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$aluno) {
        $mensagem = "Aluno não encontrado.";
        $tipo_alerta = "error";
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
        /* Cabeçalho */
        header {
    background-color: #f9f9f9; /* Cor de fundo do cabeçalho */
    padding: 0px 0;
}
/* Contêiner do cabeçalho */
.header-container {
    display: flex;
    justify-content: space-between; /* Distribui o logotipo à esquerda e o menu à direita */
    align-items: right; /* Alinha os itens verticalmente ao centro */
    width: 99%;
    margin: 0px 0px; /* Centraliza o conteúdo na tela */
}
   
/* Estilo para o header */
header {
    display: flex;
    justify-content: space-between; /* Espaça o logo e o banner */
    margin-top: 5px;
    margin-bottom: 3px;;
    align-items: center; /* Alinha o logo e o banner no centro verticalmente */
    padding: 0px; /* Espaçamento ao redor do header */
    height: 55px;
    background-color: #f9f9f9; /* Cor de fundo para o header (pode ser alterada conforme necessário) */
}

/* Logotipo */
.logo-img {
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
            width: 34%; /* Largura da linha */
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
        padding: 20px;
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
            width: 15%;

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
            <a href="./inc2.php">Inscrição</a>
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
                     <select id="turno" name="turno">
                         <option>Selecione</option>
                         <!-- Adicionar opções -->
                         <option>Matutino</option>
                         <option>Vespertino</option>
                         <option>Noturno</option>
                     </select>
                      
                     <div class="form-group">
                     <label for="curso">Cursos</label>
                     <select id="curso" name="curso">
                         <option>Selecione</option>
                         <option>Técnico de Informática</option>
                         <option>Inglês</option>
                         <option>Corte e Costura</option>
                         <!-- Adicionar opções -->
                     </select>

                     <label for="concurso" class="required">Concurso</label>
                     <select id="concurso" name="concurso" required>
                         <option>Selecione</option>
                         <option>Pontuação</option>
                         <option>Concurso</option>
                     </select>

                     <label for="vaga" class="required">Tipo de Vaga</label>
                     <select id="vaga" name="vaga" required>   
                         <option>Selecione</option>
                         <option>Senior</option>
                         <option>Pleno</option>
                         <option>Junior</option>
                         <option>Não Informado</option>
                         <!-- Adicionar opções --> 
                     </select>

                      <div class="form-group">
                     <div class="form-row">
                     <label for="nome">Nome Completo</label>
                     <input type="text" id="nome" name="nome" placeholder="Nome Completo" value="<?php echo htmlspecialchars($aluno['nome']); ?>" readonly disabled>
     
                     <label for="email">E-mail</label>
                     <input type="email" id="email" name="email" placeholder="E-mail" value="<?php echo htmlspecialchars($aluno['email']); ?>" readonly disabled>
     
                     <label for="nascimento">Data de Nascimento</label>
                     <input type="date" id="nascimento" name="nascimento" value="<?php echo htmlspecialchars($aluno['data_nascimento']); ?>" readonly disabled>

                     <label for="sexo">Sexo</label>
                     <select id="sexo" name="sexo">
                         <option>Selecione</option>
                         <option>Feminino</option>
                         <option>Masculino</option>
                         <option>Outro</option>
                         <!-- Adicionar opções -->
                     </select>
     
                     <label for="etnia">Etnia</label>
                     <select id="etnia" name="etnia">
                         <option>Selecione</option>
                         <option>Branco</option>
                         <option>Preto</option>
                         <option>Pardo</option>
                         <option>Indígena</option>
                         <option>Amarelo</option>
                         <!-- Adicionar opções -->
                     </select>
     
                     <label for="necessidades">Necessidades Educacionais Especiais</label>
                     <select id="necessidades" name="necessidades"  placeholder="Selecione">
                         <option>Selecione</option>
                         <!-- Adicionar opções -->
                         <option>Visual</option>
                         <option>Auditiva</option>
                         <option>Mental</option>
                         <option>Física</option>
                         <option>Múltipla</option>
                     </select>
     
                     <label for="estado-civil">Estado Civil</label>
                     <select id="estado-civil" name="estado-civil">
                         <option>Selecione</option>
                         <option>Casado</option>
                         <option>Divorciado</option>
                         <option>Solteiro</option>
                         <option>Outros</option>
                         <!-- Adicionar opções -->
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
                
                            <label for="uf">UF</label>
                            <input type="text" id="uf" placeholder="UF" disabled>
                
                            <label for="telefone">Telefone</label>
                            <input type="tel" id="telefone" name="telefone" placeholder="(00) 0000-0000">
                
                            <label for="celular">Celular</label>
                            <input type="tel" id="celular" name="celular" placeholder="(00) 00000-0000">
                <br>
                <br>
                
  <button type="button" class="arrow-button" onclick="nextSection()">
    <i class="fas fa-chevron-right"></i>
                                </button>
                                <div class="buttons">
                                    <button type="submit" class="limpar">Limpar</button>
                                    <button type="submit" class="cancelar">Cancelar</button>
                            </button>
                        </fielset>
                            </div>
                            </div>
                            </div>
                       
                
                            <div class="form-section" id="section2">
                                <h2><legend>Filiação</legend></h2>
                                <fieldset>
                                <label for="Nome do Pai">Nome do Pai</label>
                                <input type="text" id="nome completo do pai" name="nome do pai" placeholder="Nome completo do Pai">
                    
                                <label for="RG do Pai">RG do Pai</label>
                                <input type="text" id="rg do pai" name="rg do pai" placeholder="RG do Pai">
                
                                <div class="form-row"></div>
                                <label for="Nome da Mãe">Nome da Mãe</label>
                                <input type="text" id="nome da mae" name="nome da mãe" placeholder="Nome Completo da Mãe">
                    
                                <label for="RG da Mãe">RG da Mãe</label>
                                <input type="text" id="rg da mae" name="rg da mãe" placeholder="RG da Mãe">
                
                                <div class="form-row"></div>
                                <label for="Nome do Responsável">Nome do Responsável</label>
                                <input type="text" id="nome do responsavel" name="nome do responsavel" placeholder="Nome Completo do Responsável">
                    
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

<label for="forma-ingresso">Forma de Ingresso</label>
<select id="forma-ingresso" name="forma de ingresso">
    <option>Selecione</option>
    <option>Concurso</option>
    <option>Sorteio</option>
    <option>Transferido</option>
    <option>Ingresso Direto</option>
</select>

<div class="form-row">
    <label for="forma-organizacao">Forma de Organização</label>
    <select id="forma-organizacao" name="forma de organizacao">
        <option>Selecione</option>
        <option>Educação Infantil</option>
        <option>Ensino Fundamental</option>
        <option>Ensino Médio</option>
        <option>Outros</option>
    </select>

    <label for="educacao">Educação</label>
    <select id="educacao" name="educacao">
        <option>Selecione</option>
        <option>Subsequente</option>
        <option>Conc. Interna</option>
        <option>Conc. Externa</option>
        <option>Superior</option>
        <option>Integrada</option>
        <option>Normal</option>
        <option>Formação Geral</option>
        <option>Especialização</option>
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
                <label for="Tipo de certidão">Tipo de Certidão</label>
                <select id="certidao" name="tipo de certidao"> 
                    <option>Selecione</option>
                    <!-- Adicionar opções -->
                    <option>Certidão de Nascimento</option>
                    <option>Certidão de Casamento</option>
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
            
                <label for="identidade">Identidade</label>
                <input type="text" id="identidade" name="identidade" placeholder="RG do Aluno">

                <label for="data-identidade">Data</label>
                <input type="date" id="data-identidade" name="data-identidade">

                <label for="orgao-expedidor">Orgão Expedidor</label>
                <input type="text" id="orgao-expedidor" name="orgao-expedidor" placeholder="Orgão Expedidor">

                <label for="uf-identidade">UF</label>
                <input type="text" id="uf-identidade" name="uf-identidade" placeholder="UF">
            
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
                  <div class="upload-section">
                    <div class="document-group">
                      
                      <!-- Identidade -->
                      <div class="document">
                        <div class="upload-label">Identidade</div>
                        <label class="upload-box">
                          Frente
                          <input type="file" name="identidade_frente" accept="image/png, image/jpeg">
                        </label>
                        <label class="upload-box">
                          Verso
                          <input type="file" name="identidade_verso" accept="image/png, image/jpeg">
                        </label>
                      </div>
                      
                      <!-- CPF -->
                      <div class="document">
                        <div class="upload-label">CPF</div>
                        <label class="upload-box">
                          Somente a frente
                          <input type="file" name="cpf_frente" accept="image/png, image/jpeg">
                        </label>
                      </div>
                      
                      <!-- Histórico -->
                      <div class="document">
                        <div class="upload-label">Histórico</div>
                        <label class="upload-box">
                          Somente a frente
                          <input type="file" name="historico" accept="image/png, image/jpeg">
                        </label>
                      </div>
                      
                      <!-- Foto 3x4 -->
                      <div class="document">
                        <div class="upload-label">Foto 3x4</div>
                        <label class="upload-box">
                          Fundo branco
                          <input type="file" name="foto" accept="image/png, image/jpeg">
                        </label>
                      </div>
                    </div>
              
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


    </script>
</body>
</html>