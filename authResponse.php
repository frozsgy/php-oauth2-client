<?php
session_start();
require_once('./class/integrations.php');
require_once('./class/loader.php');

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']  == true && isset($_GET['id'])) {
    $aid = $_GET['id'];
    $u = new Integrations($_SESSION['uid'], $_SESSION['username']);
    if (isset($_GET['error'])) {
        if (isset($_GET['error_description'])) {
            print 'Cannot authenticate, error: ' . $_GET['error_description'];
        }
        die();
    } elseif(isset($_GET['code']) && isset($_GET['state'])) {
        $rc = $_GET['code'];
        $rs = $_GET['state'];
        $u->authenticate($aid, $rc, $rs);
    } else {
        die("Cannot auth\n");
    }
} else {
    include('login.html');
}
