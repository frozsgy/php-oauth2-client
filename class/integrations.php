<?php

require_once('db.php');
require_once('app.php');
require_once('t-integrations.php');

class Integrations implements userAndApps
{
    private $a;
    private $apps;
    private $uid;
    private $username = '';
    private $classes;

    public function __construct($uid, $username = '')
    {
        $this->a = new Apps();
        $this->apps = $this->a->getApps();
        $this->classes = $this->a->getClasses();
        $this->uid = $uid;
        if ($username) {
            $this->username = $username;
        }
    }

    public function addApp($aid)
    {
        if ($this->userHasApp($aid)) {
            return false;
        } elseif ($this->a->doesAppExist($aid)) {
            $sql = "INSERT into `user_apps` VALUES (null, '$this->uid', '$aid', '', '', '');";
            DB::$c->query($sql);
            return true;
        } else {
            return false;
        }
    }

    public function removeApp($aid)
    {
        if ($this->userHasApp($aid)) {
            $sql = "DELETE FROM `user_apps` WHERE `aid` = '$aid' and `uid` = '$this->uid';";
            DB::$c->query($sql);
            return true;
        } else {
            return false;
        }
    }

    public function updateRefreshToken($aid, $token)
    {
        if ($this->userHasApp($aid)) {
            $tt = DB::$c->real_escape_string($token);
            $sql = "UPDATE `user_apps` SET `refresh_token` = '$tt' WHERE `uid` = '$this->uid' and `aid` = '$aid';";
            $r = DB::$c->query($sql);
            return true;
        } else {
            return false;
        }
    }

    public function updateAuthToken($aid, $token, $expr)
    {
        if ($this->userHasApp($aid)) {
            $tt = DB::$c->real_escape_string($token);
            $sql = "UPDATE `user_apps` SET `access_token` = '$tt', `expiry` = '$expr' WHERE `uid` = '$this->uid' and `aid` = '$aid';";
            $r = DB::$c->query($sql);
            return true;
        } else {
            return false;
        }
    }

    public function getAuthToken($aid)
    {
        if ($this->userHasApp($aid)) {
            $sql = "SELECT * from `user_apps` WHERE `aid` = '$aid' and `uid` = '$this->uid'";
            $r = DB::$c->query($sql);
            if ($r->num_rows == 1) {
                $p = $r->fetch_assoc();
                if ($p["expiry"] > time()) {
                    return $p["access_token"];
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    public function getAuthTokenExpiry($aid)
    {
        if ($this->userHasApp($aid)) {
            $sql = "SELECT * from `user_apps` WHERE `aid` = '$aid' and `uid` = '$this->uid'";
            $r = DB::$c->query($sql);
            if ($r->num_rows == 1) {
                $p = $r->fetch_assoc();
                return $p["expiry"];
            }
        } else {
            return false;
        }
    }

    public function getRefreshToken($aid)
    {
        if ($this->userHasApp($aid)) {
            $sql = "SELECT * from `user_apps` WHERE `aid` = '$aid' and `uid` = '$this->uid'";
            $r = DB::$c->query($sql);
            if ($r->num_rows == 1) {
                $p = $r->fetch_assoc();
                return $p["refresh_token"];
            }
        } else {
            return false;
        }
    }

    private function getUserApps()
    {
        $myApps = array();
        $sql = "SELECT * from  `user_apps` WHERE `uid` = '$this->uid' ORDER by `id`";
        $cr = DB::$c->query($sql);
        if ($cr->num_rows) {
            while ($r = $cr->fetch_assoc()) {
                array_push($myApps, $r["aid"]);
            }
        }
        return $myApps;
    }

    private function userHasApp($aid)
    {
        return in_array($aid, $this->getUserApps());
    }

    public function getUserAppCount()
    {
        $sql = "SELECT * from  `user_apps` WHERE `uid` = '$this->uid' ORDER by `id`";
        $r = DB::$c->query($sql);
        return mysqli_num_rows($r);
    }

    public function printUserApps()
    {
        $r = $this->getUserApps();
        if ($r) {
          print '<ul>';
          foreach ($r as $k) {
            print '<li><a href="#" onClick="openNew(\'./auth.php?id=' . $k . '\');">' . $this->apps[$k] . '</a>  <a href="#" onClick="openNew(\'./deauth.php?id=' . $k . '\');">❌</a></li>';
          }
          print '</ul>';
        }
    }

    public function printRemainingApps()
    {
        if ($this->a->getAppCount() - $this->getUserAppCount()) {
            print '<h3>Apps that you can add to your profile</h3>';
            print '<p style="font-style:italic">You can click on the links to authenticate the following apps.</p>';
            print '<ul>';
            $userApps = $this->getUserApps();
            foreach ($this->apps as $id => $name) {
                if (!in_array($id, $userApps)) {
                print '<li><a href="#" onClick="openNew(\'./auth.php?id=' . $id . '\');">' . $name . '</a></li>';
                }
            }
            print '</ul>';
        }
    }

    public function integrate($aid)
    {
        if (is_numeric($aid) && isset($this->apps[$aid])) {
            if ($this->getAuthToken($aid)) {
                $expr = $this->getAuthTokenExpiry($aid);
                $o = new $this->classes[$aid]($this->uid, 'reauth', $this->getAuthToken($aid));
                if ($expr > time()) {
                  $o->setExpiry($expr);
                } else {
                  $o->refreshToken();
                }
                print $o->getMe();
            } else {
                $o = new $this->classes[$aid]($this->uid);
                $o->redirectToAuth();
            }
        } else {
            die('Wrong app');
        }
    }

    public function authenticate($aid, $code, $state)
    {
        if (is_numeric($aid) && isset($this->apps[$aid])) {
            $this->addApp($aid);
            $a = new $this->classes[$aid]($this->uid, $code, $state);
            if (!($a->getAccessToken())) {
                die("Cannot auth\n");
            } else {
                print $a->getMe();
            }
        }
    }

    public function disintegrate($aid)
    {
        if (is_numeric($aid) && $this->userHasApp($aid)) {
            $sql = "DELETE FROM `user_apps` WHERE `aid` = '$aid' and `uid` = '$this->uid';";
            $r = DB::$c->query($sql);
            print $this->apps[$aid] . " has deleted from your profile";
        } else {
            die('Wrong app');
        }
    }

}
