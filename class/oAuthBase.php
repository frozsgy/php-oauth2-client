<?php

require_once('server.php');
require_once('curl.php');
require_once('t-oAuthBase.php');
require_once('integrations.php');

abstract class oAuthBase implements baseClass
{
    protected $client_id;
    protected $client_secret;
    protected $redirect_url;
    protected $scope;
    protected $name;
    protected $state;
    protected $app_id;
    protected $auth_url;
    protected $access_token;
    protected $expiry;
    protected $refresh_token;
    protected $uid;
    protected $auth_me_url;
    protected $u;
    protected $response_type;
    protected $cc;
    protected $token_check_uri;
    protected $validity_tag;


    public function __construct($app_id, $uid, $code = '', $state = '')
    {
        $this->app_id = $app_id;
        $this->redirect_url = ServerInfo::$root_url . 'authResponse.php?id=' . $app_id;

        /**
         * The $uid can be eliminated from the state calculator.
         * You can basically replace this with anything you'd like.
         */
        $this->state = hash('sha512', $uid . 'jotform-oauth-client');

        /**
         * The following fields that has the $uid that is related to
         * the integrations of the app with the user in the database.
         * Can be modified according to the actual usage.
         * If modified, necessary changes need to be made inside the
         * parseToken method.
         */
        $this->uid = $uid;
        $this->u = new Integrations($this->uid);

        if ($code == 'reauth') {
            $this->access_token = $state;
        } else {
            $this->code = $code;
        }
        $this->cc = new oAuthcURL($this->auth_me_url);
        $this->validity_tag = 'expires_in';
        $this->access_type = 'offline';
        $this->response_type = 'code';
    }

    public function redirectToAuth()
    {
        header('Location: ' . $this->generateAuthURL());
        exit();
    }

    public function setExpiry($expr)
    {
        $this->expiry = $expr;
    }

    public function getAccessToken()
    {
        $this->cc->clearParameters();
        $this->cc->setURL($this->auth_me_url);
        $this->cc->setPostFields($this->prepareAuthData());
        $rv = $this->cc->process();
        $res = $this->parseToken($rv, 'auth');
        return $res;
    }

    public function refreshToken()
    {
        $this->cc->clearParameters();
        $this->cc->setURL($this->auth_me_url);
        $this->cc->setPostFields($this->prepareRefreshData());
        $rv = $this->cc->process();
        $res = $this->parseToken($rv);
        return $res;
    }

    private function prepareAuthData()
    {
        $fields = array('client_id' => $this->client_id,
                        'client_secret' => $this->client_secret,
                        'grant_type' => 'authorization_code',
                        'code' => $this->code,
                        'redirect_uri' => $this->redirect_url,
                        'access_type' => $this->access_type
        );
        return $fields;
    }

    private function prepareRefreshData()
    {
        $this->refresh_token = $this->u->getRefreshToken($this->app_id);
        $fields = array('client_id' => $this->client_id,
                        'client_secret' => $this->client_secret,
                        'grant_type' => 'refresh_token',
                        'refresh_token' => $this->refresh_token,
                        'access_type' => $this->access_type
        );
        return $fields;
    }

    /**
     * The following method can be overridden in the case of
     * a provider not using Bearer convention, or anything else.
     * If you are not overriding it, you can make it private.
     */

    protected function parseToken($json_params, $type = '')
    {
        $params = json_decode($json_params);
        $token_type = @$params->{'token_type'}; //-> always Bearer
        if ($token_type == 'Bearer') {
            $this->access_token = $params->{'access_token'};
            $this->expiry = $params->{$this->validity_tag} + time();
            if ($type == 'auth') {
                $this->refresh_token = $params->{'refresh_token'};

                /**
                 * In the case of using different classes for integrations,
                 * do not forget to update the following method calls.
                 */
                $this->u->updateAuthToken($this->app_id, $this->access_token, $this->expiry);
                $this->u->updateRefreshToken($this->app_id, $this->refresh_token);
            }
            return true;
        } else {
            return false;
        }
    }

    protected function prepareBearer()
    {
        return array('Content-Type: application/json', 'Authorization: Bearer ' . $this->access_token);
    }

    protected function generateAuthURL()
    {
        $params = array('client_id' => $this->client_id,
                        'redirect_uri' => $this->redirect_url,
                        'scope' => $this->scope,
                        'state' => $this->state,
                        'access_type' => $this->access_type
        );
        if (!empty($this->response_type)) {
            $params['response_type'] = $this->response_type;
        }
        return $this->auth_url . '?' . http_build_query($params);
    }

    abstract protected function isAccessTokenValid();

}
