<?php ?>

<div id="new_pokestop_gym_modal" class="w3-modal theme-text">
    <div class="w3-modal-content">
        <div class="w3-container w3-padding-16">
            <span onclick="document.getElementById('new_pokestop_gym_modal').style.display='none'" class="w3-button button-all button-tertiary w3-display-topright"><i class="fas fa-times"></i></span>
            <h2 class="w3-margin-bottom">Sugerir pokestop ou ginásio não listado</h2>

            <label for="pokestop_name">Nome Oficial:</label>
            <input type="text" id="pokestop_name" name="pokestop_name" class="w3-input full-width w3-margin-bottom" placeholder="Estátua IFSC" maxlength="50"/>

            <label for="research">Apelidos ou referências desse pokestop:</label>
            <input type="text" id="aliases" name="aliases" class="w3-input full-width w3-margin-bottom" placeholder="Hitler, Instituto de Física USP" maxlength="300"/>

            <div class="w3-margin-bottom">
                <input type="radio" id="type_pokestop" name="type" value="p" checked class="w3-radio"/><label for="type_pokestop"> Pokestop</label>
                <br/>
                <input type="radio" id="type_gym" name="type" value="g" class="w3-radio"/><label for="type_gym"> Ginásio</label>
            </div>

            <div class="w3-col w3-half duo_button_left">
                <input type="button" id="new_pokestop_gym_confirm" class="w3-button button-all button-main" value="CONFIRMAR" style="width: 100%"/>
            </div>
            <div class="w3-col w3-half duo_button_right">
                <input type="button" id="new_pokestop_gym_cancel" class="w3-button button-all button-secondary" value="CANCELAR" style="width: 100%" onclick="document.getElementById('new_pokestop_gym_modal').style.display='none'"/>
            </div>
        </div>
    </div>
</div>

<script>
    $('#new_pokestop_gym_confirm').on('click', function() {
        let pokestop_name = $('#pokestop_name').val().trim();
        let aliases = $('#aliases').val().trim();
        let type = $('input[name="type"]:checked').val();
        if(pokestop_name == "") {
            toastr['warning']("Por favor informe o nome do pokestop ou ginásio");
            return;
        }
        $(this).prop('disabled', true);
        $.post("/pogo/php_posts/post_pokestop.php", {
            operation: 'new_pokestop',
            pokestop_name: pokestop_name,
            aliases: aliases,
            type: type
        })
        .done(function (data) {
            console.log(data);
            if (data['status'] == 1) {
                toastr['success'](data['message']);
                $('#new_research_modal').hide();
                $('#pokestop_name').val('');
                $('#aliases').val('');
            }
            else {
                toastr['error'](data['message']);
            }
            $('#new_pokestop_gym_confirm').prop('disabled', false);
        })
        .fail(function() {
            toastr['error']('Erro desconhecido');
            $('#new_pokestop_gym_confirm').prop('disabled', false);
        });
    });
</script>
