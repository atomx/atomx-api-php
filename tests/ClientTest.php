<?php namespace tests;

use Atomx\AccountStore;
use Atomx\Exceptions\ApiException;
use Atomx\Exceptions\TotpRequiredException;
use Atomx\MemoryAccountStore;
use Atomx\Resources\Advertiser;
use Atomx\Resources\Domain;
use Atomx\Resources\Login;
use Atomx\Resources\Totp;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;

class TestAccountStore implements AccountStore {
    private $token = 'TEST_TOKEN';

    public function getToken() { return $this->token; }
    public function storeToken($token) { $this->token = $token; }
    public function getUsername() { return ''; }
    public function getPassword() { return ''; }
    public function getApiBase() { return 'https://api.atomx.com/v3/'; }
}

class TestLoginAccountStore extends MemoryAccountStore {
    private $loginClient;
    public function setLoginClient($client) { $this->loginClient = $client; }
    protected function getLoginClient() { return $this->loginClient; }
}

class ClientTest extends \PHPUnit_Framework_TestCase {
    public function testDiscardInvalidToken()
    {
        $store = new TestAccountStore();
        $advertiser = new Advertiser($store);

        $mock = new Mock([
            "HTTP/1.1 401 Unknown Error\r\nContent-Length: 55\r\n\r\n{\"success\":false,\"error\":\"User unauthorized\",\"errno\":1}"
        ]);

        $advertiser->getClient()->getEmitter()->attach($mock);

        try {
            $advertiser->get(['limit' => 1, 'depth' => 0]);
        } catch (ApiException $e) {}

        $this->assertNull($store->getToken());
    }

    public function testRequestWithoutLogin()
    {
        $store = new TestAccountStore();
        $domain = new Domain($store);

        $history = new History();
        $mock = new Mock([ $this->getValidEmptyResponse() ]);

        $domain->getClient()->getEmitter()->attach($mock);
        $domain->getClient()->getEmitter()->attach($history);

        $domain->get(['limit' => 1, 'depth' => 0]);

        $this->assertArrayNotHasKey('Authorization', $history->getLastRequest()->getHeaders());
    }

    public function testRequestWithAuthToken()
    {
        $store = new TestAccountStore();
        $advertiser = new Advertiser($store);

        $history = new History();
        $mock = new Mock([ $this->getValidEmptyResponse() ]);

        $advertiser->getClient()->getEmitter()->attach($mock);
        $advertiser->getClient()->getEmitter()->attach($history);

        $advertiser->get(['limit' => 1, 'depth' => 0]);

        $this->assertArraySubset(['Authorization' => ['Bearer TEST_TOKEN']], $history->getLastRequest()->getHeaders());
    }


    public function testLogin()
    {
        $login = new Login(new TestAccountStore);

        $store = new TestLoginAccountStore();
        $store->setLoginClient($login);

        $advertiser = new Advertiser($store);

        $history = new History();
        $mock = new Mock([
            $this->getValidLoginResponse(),
            $this->getValidEmptyResponse()
        ]);

        $login->getClient()->getEmitter()->attach($mock);
        $login->getClient()->getEmitter()->attach($history);

        $advertiser->getClient()->getEmitter()->attach($mock);
        $advertiser->getClient()->getEmitter()->attach($history);

        $advertiser->get(['limit' => 1, 'depth' => 0]);


        $this->assertEquals(2, count($history));
        $loginRequest = $history->getIterator()[0]['request'];
        $this->assertArrayNotHasKey('Authorization', $loginRequest->getHeaders());
        $this->assertArraySubset(['Authorization' => ['Bearer LOGIN_TOKEN']], $history->getLastRequest()->getHeaders());
    }

    public function testTotpAccountStore()
    {
        $login = new Login(new TestAccountStore);

        $store = new TestLoginAccountStore();
        $store->setLoginClient($login);

        $advertiser = new Advertiser($store);

        $history = new History();
        $mock = new Mock([
            $this->getValidLoginResponse(true),
            $this->getValidEmptyResponse()
        ]);

        $login->getClient()->getEmitter()->attach($mock);
        $login->getClient()->getEmitter()->attach($history);

        $advertiser->getClient()->getEmitter()->attach($mock);
        $advertiser->getClient()->getEmitter()->attach($history);

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
      return new Response(200, [], Stream::factory("[]"));
    }

    private function getValidLoginResponse($totp = false)
    {
        $token = $totp ? 'TOTP_TOKEN' : 'LOGIN_TOKEN';
        $loginBody = '{"user":{"id":1},"resource":"auth_token","totp_required":' . var_export($totp, true) . ',' .
            '"message":"atomx (user id 1) logged in","success":true,"auth_token":"' . $token . '"}';

        return new Response(200,
            ['Content-Type' => 'application/json; charset=UTF-8'],
            Stream::factory($loginBody)
        );
    }
}
