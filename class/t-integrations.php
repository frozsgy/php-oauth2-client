<?php

interface userAndApps
{
    public function addApp($aid);
    public function removeApp($aid);
    public function updateRefreshToken($aid, $token);
    public function updateAuthToken($aid, $token, $expr);
    public function getAuthToken($aid);
    public function getAuthTokenExpiry($aid);
    public function getRefreshToken($aid);
    public function getUserAppCount();
    public function printUserApps();
    public function printRemainingApps();
}
