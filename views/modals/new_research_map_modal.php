<?php ?>

<div id="new_research_map_modal" class="w3-modal theme-text">
    <div class="w3-modal-content">
        <div class="w3-container">
            <span onclick="document.getElementById('new_research_map_modal').style.display='none'" class="w3-button button-all button-tertiary w3-display-topright"><i class="fas fa-times"></i></span>
            <h2>Informar Missão</h2>

            <p class="theme-text-secondary-faded">Selecione o local do pokestop no mapa</p>
        </div>
        <div id="researchMap" class="full-width app-map" style="position: relative;"></div>
        <div class="w3-container w3-padding-16">

            <label for="research">Missão:</label>
            <input type="text" id="map_research" name="research" class="w3-input full-width w3-margin-bottom" maxlength="200"/>

            <p class="theme-text-secondary-faded">Preencha pelo menos um desses campos<i class="fas fa-arrows-alt-v" style="margin-left: 8px"></i></p>

            <label for="reward">Recompensa:</label>
            <input type="text" id="map_reward" name="reward" class="w3-input full-width w3-margin-bottom" maxlength="200"/>

            <div class="duo_button_div">
                <div class="duo_button_left">
                    <input type="button" id="research_map_cancel" class="w3-button button-all button-secondary" value="CANCELAR" style="width: 100%" onclick="document.getElementById('new_research_map_modal').style.display='none'"/>
                </div>
                <div class="duo_button_right">
                    <input type="button" id="research_map_confirm" class="w3-button button-all button-main" value="CONFIRMAR" style="width: 100%"/>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var researchMap = null;
    var startPos = null;
    var researchMarker = null;

    function researchMapModalOnLoad () {
        var input = document.getElementById("map_research");

        input.addEventListener("keyup", function(event) {
            if (event.keyCode === 13) {
                $('#map_reward').focus();
            }
        });

        input = document.getElementById("map_reward");

        input.addEventListener("keyup", function(event) {
            if (event.keyCode === 13) {
                $('#research_map_confirm').trigger('click');
            }
        });
    }

    function geoSuccess (position) {
        startPos = position;

        arr = [startPos.coords.latitude, startPos.coords.longitude];

        if(researchMap != null) {
            researchMap.flyTo(arr, 18);

            let e = {latlng: arr}
            onMapClick(e);
        }
    }
    function geoError (error) {
        switch (error.code) {
            case error.TIMEOUT:
                break;
        }
        $('#map-crosshair').prop('disabled', true);
    }

    function openMapModal() {
        let modal = document.getElementById('new_research_map_modal');
        modal.style.display='block';
        modal.focus();

        if(researchMap === null) {
            initializeMap();
        }
    }

    function initializeMap() {
        let arr = [-22.0088, -47.8918];

        researchMap = L.map('researchMap').setView(arr, 13);

        L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 18,
            id: 'mapbox.streets',
            accessToken: 'pk.eyJ1IjoibmF0c3hkIiwiYSI6ImNqbWtqeHMyZjByaGYzdnBtbGQyeXA0ZXQifQ.ChFOYFIHGFNK0z5mJnDO_w'
        }).addTo(researchMap);

        researchMap.on('click', onMapClick);

        let html = '<button id="map-crosshair" onclick="goToCurrentLocation()"><i class="fas fa-crosshairs"></i></button>';
        $('#researchMap').append(html);
    }

    function onMapClick(e) {
        if(researchMarker !== null) {
            researchMarker.removeFrom(researchMap);
        }
        researchMarker = L.marker(e.latlng, {
            draggable: true
        }).addTo(researchMap);

        // researchMarker.on('dragend', function(e) {
        //     console.log("LLL");
        // });
    }

    function goToCurrentLocation() {
        event.stopPropagation();
        navigator.geolocation.getCurrentPosition(geoSuccess, geoError);
    }

    $('#research_map_confirm').on('click', function() {
        let research = $('#map_research').val().trim();
        let reward = $('#map_reward').val().trim();
        if(researchMarker == null) {
            toastr['warning']("Por favor informe a localização da missão");
            return;
        }
        if(research == "" && reward == "") {
            toastr['warning']("Por favor informe a missão e/ou a recompensa");
            $('#research').focus();
            return;
        }
        $(this).prop('disabled', true);
        let latLng = researchMarker.getLatLng();
        latLng = [latLng.lat, latLng.lng].toString();

        $.post("/pogo/php_posts/post_research.php", {
            operation: 'new_research',
            pokestop_coordinates: latLng,
            research: research,
            reward: reward
        })
        .done(function (data) {
            if (data['status'] == 1) {
                toastr['success'](data['message']);
                $('#new_research_map_modal').hide();
                $('#map_research').val('');
                $('#map_reward').val('');
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
                    'coordenadas': latLng,
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
            $('#research_map_confirm').prop('disabled', false);
        })
        .fail(function() {
            toastr['error']('Erro desconhecido');
            $('#research_map_confirm').prop('disabled', false);
        });
    });
</script>