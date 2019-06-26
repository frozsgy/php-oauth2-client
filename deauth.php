<?php
session_start();
require_once('./class/integrations.php');
require_once('./class/loader.php');

if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']  == true && isset($_GET['id'])) {
    $aid = $_GET['id']; 
    $u = new Integrations($_SESSION['uid'], $_SESSION['username']);
    $u->disintegrate($aid);
    ?>
    <script type="text/javascript">
        function closeWindow()
        {
            setTimeout(function() {
                window.close();
            }, 1500);
            window.opener.location.reload();
        }
        window.onload = closeWindow();
        </script>
    <?php
} else {
    include('login.html');
}
