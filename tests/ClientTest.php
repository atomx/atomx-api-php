<?php namespace tests;

use Atomx\TokenStore;
use Atomx\Exceptions\ApiException;
use Atomx\Exceptions\TotpRequiredException;
use Atomx\LoginTokenStore;
use Atomx\Resources\Advertiser;
use Atomx\Resources\Domain;
use Atomx\Resources\Login;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class TestTokenStore implements TokenStore {
    private $token = 'TEST_TOKEN';

    public function getToken() { return $this->token; }
    public function storeToken($token) { $this->token = $token; }
    public function getUsername() { return ''; }
    public function getPassword() { return ''; }
    public function getApiBase() { return 'https://api.atomx.com/v3/'; }
}

class TestLoginTokenStore extends LoginTokenStore {
    private $loginClient;
    public function setLoginClient($client) { $this->loginClient = $client; }
    protected function getLoginClient() { return $this->loginClient; }
}

class ClientTest extends \PHPUnit_Framework_TestCase {
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTokenEndpointWithoutAS()
    {
        new Advertiser();
    }

    public function testDiscardInvalidToken()
    {
        $mock = new MockHandler([
            new Response(401, [], "{\"success\":false,\"error\":\"User unauthorized\",\"errno\":1}")
        ]);

        $handler = HandlerStack::create($mock);

        $store = new TestTokenStore();
        $advertiser = new Advertiser($store, null, $handler);

        try {
            $advertiser->get(['limit' => 1, 'depth' => 0]);
        } catch (ApiException $e) {}

        $this->assertNull($store->getToken());
    }

    public function testRequestWithAuthToken()
    {
        $historyContainer = [];
        $history = Middleware::history($historyContainer);
        $mock = new MockHandler([ $this->getValidEmptyResponse() ]);

        $handler = HandlerStack::create($mock);
        $handler->push($history);

        $store = new TestTokenStore();
        $advertiser = new Advertiser($store, null, $handler);

        $advertiser->get(['limit' => 1, 'depth' => 0]);

        $this->assertCount(1, $historyContainer);
        $request = $historyContainer[0]['request'];
        $this->assertEquals('Bearer TEST_TOKEN', $request->getHeaderLine('Authorization'));
    }
    public function testLogin()
    {
        $historyContainer = [];
        $history = Middleware::history($historyContainer);
        $mock = new MockHandler([
            $this->getValidLoginResponse(),
            $this->getValidEmptyResponse()
        ]);

        $handler = HandlerStack::create($mock);
        $handler->push($history);

        $login = new Login(new TestTokenStore, null, $handler);

        $store = new TestLoginTokenStore();
        $store->setLoginClient($login);

        $advertiser = new Advertiser($store, null, $handler);

        $advertiser->get(['limit' => 1, 'depth' => 0]);

        $this->assertCount(2, $historyContainer);
        $this->assertArrayNotHasKey('Authorization', $historyContainer[0]['request']->getHeaders());
        $this->assertArraySubset(['Authorization' => ['Bearer LOGIN_TOKEN']], $historyContainer[1]['request']->getHeaders());
    }

    public function testTotpTokenStore()
    {
        $historyContainer = [];
        $history = Middleware::history($historyContainer);
        $mock = new MockHandler([
            $this->getValidLoginResponse(true),
            $this->getValidEmptyResponse()
        ]);

        $handler = HandlerStack::create($mock);
        $handler->push($history);

        $login = new Login(new TestTokenStore, null, $handler);

        $store = new TestLoginTokenStore();
        $store->setLoginClient($login);

        $advertiser = new Advertiser($store, null, $handler);

        $totpException = false;
        $token = '';

        try {
            $advertiser->get(['limit' => 1, 'depth' => 0]);
        } catch (TotpRequiredException $e) {
            $totpException = true;
            $token = $e->getResponse()['auth_token'];
        }

        $this->assertTrue($totpException);
        $this->assertEquals('TOTP_TOKEN', $token);
    }

    private function getValidEmptyResponse()
    {
      return new Response(200, [], "[]");
    }

    private function getValidLoginResponse($totp = false)
    {
        $token = $totp ? 'TOTP_TOKEN' : 'LOGIN_TOKEN';
        $loginBody = '{"user":{"id":1},"resource":"auth_token","totp_required":' . var_export($totp, true) . ',' .
            '"message":"atomx (user id 1) logged in","success":true,"auth_token":"' . $token . '"}';

        return new Response(200,
            ['Content-Type' => 'application/json; charset=UTF-8'],
            $loginBody
        );
    }
}
