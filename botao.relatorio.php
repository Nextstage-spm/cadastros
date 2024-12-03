<!DOCTYPE html> 
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="shortcut icon" href="./img/Slide S.P.M. (13).png" type="image/x-icon">
    <style>
        .button-container {
            display: grid;
            grid-template-areas: 
                "btn1 btn2 btn3"
                ". btn4 ."
                "btn5 btn6 btn7";
            row-gap: 10px; 
            grid-gap: 20px;
            max-width: 800px;
            margin: 0 auto;
            margin-top: 120px;
            justify-content: center;
            align-items: center;
            
        }

        
        a.button {
            row-gap: 100px; 
            padding: 20px;
            font-size: 18px;
            font-weight: bold;
            font-family: "Open Sans", sans-serif;
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
        }

        .button i {
            margin-left: 10px; /* Espaçamento entre o texto e o ícone */
            font-size: 24px;
        }

        .btn-orange-light-top {
            margin-left: -1px;
            width: 300px;
            height: 55px;
            background: linear-gradient(to bottom, #ed9bf4, #5c1465);
            grid-area: btn1;
            margin-top: -30px;
            margin-bottom: 80px;
        }
        
        .btn-purple-light-top {
            width: 300px;
            height: 55px;
            background: linear-gradient(to bottom, #a08ef9, #1d0966);
            grid-area: btn2;
            margin-top: -30px;
            margin-bottom: 80px;
        }

        .btn-green-light-top {
            width: 300px;
            height: 55px;
            background: linear-gradient(to bottom, #afcc8d, #16581a);
            grid-area: btn3;
            margin-top: -30px;
            margin-bottom: 80px;
        }

        .btn-red-light-top {
            width: 300px;
            height: 55px;
            background: linear-gradient(to bottom, #f15d71, #b81701);
            grid-area: btn4;
            margin-top: -30px;
            margin-bottom: 80px;
        }

        .btn-orange-light-bottom {
            width: 300px;
            height: 55px;
            background: linear-gradient(to bottom, #adadad, #615d5d);
            grid-area: btn5;
            margin-top: -30px;
            margin-bottom: 80px;
        }
        
        .btn-purple-light-bottom {
            width: 300px;
            height: 55px;
            background: linear-gradient(to bottom, #57c8d0, #0a5b4f);
            grid-area: btn6;
            margin-top: -30px;
            margin-bottom: 80px;
        }

        .btn-green-light-bottom {
            width: 300px;
            height: 55px;
            background: linear-gradient(to bottom, #fa92d5, #a91a56);
            grid-area: btn7;
            margin-top: -30px;
            margin-bottom: 80px;
        }
    </style>
</head>
<body>

<div class="button-container">
    <a href="#" data-url="relatorio.func1.php" class="carregar-conteudo button btn-orange-light-top">Funcionário<i class="fas fa-hard-hat"></i></a>
    <a href="#" data-url="relatorio.php" class="carregar-conteudo button btn-purple-light-top">Curso<i class="fas fa-book-open"></i></a>
    <a href="#" data-url="relatorio.prof.php" class="carregar-conteudo button btn-green-light-top">Professor<i class="fas fa-chalkboard-teacher"></i></a>
    <a href="#" data-url="relatorio.turma2.php" class="carregar-conteudo button btn-red-light-top">Turma<i class="fas fa-users"></i></a>
    <a href="#" data-url="relatorio.dic.php" class="carregar-conteudo button btn-orange-light-bottom">Disciplina<i class="fas fa-book"></i></a>
    <a href="#" data-url="relatorio.aluno.php" class="carregar-conteudo button btn-purple-light-bottom">Aluno<i class="fas fa-user-graduate"></i></a>
    <a href="#" data-url="relatorio.int.php" class="carregar-conteudo button btn-green-light-bottom">Instituição<i class="fas fa-university"></i></a>
</div>


<script>
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





function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.currentTarget.parentNode;
            dropdown.classList.toggle('show-dropdown');
        }
    </script>
</body>
</html>
