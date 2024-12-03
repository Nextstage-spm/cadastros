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
}

hr {
            border: none;
            border-top: 2px solid #fa9828; /* Espessura e cor */
            width: 41%; /* Largura da linha */
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
            margin-bottom: 1px;
            margin-top: 0px;
            font-weight: normal;
            margin-left: 75px;
            border-radius: 10px;
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
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        font-size: 16px;
}
        
        input[type="file"], input[type="text"], input[type="email"], input[type="date"], input[type="tel"] select {
            padding: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            margin-left: 15px;
            border: 1px solid #ccc;
            border-radius: 10px;
            width: 50%;
        }

        #unidade-de-ensino
         {
            width: 1320px;
            height: 37px;
            background-color: #f9f9f9; /* Fundo levemente acinzentado */
            color: #333; /* Cor do texto */
            font-weight: normal; /* Texto em negrito */
            margin-left: 80px;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        #data-matricula {
            width: 130px;
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
            width: 120px;
            height: 37px;
            background-color: #f9f9f9; /* Fundo levemente acinzentado */
            color: #333; /* Cor do texto */
            font-weight: normal; /* Texto em negrito */
            margin-left: 15px;
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
}

button.limpar {
    background-color: #7F8C8D;
    color: white;
}

button.enviar {
    background-color: #21d946;
    color: white;
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
        gap: 10px; /* Espaço entre os campos */
}

.form-group label {
    width: 100px; /* Faz o label ocupar toda a linha */
    margin-bottom: 0px; /* Espaço entre o label e o campo */
}

.form-group input, .form-group select {
    width: calc(25% - 50px); /* 25% do espaço para 4 campos por linha */
    padding: 8px;
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
            <a href="./index.php">Início</a>
            <a href="./inc.php">Inscrição</a>
            <a href="#consulta">Consulta</a>
            <a href="./dduvida.html">Dúvidas</a>
            <a href="./rregras.html">Regras</a>
            <a href="./sobre.html">Sobre </a>
        </div>
        </nav>

        <h1>Preencha seus dados abaixo para realizar a inscrição</h1>
        <hr>
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

                <label for="curso">Cursos</label>
                <select id="curso" name="curso">
                    <option>Selecione</option>
                    <option>Técnico de Informática</option>
                    <option>Inglês</option>
                    <option>Corte e Costura</option>
                    <!-- Adicionar opções -->
                </select>

                <label for="nome">Nome/Nome Social do Aluno</label>
                <input type="text" id="nome" name="nome" placeholder="Nome Completo">

                <label for="email">E-mail do Aluno</label>
                <input type="email" id="email" name="email" placeholder="E-mail">

                <label for="nascimento">Data de Nascimento</label>
                <input type="date" id="nascimento" name="nascimento">

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

                <label for="vaga" class="required">Tipo de Vaga</label>
                <select id="vaga" name="vaga" required>
                    <option>Selecione</option>
                    <option>SR</option>
                    <option>PD</option>
                    <option>RP</option>
                    <option>N/I</option>
                    <!-- Adicionar opções -->
                </select>

                <label for="concurso" class="required">Concurso</label>
                <select id="concurso" name="concurso" required>
                    <option>Selecione</option>
                    <option>Pontuação</option>
                    <option>Concurso</option>
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
                <br>

        <h2><legend>Endereço</legend></h2>
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
            </div>
            </div>
            </div>
       

            <div class="form-section" id="section2">
                <h2><legend>Filiação</legend></h2>
                <fieldset>
                <label for="Nome Completo do Pai">Nome Completo do Pai</label>
                <input type="text" id="nome do pai" name="nome do pai" placeholder="Nome Completo do Pai">
    
                <label for="RG do Pai">RG do Pai</label>
                <input type="text" id="rg do pai" name="rg do pai" placeholder="RG do Pai">
    
                <label for="Nome Completo da Mãe">Nome Completo da Mãe</label>
                <input type="text" id="nome da mae" name="nome da mãe" placeholder="Nome Completo da Mãe">
    
                <label for="RG da Mãe">RG da Mãe</label>
                <input type="text" id="rg da mae" name="rg da mãe" placeholder="RG da Mãe">
    
                <label for="Nome Completo do Responsável">Nome Completo do Responsável</label>
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
                    <label for="unidade de ensino de origem">Unidade de Ensino de Origem</label>
                    <input type="text" id="unidade-origem" name="unidade de ensino de origem" placeholder="Unidade de Ensino de Origem">
                    <label for="forma de ingresso">Forma de Ingresso</label>
                    <select id="forma de ingresso" name="forma de ingresso"> 
                        <option>Selecione</option>
                        <!-- Adicionar opções -->
                        <option>Concurso</option>
                        <option>Sorteio</option>
                        <option>Transferido</option>
                        <option>Ingresso Direto</option>
                    </select>
    
                    <label for="forma de organizacao">Forma de Organização</label>
                    <select id="forma de organizacao" name="forma de organizacao"> 
                        <option>Selecione</option>
                        <!-- Adicionar opções -->
                        <option>Educação Infantil</option>
                        <option>Ensino Fundamental</option>
                        <option>Ensino Médio</option>
                        <option>Outros</option>
                    </select>
    
                    <label for="Educacao">Educação</label>
                    <select id="educacao" name="educacao"> 
                        <option>Selecione</option>
                        <!-- Adicionar opções -->
                        <option>Subsequente</option>
                        <option>Conc. Interna</option>
                        <option>Conc. Externa</option>
                        <option>Superior</option>
                        <option>Integrada</option>
                        <option>Normal</option>
                        <option>Formação Geral</option>
                        <option>Especialização</option>
                    </select>

                    <button type="button" class="arrow-button" onclick="prevSection()">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="arrow-button" onclick="nextSection()">
                        <i class="fas fa-chevron-right"></i> </button>

                    <div class="buttons">
                        <button type="submit" class="limpar">Limpar</button>
                        <button type="submit" class="cancelar">Cancelar</button>
                </button>
                </fieldset>
            </div>

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
                            <label>Identidade (Frente)</label>
                            <input type="file" id="identidadeFrente" name="identidadeFrente" accept="image/*">
                            <span id="errorIdentidadeFrente" class="error"></span>
    
                            <label>Identidade (Verso)</label>
                            <input type="file" id="identidadeVerso" name="identidadeVerso" accept="image/*">
                            <span id="errorIdentidadeVerso" class="error"></span>
                        
                            <label>CPF</label>
                            <input type="file" id="cpf" name="cpf" accept="image/*">
                            <span id="errorCPF" class="error"></span>
                        
                            <label>Histórico</label>
                            <input type="file" id="historico" name="historico" accept="image/*">
                            <span id="errorHistorico" class="error"></span>
                      
                       
                            <label>Foto 3x4</label>
                            <input type="file" id="foto" name="foto" accept="image/*">
                            <span id="errorFoto" class="error"></span>

                            <button type="button" class="arrow-button" onclick="prevSection()">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        
                            <button type="submit" class="cancelar">Cancelar</button>
                            <button type="submit" class="enviar">Enviar</button>
        </form>
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

         // Função que consulta o CEP na API ViaCEP
document.getElementById('cep').addEventListener('blur', function() {
    const cep = this.value.replace(/\D/g, ''); // Remove caracteres não numéricos

    if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`) // Adicionei as aspas
            .then(response => response.json())
            .then(data => {
                if (!data.erro) {
                    // Preenche os campos do formulário com os dados retornados pela API
                    document.getElementById('logradouro').value = data.logradouro;
                    document.getElementById('bairro').value = data.bairro;
                    document.getElementById('municipio').value = data.localidade;
                    document.getElementById('uf').value = data.uf;
                } else {
                    alert("CEP não encontrado.");
                }
            })
            .catch(error => {
                alert("Erro ao consultar o CEP. Verifique sua conexão.");
            });
    } else {
        alert("Formato de CEP inválido.");
    }
});

    </script>
</body>
</html>