<?php ?>

<div id="confirm_modal" class="w3-modal theme-text">
    <div class="w3-modal-content">
        <div class="w3-container w3-padding-16">
            <span onclick="document.getElementById('confirm_modal').style.display='none'" class="w3-button button-all button-tertiary w3-display-topright"><i class="fas fa-times"></i></span>
            <h2 id="confirm_title">Confirma?</h2>
            <div class="duo_button_div">
                <div class="duo_button_left">
                    <input type="button" id="confirm_no" class="w3-button button-all button-secondary" value="NÃO" style="width: 100%" onclick="document.getElementById('confirm_modal').style.display='none'"/>
                </div>
                <div class="duo_button_right">
                    <input type="button" id="confirm_yes" class="w3-button button-all button-main" value="SIM" style="width: 100%"/>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
</script>
