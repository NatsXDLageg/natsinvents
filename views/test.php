<?php
/**
 * Created by PhpStorm.
 * User: natha
 * Date: 08/09/2018
 * Time: 23:19
 */
session_start();

echo '<p>GET: ';
var_dump($_GET);
echo '</p>';
echo '<p>POST: ';
var_dump($_POST);
echo '</p>';
echo '<p>SESSION: ';
var_dump($_SESSION);
echo '</p>';
echo '<p>COOKIE: ';
var_dump($_COOKIE);
echo '</p>';