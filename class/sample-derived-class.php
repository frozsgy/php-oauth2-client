<?php
require_once('oAuthBase.php');

class Sample extends oAuthBase
{

    public function __construct($uid, $code = '', $state = '')
    {
        /**
         * The first parameter for the parent is the app id in the database.
         * Replace it with the unique id.
         */
        parent::__construct(99, $uid, $code, $state);

        /**
         * A name for the App that you can use.
         */
        $this->name = "Sample";

        /**
         * client_id for the API.
         */
        $this->client_id = '1be8303c';

        /**
         * client_secret for the API.
         */
        $this->client_secret = '0e46e8f8';

        /**
         * The required scopes for the API.
         * Some providers require comma seperated lists,
         * some require space seperated ones.
         * Check documentation for details.
         */
        $this->scope = 'actions timeline';

        /**
         * The authorization URL, which is used as a redirect.
         */
        $this->auth_url = "https://myapp.com/oauth/authorize";

        /**
         * The authorization cURL address, which is used to get the
         * authorization code, tokens, and refresh token.
         */
        $this->auth_me_url = 'https://myapp.com/oauth/v1/token';

        /**
         * The URL to check the validity of the token.
         * Not all providers provide it.
         * If not provided, change the isAccessTokenValid() method as well.
         */
        $this->token_check_uri = 'https://myapp.com/oauth/v1/access-tokens/';

        /**
         * The JSON tag for expiry date in the responses.
         * Default is expires_in. Some providers use expires_at,
         * in that case, uncomment the following line and type the required tag.
         */
        //$this->validity_tag = 'expires_in';

  }

    /**
     * Example function to get profile data of the user.
     * Fill in the necessary parts.
     * Since the methods differ from provider to provider,
     * no general method was written.
     */
    public function getMe()
    {
        if ($this->isAccessTokenValid()) {
            return "Hello, my tokens work nicely";
        } else {
            $this->refreshToken();
            return $this->getMe();
        }
    }

    /**
     * Some providers do not follow the convention
     * {'token_type'} => Bearer
     * In that case, uncomment the following method to overrule
     * the method in the base class.
     */

    /*
    protected function parseToken($json_params, $type = '')
    {
        $params = json_decode($json_params);
        if (@$params->{'access_token'}) {
            $this->access_token = $params->{'access_token'};
            $this->expiry = $params->{$this->validity_tag} + time();
            if ($type == 'auth') {
                $this->refresh_token = $params->{'refresh_token'};
                $this->u->updateAuthToken($this->app_id, $this->access_token, $this->expiry);
                $this->u->updateRefreshToken($this->app_id, $this->refresh_token);
            }
            return true;
        } else {
            return false;
        }
    }
    */


    /**
     * The given example is based on an example API that provides
     * a API endpoint to check the validity of a token.
     * If it's not applicable, you should use your own data to check it.
     * Example implementation: saving the expiry dates on the DB.
     * The DB version can be seen at spotify.php
     * @returns boolean
     */

    protected function isAccessTokenValid()
    {
        $this->cc->clearParameters();
        $this->cc->setURL($this->token_check_uri . $this->u->getAuthToken($this->app_id));
        $rv = $this->cc->process();
        $jr = json_decode($rv);
        return (bool)$jr->{$this->validity_tag};
    }

}
