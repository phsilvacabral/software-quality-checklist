$(document).ready(function() {
    $('#cadastro-button').click(function(e) {
        e.preventDefault(); // Impede o envio padrão do formulário

        // Coleta os dados do formulário
        var nome = $('#nome').val();
        var email = $('#email').val();
        var senha = $('#senha').val();
        var senha2 = $('#senha2').val();
        var tipo = $('input[name="tipo"]:checked').val(); // Obtém o tipo de usuário selecionado

        // Verifica se todos os campos foram preenchidos
        if (nome === '' || email === '' || senha === '' || senha2 === '' || !tipo) {
            $('#cadastro-message').text('Por favor, preencha todos os campos.');
            return;
        }

        // Verifica se as senhas coincidem
        if (senha !== senha2) {
            $('#cadastro-message').text('As senhas não coincidem.');
            return;
        }

        // Faz a requisição AJAX para cadastrar.php
        $.ajax({
            url: '../php/cadastrar.php',
            type: 'POST',
            data: {
                nome: nome,
                email: email,
                senha: senha,
                tipo: tipo
            },
            success: function(response) {
                // Redireciona para a página de login caso o cadastro seja bem-sucedido
                if (response === 'ok') {
                    window.location.href = '../';
                } else {
                    // Exibe a mensagem de erro caso o cadastro não seja bem-sucedido
                    $('#cadastro-message').text(response);
                }
            },
            error: function(xhr, status, error) {
                // Mostra mensagem de erro em caso de falha
                $('#cadastro-message').text('Erro ao cadastrar. Tente novamente.');
            }
        });
    });
});