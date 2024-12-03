// No seu arquivo main.js ou 3.3.js
document.addEventListener('DOMContentLoaded', function() {
    // Função para inicializar os eventos do CRUD
    function initializeCrudEvents() {
        // Previne o refresh em todos os formulários
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Envia o formulário via AJAX
                const formData = new FormData(this);
                fetch(this.action, {
                    method: this.method,
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    // Atualiza apenas a parte necessária da página
                    document.getElementById('conteudo').innerHTML = data;
                    // Reinicializa os eventos após atualizar o conteúdo
                    initializeCrudEvents();
                })
                .catch(error => console.error('Erro:', error));
            });
        });
    }

    // Função para carregar conteúdo dinâmico
    function loadContent(url) {
        fetch(url)
            .then(response => response.text())
            .then(data => {
                document.getElementById('conteudo').innerHTML = data;
                // Importante: reinicializa os eventos do CRUD após carregar novo conteúdo
                initializeCrudEvents();
                // Reativa outros scripts se necessário
                ativarScriptsDinamicos();
            })
            .catch(error => {
                console.error('Erro ao carregar conteúdo:', error);
                document.getElementById('conteudo').innerHTML = '<p>Erro ao carregar o conteúdo.</p>';
            });
    }

    // Previne o comportamento padrão dos links com a classe carregar-conteudo
    document.addEventListener('click', function(e) {
        const target = e.target.closest('a');
        if (target && target.classList.contains('carregar-conteudo')) {
            e.preventDefault();
            const url = target.getAttribute('data-url');
            if (url) {
                loadContent(url);
            }
        }
    });

    // Inicializa os eventos do CRUD quando a página carrega
    initializeCrudEvents();
});

// Função para reativar scripts
function ativarScriptsDinamicos() {
    const scripts = document.getElementById('conteudo').getElementsByTagName('script');
    for (let script of scripts) {
        const novoScript = document.createElement('script');
        novoScript.textContent = script.textContent;
        script.parentNode.replaceChild(novoScript, script);
    }
}