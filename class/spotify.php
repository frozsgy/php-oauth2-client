<?php
require_once('oAuthBase.php');

class Spotify extends oAuthBase
{

    public function __construct($uid, $code = '', $state = '') {
        parent::__construct(1, $uid, $code, $state);
        $this->name = "Spotify";
        $this->client_id = '';
        $this->client_secret = '';
        $this->scope = 'user-read-private user-read-email';
        $this->auth_url = "https://accounts.spotify.com/authorize";
        $this->auth_me_url = 'https://accounts.spotify.com/api/token';
    }

    public function getMe()
    {
        if ($this->isAccessTokenValid())
        {
          $request_headers = $this->prepareBearer();
          $this->cc->clearParameters();
          $this->cc->setURL('https://api.spotify.com/v1/me');
          $this->cc->setHeaders($request_headers);
          $rv = $this->cc->process();
          $jr = json_decode($rv);
          $rrv = 'Profile URL: ' . $jr->{'external_urls'}->{'spotify'} . '<br>E-mail: ' . $jr->{'email'} . '<br>Photo: <img src = "' . @$jr->{'images'}[0]->{'url'} . '">';
          return $rrv;
        } else {
            $this->refreshToken();
            return $this->getMe();
        }
    }

    protected function isAccessTokenValid()
    {
        /**
         * Spotify does not provide an endpoint to check if the token is valid,
         * therefore we rely on the database data here.
         */
         return $this->u->getAuthTokenExpiry($this->app_id) > time();
    }
}
