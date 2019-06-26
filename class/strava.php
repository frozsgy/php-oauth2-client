<?php
require_once('oAuthBase.php');

class Strava extends oAuthBase
{

    public function __construct($uid, $code = '', $state = '')
    {
        parent::__construct(3, $uid, $code, $state);
        $this->name = "Strava";
        $this->client_id = 0;
        $this->client_secret = '';
        $this->scope = 'read_all,activity:read_all';
        $this->auth_url = "https://www.strava.com/oauth/authorize";
        $this->auth_me_url = 'https://www.strava.com/oauth/token';
        $this->validity_tag = 'expires_at';
  }

    public function getMe()
    {
        if ($this->isAccessTokenValid()) {
            $request_headers = $this->prepareBearer();
            $this->cc->clearParameters();
            $this->cc->setURL('https://www.strava.com/api/v3/athlete/activities');
            $this->cc->setHeaders($request_headers);
            $rv = $this->cc->process();
            $jr = json_decode($rv);
            $rrv = '';
            foreach ($jr as $i)
            {
                $rrv .= '<b>Activity</b><br>';
                $rrv .= 'Name: ' . $i->{'name'} . '<br>';
                $rrv .= 'Distance: ' . $i->{'distance'} . '<br>';
                $rrv .= 'Start Time: ' . $i->{'start_date_local'} . '<br>';
                $rrv .= 'Moving Time: ' . $i->{'moving_time'} . '<br>';
                $rrv .= '<hr>';
            }
            return $rrv;
          } else {
              $this->refreshToken();
              return $this->getMe();
          }
    }

    protected function isAccessTokenValid()
    {
        /**
         * Strava does not provide an endpoint to check if the token is valid,
         * therefore we rely on the database data here.
         */
         return $this->u->getAuthTokenExpiry($this->app_id) > time();
    }

}
