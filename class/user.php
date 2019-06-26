<?php

require_once('db.php');

class User
{
    private $username;
    private $expiry;
    private $level = 0;
    private $id;
    private $defLoggedInTime = 3600;
    private $isLoggedIn = false;

    public function __construct($username = '', $uid = 0, $level = 0)
    {
        if ($uid && $level) {
            $this->username = $username;
            $this->id = $uid;
            $this->level = $level;
        }
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getID()
    {
        return $this->id;
    }

    public function isLoggedIn()
    {
        return $this->isLoggedIn;
    }

    public function addUser($username, $password, $email)
    {
        if ($this->checkUser($username)) {
            return -1;
        } elseif ($this->checkEmail($email)) {
            return -2;
        } else {
            $un = DB::$c->real_escape_string($username);
            $em = DB::$c->real_escape_string($email);
            $pw = password_hash($password, PASSWORD_ARGON2I);
            $sql = "INSERT into `users` VALUES (null, '$un', '$pw', '$em');";
            DB::$c->query($sql);
            return $this->checkUser($username);
        }
    }

    public function login($email, $password)
    {
        $uid = $this->checkEmail($email);
        if ($this->isLoggedIn) {
            return false;
        }
        if ($uid && password_verify($password, $this->getHashfromID($uid))) {
            $this->username = $this->getUsernamefromID($uid);
            $this->expiry = time() + $this->defLoggedInTime;
            $this->level = 1;
            $this->id = $uid;
            $this->isLoggedIn = true;
            $_SESSION['loggedIn'] = true;
            $_SESSION['username'] = $this->username;
            $_SESSION['uid'] = $this->id;
            $_SESSION['hash'] = $password;
            return true;
        }
        return false;
    }

    public function logOut()
    {
        if ($_SESSION['loggedIn']) {
            session_destroy();
            $this->username = '';
            $this->expiry = time();
            $this->level = 0;
            $this->id = 0;
            $this->isLoggedIn = false;
            return true;
        }
        return false;
    }

    /**
     * Private methods below
     */

    private function checkUser($username)
    {
        $username = DB::$c->real_escape_string($username);
        $sql = "SELECT `id`, `username` from `users` WHERE `username` = '$username'";
        $r = DB::$c->query($sql);
        if ($r->num_rows == 1) {
            $p = $r->fetch_assoc();
            return $p["id"];
        }
        return 0;
    }

    private function checkEmail($email)
    {
        $email = DB::$c->real_escape_string($email);
        $sql = "SELECT `id`, `email` from `users` WHERE `email` = '$email'";
        $r = DB::$c->query($sql);
        if ($r->num_rows == 1) {
            $p = $r->fetch_assoc();
            return $p["id"];
        }
        return 0;
    }

    private function getHashfromID($id)
    {
        $sql = "SELECT `id`, `password` from `users` WHERE `id` = '$id'";
        $r = DB::$c->query($sql);
        $p = $r->fetch_assoc();
        return $p["password"];
    }

    private function getUsernamefromID($id)
    {
        $sql = "SELECT `id`, `username` from `users` WHERE `id` = '$id'";
        $r = DB::$c->query($sql);
        $p = $r->fetch_assoc();
        return $p["username"];
    }

    private function getIDfromUsername($username)
    {
        $username = DB::$c->real_escape_string($username);
        $sql = "SELECT `id`, `username` from `users` WHERE `username` = '$username'";
        $r = DB::$c->query($sql);
        if ($r->num_rows == 1) {
            $p = $r->fetch_assoc();
            return $p["id"];
        }
        return 0;
    }

}
