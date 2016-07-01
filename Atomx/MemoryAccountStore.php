<?php namespace Atomx;

use Atomx\Exceptions\TotpRequiredException;
use Atomx\Resources\Login;

class MemoryAccountStore implements AccountStore {
    protected $token = null;
    protected $username, $password, $totp, $apiBase;

    /**
     * @param string|null $username
     * @param string|null $password
     * @param string|null $totp
     * @param string|null $apiBase
     */
    public function __construct($username = null, $password = null, $totp = null, $apiBase = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->totp = $totp;
        $this->apiBase = $apiBase;

        if ($this->apiBase == null)
            $this->apiBase = AtomxClient::API_BASE;
    }

    /**
     * @return null|string
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
            'totp' => $this->getTotp()
        ]);

        if ($response['totp_required'])
            throw new TotpRequiredException($response);

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

    public function getTotp()
    {
        return $this->totp;
    }

    /**
     * @return string
     */
    public function getApiBase()
    {
        return $this->apiBase;
    }
}
