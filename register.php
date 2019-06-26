<!doctype html>
<html lang="en">
<head>
  <title>user registration</title>
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
  </style>
</head>
<body>

  <div style="margin-top:100px; text-align:left; margin-left:300px;">

    <?php
    require_once('./class/user.php');
    if (isset($_POST['uname']) && isset($_POST['email']) && isset($_POST['password'])) {
        $u = new User();
        $un = $_POST['uname'];
        $em = $_POST['email'];
        $pw = $_POST['password'];
        $r = $u->addUser($un, $pw, $em);
        if ($r > 0) {
            print "<h2>User $un created!</h2>";
            print '<p>Click <a href="index.php">here</a> to login</p>';
        } else {
            die("A user with the given credentials already exists</div></body></html>");
        }
    } else {
        die("You forget to fill everything</div></body></html>");
    }
    ?>
  </div>



</body>
</html>
