<?php ?>

<div id="new_research_modal" class="w3-modal theme-text">
    <div class="w3-modal-content">
        <div class="w3-container w3-padding-16">
            <span onclick="document.getElementById('new_research_modal').style.display='none'" class="w3-button button-all button-tertiary w3-display-topright"><i class="fas fa-times"></i></span>
            <h2>Informar Missão</h2>

            <label for="pokestop_name">Pokestop:</label>
            <input type="text" id="pokestop_name" name="pokestop_name" class="w3-input full-width w3-margin-bottom" maxlength="360"/>

            <label for="research">Missão:</label>
            <input type="text" id="research" name="research" class="w3-input full-width w3-margin-bottom" maxlength="200"/>

            <p class="theme-text-secondary-faded">Preencha pelo menos um desses campos<i class="fas fa-arrows-alt-v" style="margin-left: 8px"></i></p>

            <label for="reward">Recompensa:</label>
            <input type="text" id="reward" name="reward" class="w3-input full-width w3-margin-bottom" maxlength="200"/>

            <div class="w3-col w3-half duo_button_left">
                <input type="button" id="research_confirm" class="w3-button button-all button-main" value="CONFIRMAR" style="width: 100%"/>
            </div>
            <div class="w3-col w3-half duo_button_right">
                <input type="button" id="research_cancel" class="w3-button button-all button-secondary" value="CANCELAR" style="width: 100%" onclick="document.getElementById('new_research_modal').style.display='none'"/>
            </div>
        </div>
    </div>
</div>

<script>
    $('#research_confirm').on('click', function() {
        let pokestop_name = $('#pokestop_name').val().trim();
        let research = $('#research').val().trim();
        let reward = $('#reward').val().trim();
        if(pokestop_name == "") {
            toastr['warning']("Por favor informe o pokestop");
            return;
        }
        if(research == "" && reward == "") {
            toastr['warning']("Por favor informe a missão e/ou a recompensa");
            return;
        }
        $(this).prop('disabled', true);
        $.post("/pogo/php_posts/post_research.php", {
            operation: 'new_research',
            pokestop_name: pokestop_name,
            research: research,
            reward: reward
        })
        .done(function (data) {
            console.log(data);
            if (data['status'] == 1) {
                toastr['success'](data['message']);
                $('#new_research_modal').hide();
                $('#pokestop_name').val('');
                $('#research').val('');
                $('#reward').val('');
                let rere = '';
                if(research == '') {
                    rere = reward;
                }
                else if(reward == '') {
                    rere = research;
                }
                else {
                    rere = research + ': ' + reward;
                }
                let el = {
                    'id': data['data']['insert_id'],
                    'removable': 1,
                    'pokestop': pokestop_name,
                    'missao': rere,
                    'dia': moment().format('YYYY-MM-DD')
                };
                let html = getResearchElement(el);
                if($('.research-container').length > 0) {
                    $('.research-container').eq(0).before(html);
                }
                else {
                    $('#research_div').append(html);
                }
                bindDeleteResearchButtonAction();
            }
            else {
                console.log(data);
                toastr['error'](data['message']);
            }
            $('#research_confirm').prop('disabled', false);
        })
        .fail(function() {
            toastr['error']('Erro desconhecido');
            $('#research_confirm').prop('disabled', false);
        });
    });

    function bindDeleteResearchButtonAction() {
        $('.research-delete-button').off().on('click', function() {

            let research_div = $(this).closest('.research-container');
            $('#confirm_yes').off().on('click', function() {
                console.log(research_div);
                deleteResearch(research_div);
            });
            $('#confirm_title').text('Deseja mesmo remover o informe de missão?');
            document.getElementById('confirm_modal').style.display='block';
        });
    }

    function deleteResearch(research_div) {
        let research_id = research_div.attr('data-id');
        $.post( "/pogo/php_posts/post_research.php", {
            operation: 'delete_research',
            research: research_id
        })
        .done(function(data) {
            console.log(data);
            if(data['status'] == 1) {
                research_div.next().remove();
                research_div.remove();
                document.getElementById('confirm_modal').style.display='none';
                toastr['success'](data['message']);
            }
            else {
                toastr['error'](data['message']);
            }
        });
    }
</script>
