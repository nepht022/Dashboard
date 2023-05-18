//quando o documento for carregado
$(document).ready(() => {
    //ao clicar em documentacao, o documentacao.html será carregado
	$('#documentacao').on('click', () => {
        $('#pagina').load('documentacao.html')
        //outra forma de carregar os dados na página
        /*$.get('documentacao.html', data => {
            $('#pagina').html(data)
        })*/
    })
    //ao clicar em suporte, o suporte.html será carregado
    $('#suporte').on('click', () => {
        $('#pagina').load('suporte.html')
    })

    //ao mudar o campo competencia, atribui seu novo valor a uma variavel
    $('#competencia').on('change', function(e){
        let competencia = $(e.target).val()

        //tenta fazer uma requisicao via ajax para o app.php passando o novo valor do campo competencia via GET
        $.ajax({
            type: 'GET',//tipo da requisicao
            url: 'app.php',//destino da requisicao
            data: `competencia=${competencia}`,//dado enviado na requisicao
            dataType: 'json',//tipo dos dados retornados
            //se a requisicao foi um sucesso, transforma o '?' dos cards nos valores recuperados
            success: function(dados){
                $('#num_vendas').html(dados.num_vendas)
                $('#total_vendas').html(dados.total_vendas)
                $('#clientes_ativos').html(dados.total_clientes_ativos)
                $('#clientes_inativos').html(dados.total_clientes_inativos)
                $('#total_despesas').html(dados.total_despesas)
                $('#total_reclamacoes').html(dados.total_reclamacoes)
                $('#total_elogios').html(dados.total_elogios)
                $('#total_sugestoes').html(dados.total_sugestoes)
            },
            //se a requisicao nao teve sucesso, exibe uma mensagem de error
            error: function(error){console.log(error)}
        })
    })
})