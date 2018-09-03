<?php

echo "  var vanishDivSelector = $('.vanish');
        if(vanishDivSelector.length > 0) {
            history.replaceState(null, document.title, '".$_SERVER['PHP_SELF']."');
            setTimeout(function() {
                vanishDivSelector.remove();
            }, 5000);
        }";