<?php

class ServerInfo
{
    public static $root_url;
}

/**
 * Update according to your server configuration
 */

ServerInfo::$root_url = 'http://' . $_SERVER['SERVER_NAME'] . '/folder-name/';
