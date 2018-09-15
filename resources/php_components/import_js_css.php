<?php

$cache_sufix = '?'.time();

echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';

//     ___ ___ ___
//    / __/ __/ __|
//   | (__\__ \__ \
//    \___|___/___/
//
if(isset($w3css) && $w3css == true) {
    echo '<link rel="stylesheet" type="text/css" href="/pogo/resources/css/w3.css">';
}
if(isset($theme) && $theme == true) {
    echo '<link rel="stylesheet" type="text/css" href="/pogo/resources/css/theme.css'.$cache_sufix.'">';
}
if(isset($fontAwesome) && $fontAwesome == true) {
    echo '<link rel="stylesheet" type="text/css" href="/pogo/resources/css/all.min.css">';
}
if(isset($toastr) && $toastr == true) {
    echo '<link rel="stylesheet" type="text/css" href="/pogo/resources/css/toastr.min.css">';
}
if(isset($awesomplete) && $awesomplete == true) {
    echo '<link rel="stylesheet" type="text/css" href="/pogo/resources/css/awesomplete.css">';
}
if(isset($iconSelect) && $iconSelect == true) {
    echo '<link rel="stylesheet" type="text/css" href="/pogo/resources/css/iconselect.css" >';
}

//       _ ___
//    _ | / __|
//   | || \__ \
//    \__/|___/
//
if(isset($jquery) && $jquery == true) {
    echo '<script type="text/javascript" src="/pogo/resources/js/jquery-3.3.1.min.js"></script>';
}
if(isset($toastr) && $toastr == true) {
    echo '<script type="text/javascript" src="/pogo/resources/js/toastr.min.js"></script>';
}
if(isset($awesomplete) && $awesomplete == true) {
    echo '<script type="text/javascript" src="/pogo/resources/js/awesomplete.min.js"></script>';
}
if(isset($iconSelect) && $iconSelect == true) {
    echo '<script type="text/javascript" src="/pogo/resources/js/iconselect.js"></script>';
    echo '<script type="text/javascript" src="/pogo/resources/js/iscroll.js"></script>';
}
if(isset($moment) && $moment == true) {
    echo '<script type="text/javascript" src="/pogo/resources/js/moment-with-locales.js"></script>';
}
if(isset($html2canvas) && $html2canvas == true) {
    echo '<script type="text/javascript" src="/pogo/resources/js/html2canvas.js"></script>';
}