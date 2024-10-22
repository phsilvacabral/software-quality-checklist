$(document).ready(function () {
    // Função acionada ao clicar no botão
    $("#loginButton").on("click", function (event) {
        event.preventDefault(); // Impede o comportamento padrão do botão

        // Coleta os dados dos inputs
        var email = $("#email").val();
        var senha = $("#senha").val();

        // Verifica se os campos estão preenchidos
        if (email === "" || senha === "") {
            $("#login-message").html("Por favor, preencha todos os campos.");
            $("#login-message").css("color", "white");

            return; // Impede a requisição se os campos estiverem vazios
        }

        // Envia os dados via AJAX para logar.php
        $.ajax({
            url: '../php/logar.php', // Página de destino
            type: 'POST', // Método de envio
            data: {
                email: email,
                senha: senha
            },
            success: function (response) {
                if (response === "ok") {
                    window.location.href = "../";
                } else {
                    $("#login-message").html(response);
                    $("#login-message").css("color", "white");
                }

            },
            error: function (xhr, status, error) {
                // Exibe uma mensagem de erro em caso de falha
                $("#login-message").html("Ocorreu um erro: " + error);
                $("#login-message").css("color", "white");
            }
        });
    });
});
