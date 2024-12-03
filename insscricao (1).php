<?php 
include("config.php"); // Inclui a conexão com o banco de dados

// Função para gerar opções para os selects
function geraOpcoes($conn, $tableName) {
    $options = '';
    $sql = "SELECT id FROM tipo_de_vaga"; // Ajuste conforme o nome da coluna
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $options .= '<option>' . $row['nome'] . '</option>'; // Ajuste se a coluna tiver outro nome
        }
    }
    
    return $options;
}
?>

<form id="formInscricao" action="inscricao.php" method="POST">
    <!-- Primeira Parte: Upload de Documentos -->
    <div class="form-section active" id="section1">
        <h2><label for="unidade-de-ensino">Unidade de Ensino</label></h2>
        <input type="text" id="unidade-de-ensino" name="unidade-de-ensino" placeholder="Unidade de Ensino">
        
        <div class="form-group">
            <fieldset>
                <label for="data-matricula">Data da Matrícula</label>
                <input type="date" id="data-matricula" name="data-matricula">
                
                <label for="serie-semestre">Série/Semestre</label>
                <input type="text" id="serie-semestre" name="serie-semestre" placeholder="Série/Semestre">

                <label for="turma">Turma</label>
                <input type="text" id="turma" name="turma" placeholder="Turma">

                <label for="turno">Turno</label>
                <select id="turno" name="turno">
                    <option>Selecione</option>
                    <?php echo geraOpcoes($conn, 'turnos'); // Tabela de turnos ?>
                </select>

                <label for="curso">Cursos</label>
                <select id="curso" name="curso">
                    <option>Selecione</option>
                    <?php echo geraOpcoes($conn, 'cursos'); // Tabela de cursos ?>
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
                    <?php echo geraOpcoes($conn, 'sexos'); // Tabela de sexos ?>
                </select>

                <label for="etnia">Etnia</label>
                <select id="etnia" name="etnia">
                    <option>Selecione</option>
                    <?php echo geraOpcoes($conn, 'etnias'); // Tabela de etnias ?>
                </select>

                <label for="tipo_de_vaga" class="required">Tipo de Vaga</label>
                <select id="tipo_de_vaga" name="tipo_de_vaga" required>
                <option value="">Selecione</option>
               <?php
               // Consulta para obter as titulações disponível
        $query = $conn->query("SELECT id, nome FROM tipo_de_vaga ORDER BY nome ASC");
        $registros = $query->fetchAll(PDO::FETCH_ASSOC);

        // Exibe as opções de titulação no select
        foreach ($registros as $option) {
            echo "<option value=\"{$option['id']}\">{$option['nome']}</option>";
        }
        ?>
    </select>
                    <option>Selecione</option>
                    <?php echo geraOpcoes($conn, 'tipo_de_vagas'); // Tabela de tipos de vaga ?>
                </select>

                <label for="concurso" class="required">Concurso</label>
                <select id="concurso" name="concurso" required>
                    <option>Selecione</option>
                    <?php echo geraOpcoes($conn, 'concursos'); // Tabela de concursos ?>
                </select>

                <label for="necessidades">Necessidades Educacionais Especiais</label>
                <select id="necessidades" name="necessidades">
                    <option>Selecione</option>
                    <?php echo geraOpcoes($conn, 'necessidades'); // Tabela de necessidades ?>
                </select>

                <label for="estado-civil">Estado Civil</label>
                <select id="estado-civil" name="estado-civil">
                    <option>Selecione</option>
                    <?php echo geraOpcoes($conn, 'estados_civis'); // Tabela de estados civis ?>
                </select>
            </fieldset>
            <br>

            <h2><legend>Endereço</legend></h2>
            <fieldset>
                <label for="cep">CEP</label>
                <input type="text" id="cep" name="cep" placeholder="Digite o CEP" required>

                <label for="logradouro">(Rua/Travessa/Estrada, etc.)</label>
                <input type="text" id="logradouro" name="logradouro" placeholder="Rua/Travessa/Estrada, etc." disabled>

                <label for="complemento">Complemento</label>
                <input type="text" id="complemento" name="complemento" placeholder="Complemento">

                <label for="numero">Número</label>
                <input type="text" id="numero" name="numero" placeholder="Número">

                <label for="bairro">Bairro</label>
                <input type="text" id="bairro" name="bairro" placeholder="Bairro" disabled>

                <label for="municipio">Município</label>
                <input type="text" id="municipio" name="municipio" placeholder="Município" disabled>

                <label for="uf">UF</label>
                <input type="text" id="uf" name="uf" placeholder="UF" disabled>

                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone" placeholder="(00) 0000-0000">

                <label for="celular">Celular</label>
                <input type="tel" id="celular" name="celular" placeholder="(00) 00000-0000">
                
                <br><br>
                <button type="button" class="arrow-button" onclick="nextSection()">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="buttons">
                    <button type="submit" class="limpar">Limpar</button>
                    <button type="submit" class="cancelar">Cancelar</button>
                </div>
            </div>
        </div>

        <!-- Seções seguintes... -->
    </div>

    <!-- Fechando a conexão -->
    <?php $conn->close(); ?>
</form>
