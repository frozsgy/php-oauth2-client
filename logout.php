<?php

require_once('./class/user.php');

if ($_SESSION['loggedIn']) {
    $u = new User();
    $s = $u->logOut();
    if ($s) {
        print "<h2>Goodbye!</h2>";
    } else {
        die("You didn't even logged in at the first place.");
    }
} else {
    die("You forgot to fill everything");
}
