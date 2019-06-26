<?php

require_once('./class/user.php');
session_start();
if (isset($_POST['email']) && isset($_POST['password'])) {
    $u = new User();
    $em = $_POST['email'];
    $pw = $_POST['password'];
    $r = $u->login($em, $pw);
    if ($r) {
        header('Location: index.php');
        exit();
    } else {
        die("Wrong login data");
    }
} else {
    die("You forget to fill everything");
}
