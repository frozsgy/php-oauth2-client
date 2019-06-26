<?php

interface baseClass
{
    public function redirectToAuth();
    public function setExpiry($expr);
    public function getAccessToken();
    public function refreshToken();
}
