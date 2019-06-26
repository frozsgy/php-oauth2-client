<!doctype html>
<html lang="en">
<head>
  <title>hello</title>
  <style>
    input {
      margin-top: 5px;
      margin-bottom: 5px;
      display:inline-block;
      *display: inline;     /* for IE7*/
      zoom:1;              /* for IE7*/
      vertical-align:middle;
      margin-left:10px
    }
    label {
      display:inline-block;
      *display: inline;     /* for IE7*/
      zoom:1;              /* for IE7*/
      float: left;
      padding-top: 10px;
      text-align: right;
      width: 140px;
    }
    a {
      text-decoration: none;
      color: green;
      font-weight: 500;
    }
  </style>
  <script>
  function openNew(url) {
    var ra=(new Date).getTime();
    var left=(screen.width/2)-425;
    var top=(screen.height/2)-300;
    window.open(url,'authenticateWindow'+ra,'height=600,width=850,left='+left+',top='+top+',resizable=no,scrollbars=yes,toolbar=no,menubar=no,location=no,directories=no, status=yes');
  }
</script>
</head>
<body>

  <div style="margin-top:100px; text-align:left; margin-left:300px;">
    <?php
    session_start();
    if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']  == true) {
        if(isset($_GET['o']) && $_GET['o'] == 'logout') {
            include('logout.php');
            print 'If you\'d like to login again, click <a href="./?">here</a>';
        } else {
            require_once('./class/user.php');
            require_once('./class/integrations.php');
            $u = new User($_SESSION['username'], $_SESSION['uid'], 1);
            $a = new Integrations($_SESSION['uid']);
            print '<h2>Hello '.$u->getUsername().'!</h2>';
            print '<p>If you\'d like to logout, click <a href="./?o=logout">here</a></p>';
            print '<h3>Your apps</h3>';
            $a->printUserApps();
            $a->printRemainingApps();
        }
    } else {
        include('login.html');
    }
    ?>
  </div>



</body>
</html>
