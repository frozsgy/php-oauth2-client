<?php
require_once('oAuthBase.php');
require_once('google/vendor/autoload.php');

class Google extends oAuthBase
{
    private $GoogleAPI;

    public function __construct($uid, $code = '', $state = '')
    {
        parent::__construct(2, $uid, $code, $state);
        $this->name = "Google";
        $this->client_id = '';
        $this->client_secret = '';
        $this->scope = '';
        $this->GoogleAPI = new Google_Client();
        $this->GoogleAPI->setClientId($this->client_id);
        $this->GoogleAPI->setClientSecret($this->client_secret);
        $this->GoogleAPI->setApplicationName($this->name);
        $this->GoogleAPI->setRedirectUri($this->redirect_url);
        $this->GoogleAPI->addScope($this->scope);
        $this->GoogleAPI->setState($this->state);
        $this->GoogleAPI->setAccessType($this->access_type);
        /**
         * The following line can uncommented if you would like to
         * force approval every time the user logs in.
         */
        //$this->GoogleAPI->setApprovalPrompt('force');
        $this->auth_url = $this->GoogleAPI->createAuthUrl();
        if ($code == 'reauth') {
          $this->GoogleAPI->setAccessToken($state);
        }
        $this->token_check_uri = 'https://www.googleapis.com/oauth2/v3/tokeninfo';
        $this->validity_tag = 'expires_in';
    }

    protected function generateAuthURL()
    {
        return $this->auth_url;
    }

    public function printAuth()
    {
        print $this->auth_url;
    }

    public function getAccessToken()
    {
        $tk = $this->GoogleAPI->fetchAccessTokenWithAuthCode($this->code);
        if ($tk) {
            $this->access_token = $tk["access_token"];
            $this->GoogleAPI->setAccessToken($tk);
            $this->expiry = time() + 3600;
            $u = new Integrations($this->uid);
            $u->updateAuthToken($this->app_id, $this->access_token, $this->expiry);
            $this->GoogleAPI->authenticate($this->code);
            return true;
        } else {
            return false;
        }
    }

    public function refreshToken()
    {
        /**
         * Since Google automatically refreshes the tokens,
         * this method is extremely simple.
         */
        $this->getAccessToken();
    }

    public function getMe()
    {
        $oac = new Google_Service_Oauth2($this->GoogleAPI);
        $me = $oac->userinfo_v2_me->get();
        $myData = 'User ID: ' . $me['id'];
        $myData .= '<br>Full name: ' . $me['givenName'] . ' ' . $me['familyName'];
        $myData .= '<br>E-mail: ' . $me['email'];
        $myData .= '<br>Photo: <img src="' . $me['picture'] . '">';
        return $myData;
    }

    protected function isAccessTokenValid()
    {
        $this->cc->clearParameters();
        $this->cc->setURL($this->token_check_uri);
        $getArray = array('access_token' => $this->u->getAuthToken($this->app_id));
        $this->cc->setGetFields($getArray);
        $rv = $this->cc->process();
        $jr = json_decode($rv);
        return (bool)$jr->{$this->validity_tag};
    }

}
