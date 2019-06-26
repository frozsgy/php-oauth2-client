<?php

require_once('db.php');

class Apps
{
    private $apps=array();
    private $appCount=0;
    private $classes=array();
    private $files=array();

    public function __construct()
    {
        $sql = "SELECT * from  `apps` WHERE `enabled` = '1' ORDER by `id`";
        $cr = DB::$c->query($sql);
        if ($cr->num_rows) {
            while ($r = $cr->fetch_assoc()) {
                $this->apps[$r["id"]] = $r["name"];
                $this->classes[$r["id"]] = $r["class"];
                $this->files[$r["id"]] = strtolower($r["class"]);
                $this->appCount++;
            }
        }
  }

    public function getApps()
    {
        return $this->apps;
    }

    public function getAppCount()
    {
        return $this->appCount;
    }

    public function getClasses()
    {
        return $this->classes;
    }

    public function loadClasses()
    {
        foreach ($this->files as $l => $class) {
            require_once($class.'.php');
        }
    }

    public function printApps()
    {
        if ($this->getAppCount()) {
            print '<ul>';
            foreach ($this->apps as $id => $name) {
                print '<li><a onClick="openNew(./auth.php?id='.$id.');">' . $name . '</a></li>';
            }
            print '</ul>';
        }
    }

    public function doesAppExist($id)
    {
        if (is_numeric($id) && $this->apps[$id]) {
            return true;
        }
        return false;
    }

}
