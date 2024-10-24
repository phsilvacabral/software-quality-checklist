$(document).ready(function () {
    var changedItems = {}; // Objeto para armazenar os itens que foram alterados
    var codChecklist = $('#checklist-container').data('cod-checklist');

    // Detecta mudança nos selects
    $('.select-conformidade').change(function () {
        var itemId = $(this).data('id');
        var newValue = $(this).val();
        changedItems[itemId] = newValue;
    });

    // Função para salvar os dados no banco de dados
    $('#botao-salvar-checklist').click(function (e) {
        e.preventDefault();

        // Verifica se algum item foi alterado
        if (Object.keys(changedItems).length > 0) {
            console.log('Itens alterados:', changedItems);

            $.ajax({
                url: 'php/atualizar_checklist.php',
                type: 'POST',
                data: {
                    itens: changedItems,
                    codChecklist: codChecklist
                },
                success: function (response) {
                    window.location.href = '../checklist/';
                },
            });
        } else {
            // Função para verificar se há algum item "NC" e enviar para escalonar.php
            var itensNc = [];

            // Verifica se algum dos selects contém o valor "NC"
            $('.select-conformidade').each(function () {
                if ($(this).val() === 'NC') {
                    var itemId = $(this).data('id');
                    itensNc.push(itemId); // Adiciona o ID dos itens NC ao array
                }
            });

            // Se houver itens NC, exibir o confirm alert
            if (itensNc.length > 0) {
                var confirmAlert = confirm("Existem itens não conformes. Deseja escalonar e notificar aos responsáveis por essas não conformidades?");
                if (confirmAlert) {
                    // Se o usuário confirmar, envie os IDs dos itens NC para escalonar.php via AJAX
                    $.ajax({
                        url: 'php/escalonar.php', // Caminho do arquivo PHP
                        type: 'POST',
                        data: { itensNc: JSON.stringify(itensNc), codChecklist: codChecklist },
                        success: function (response) {
                            // notificar aos responsáveis
                            $.ajax({
                                url: 'php/email.php',
                                type: 'POST',
                                data: { codChecklist: codChecklist },
                                success: function (response) {
                                    console.log(response);
                                    alert('Não conformidades escalonadas e notificadas com sucesso.');
                                    window.location.href = '../checklist/';
                                },
                                error: function (xhr, status, error) {
                                    console.log(error); // Exibe o erro no console
                                    alert('Erro ao escalonar as não conformidades.');
                                }
                            });
                        },
                    });
                }
            } else {
                // Se não houver itens NC, apenas redireciona
                window.location.href = '../checklist/';
            }
        }
    });



    // Função para excluir checklist inteiro
    $('#botao-excluir').click(function (e) {
        if (confirm('Deseja realmente excluir o checklist?')) {
            $.ajax({
                url: 'php/excluir_checklist.php',
                type: 'POST',
                data: {
                    codChecklist: codChecklist
                },
                success: function (response) {
                    window.history.back();
                },
            });
        }
    });



    // abrir checklist não conforme
    $('#abrir-nc').click(function () {
        var checklistId = $(this).data('checklist');
        var vAtual = $(this).data('versao');
        window.location.href = 'checklist.php?id=' + checklistId + '&versao=' + vAtual;
    });



    // Evento para redirecionar com base na versão selecionada
    $("#select-versao-escalonamento").change(function () {
        var vAtual = $(this).val();
        var checklistId = $(this).data('checklist');

        if (vAtual) {
            // Constrói a URL e redireciona para a nova página
            window.location.href = 'checklist.php?id=' + checklistId + '&versao=' + vAtual;
        }
    });



    // Função para excluir item de checklist
    $(".botao-excluir-item").click(function () {
        var itemId = $(this).data('id');
        $.ajax({
            url: 'php/excluir_item.php',
            type: 'POST',
            data: { id: itemId },
            success: function (response) {
                if (response === 'success') {
                    alert('Item excluído com sucesso!');
                    location.reload(); // Recarrega a página para atualizar a lista de itens
                } else {
                    alert('Erro ao excluir item.');
                }
            },
            error: function () {
                alert('Erro na requisição.');
            }
        });
    });



    // Função para salvar e voltar
    $("#botao-salvar").click(function () {
        var itens = [];

        // Pega todos os dados dos itens de checklist
        $(".item-checklist").each(function () {
            // Verifique se o campo de id, nome, complexidade e responsável existe antes de tentar acessá-los
            var idElement = $(this).find('.id-ck');
            var nomeElement = $(this).find('.name-ck-editar');
            var complexidadeElement = $(this).find('select[name="complexidade"]');
            var responsavelElement = $(this).find('select[name="responsaveis"] option:selected');

            // Certifique-se de que os elementos estão presentes antes de usar .text() ou .val()
            var id = idElement.length ? idElement.text().trim() : null;
            var nome = nomeElement.length ? nomeElement.val().trim() : null;
            var complexidade = complexidadeElement.length ? complexidadeElement.val() : null;
            var responsavel = responsavelElement.length ? responsavelElement.val() : null;

            // Se todos os campos necessários estiverem preenchidos, adiciona ao array de itens
            if (id && nome && complexidade && responsavel) {
                itens.push({
                    id: id,
                    nome: nome,
                    complexidade: complexidade,
                    responsavel: responsavel
                });
            }
        });

        // Verifique se o array de itens não está vazio antes de enviar
        if (itens.length > 0) {
            $.ajax({
                url: 'php/salvar_itens.php', // Caminho para o PHP que vai salvar os itens
                type: 'POST',
                data: {
                    itens: JSON.stringify(itens),
                    checklistId: codChecklist
                },
                success: function (response) {
                    console.log(response); // Para ver a resposta no console
                    if (response === 'success') {
                        alert('Checklist salvo com sucesso!');
                        window.location.href = '../checklist/';
                    } else {
                        alert('Erro ao salvar checklist: ' + response); // Mostra o erro do servidor
                    }
                },
                error: function (xhr, status, error) {
                    alert('Erro na requisição: ' + error);
                }
            });
        } else {
            alert('Nenhum item para salvar.');
            window.location.href = '../checklist/';
        }
    });



    // criar checklist
    $("#botao-publicar-criar").click(function () {
        var nomeChecklist = $("#name-checklist").val().trim();
        var escalonamento = 1; // Escalonamento padrão
        var complexidade = $("#select-complexidade").val();
        var responsavel = $("#select-responsavel").val();

        // Verifica se todos os campos obrigatórios estão preenchidos
        if (nomeChecklist && complexidade && responsavel) {
            $.ajax({
                url: 'php/criar_item_check.php', // Página PHP para processar a criação
                type: 'POST',
                data: {
                    nomeChecklist: nomeChecklist,
                    escalonamento: escalonamento,
                    complexidade: complexidade,
                    responsavel: responsavel,
                    codChecklist: codChecklist
                },
                success: function (response) {
                    if (response === 'success') {
                        alert('Checklist criado com sucesso!');
                        location.reload(); // Atualiza a página após sucesso
                    } else {
                        alert('Erro ao criar checklist: ' + response);
                    }
                },
                error: function (xhr, status, error) {
                    alert('Erro na requisição: ' + error);
                }
            });
        } else {
            alert('Por favor, preencha todos os campos.');
        }
    });



    // Função para abrir o popup quando o botão é clicado
    $('#new-checklist').on('click', function () {
        $('#popup-new-checklist').fadeIn(); // Exibe o popup
    });

    // Função para fechar o popup quando o botão "Cancelar" é clicado
    $('#botao-cancelar-popup').on('click', function () {
        $('#popup-new-checklist').fadeOut(); // Esconde o popup
    });

    // Função para fechar o popup clicando fora dele
    $(document).on('mouseup', function (e) {
        var popupContent = $(".popup-content");
        if (!popupContent.is(e.target) && popupContent.has(e.target).length === 0) {
            $('#popup-new-checklist').fadeOut(); // Esconde o popup se clicar fora
        }
    });

    // Função para tratar a criação do checklist quando o botão "Criar checklist" é clicado
    $('#botao-criar-popup').on('click', function () {
        var titulo = $('#titulo-check').val();
        var descricao = $('#descricao-check').val();
        var criador = $('#conta-name').data('id-logado');

        if (titulo.trim() === "" || descricao.trim() === "") {
            alert("Por favor, preencha todos os campos.");
        } else {
            // Enviar os dados para o servidor usando AJAX
            $.ajax({
                url: 'php/criar_checklist.php', // Caminho correto para o PHP
                type: 'POST',
                data: {
                    titulo: titulo,
                    descricao: descricao,
                    criador: criador
                },
                success: function (response) {
                    location.reload();
                },
                error: function (xhr, status, error) {
                    alert("Erro ao criar checklist: " + error);
                }
            });
        }
    });



});
