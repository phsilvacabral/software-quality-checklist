// atualizar checklist
$(document).ready(function() {
    var changedItems = {}; // Objeto para armazenar os itens que foram alterados
    var codChecklist = $('#checklist-container').data('cod-checklist'); // Recupera o valor do data attribute

    // Detecta mudança nos selects
    $('.select-conformidade').change(function() {
        var itemId = $(this).data('id'); // Pega o ID do item
        var newValue = $(this).val(); // Pega o novo valor selecionado
        changedItems[itemId] = newValue; // Armazena o ID do item e o novo valor
    });

    // Função para salvar os dados no banco de dados
    $('#botao-salvar-checklist').click(function(e) {
        e.preventDefault();

        // Verifica se algum item foi alterado
        if (Object.keys(changedItems).length > 0) {
            console.log('Itens alterados:', changedItems); // Depuração

            // Envia os dados via AJAX para o PHP
            $.ajax({
                url: 'php/atualizar_checklist.php', // Verifique se o caminho está correto
                type: 'POST',
                data: {
                    itens: changedItems, // Envia os itens alterados
                    codChecklist: codChecklist // Passa o código do checklist corretamente
                },
                success: function(response) {
                    //alert(response);
                    window.history.back();
                },
                // error: function(xhr, status, error) {
                //     alert('Erro: ' + status + '\nDetalhes: ' + error + '\nResposta do servidor: ' + xhr.responseText);
                // }
            });
        } else {
            alert('Nenhuma alteração foi feita.');
            window.history.back();
        }
    });
});


// versionamento
$(document).ready(function() {
    $('#versao-escalonamento').change(function() {
        var versaoSelecionada = $(this).val();
        $.post('checklist.php', { check_nc: versaoSelecionada }, function(response) {
            console.log(response);
            location.reload();
        }).fail(function() {
            alert('Erro ao enviar a versão.');
        });
    });
});

// abrir checklist nc
$(document).ready(function() {
    $('#abrir-nc').click(function() {
        // Envia uma requisição AJAX para definir a variável $abrir_check_nc como true
        $.post('checklist.php', { abrir_check_nc: true }, function(response) {
            // Atualiza a página após definir a variável
            location.reload(); // Recarrega a página
        }).fail(function() {
            alert('Erro ao tentar abrir as não conformidades.');
        });
    });
});