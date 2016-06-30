<?php namespace Atomx;

use Atomx\Resources\Login;

class MemoryAccountStore implements AccountStore {
    protected $token = null;
    protected $username, $password, $apiBase;

    /**
     * @param string $username
     * @param string $password
     * @param string $apiBase
     */
    public function __construct($username = null, $password = null, $apiBase = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->apiBase = $apiBase;

        if ($this->apiBase == null)
            $this->apiBase = AtomxClient::API_BASE;
    }

    /**
     * @return string|null
     */
    public function getToken()
    {
        if (is_null($this->token)) {
            $this->storeToken($this->getTokenFromLogin());
        }

        return $this->token;
    }

    protected function getLoginClient()
    {
        return new Login($this);
    }

    protected function getTokenFromLogin()
    {
        $response = $this->getLoginClient()->login([
            'email' => $this->getUsername(),
            'password' => $this->getPassword(),
        ]);

        return $response['auth_token'];
    }

    /**
     * @param string|null $token
     */
    public function storeToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getApiBase()
    {
        return $this->apiBase;
    }
}
