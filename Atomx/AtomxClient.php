<?php namespace Atomx;

use Exception;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;

class AtomxClient extends ApiClient {
    protected $apiBase = null;
    protected $id = null;
    protected $requiresLogin = true;

    /**
     * @var AccountStore Store the token for the application
     */
    private $accountStore;

    function __construct(AccountStore $accountStore, $idOrFields = null)
    {
        $this->apiBase = $accountStore->getApiBase();

        parent::__construct();

        $this->accountStore = $accountStore;


        if (is_array($idOrFields))
            $this->fields = $idOrFields;
        else if (is_numeric($idOrFields))
            $this->id = $idOrFields;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    protected function handleResponse(Response $response)
    {
        $code = $response->getStatusCode();

        if ($code == 200) {
            return json_decode($response->getBody()->getContents(), true);
        }

        if ($code == 401) {
            // Unauthorized, invalidate token
            $this->accountStore->storeToken(null);
        }

        throw new ApiException('Request failed, received the following status: ' .
            $response->getStatusCode() . ' Body: ' . $response->getBody()->getContents());
    }

    protected function getDefaultOptions()
    {
        $options = parent::getDefaultOptions();

        if ($this->requiresLogin)
            $options['headers'] = ['Authorization' => 'Bearer ' . $this->getToken()];

        return $options;
    }

    public function login()
    {
        $this->requiresLogin = false;

        try {
            $response = $this->postUrl('login', [
                'json' => [
                    'email'    => $this->accountStore->getUsername(),
                    'password' => $this->accountStore->getPassword()
                ]
            ]);
        } catch (ApiException $e) {
            $message = str_replace($e->getMessage(), $this->accountStore->getPassword(), '[redacted]');

            throw new ApiException('Unable to login to API! Message: ' . $message);
        }

        $this->requiresLogin = true;

        if ($response instanceof Stream) {
            $response = json_decode($response->getContents(), true);
        }

        if ($response['success'] !== true)
            throw new ApiException('Unable to login to API!');


        $token = $response['auth_token'];

        $this->accountStore->storeToken($token);

        return $response['user'];
    }

    private function getToken()
    {
        $token = $this->accountStore->getToken();

        if ($token !== null) {
            return $token;
        }

        $this->login();

        return $this->accountStore->getToken();
    }

    public function update()
    {
        if (is_null($this->id))
            throw new Exception('No id set to update!');

        return $this->putUrl($this->endpoint, [
            'query' => ['id' => $this->id],
            'json'  => $this->fields
        ]);
    }

    public function create()
    {
        return $this->post($this->fields);
    }

    public static function find($store, $id)
    {
        $model = new static($store);

        if (is_array($id))
            $id = implode(',', $id);

        return $model->get(['id' => $id]);
    }

    public static function all($store)
    {
        $model = new static($store);

        return $model->get();
    }
}
