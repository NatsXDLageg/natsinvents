<?php

echo "  var errorDivSelector = $('.vanish');
        if(errorDivSelector.length > 0) {
            setTimeout(function() {
                errorDivSelector.remove();
            }, 5000);
        }";