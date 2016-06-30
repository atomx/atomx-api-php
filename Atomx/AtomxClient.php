<?php namespace Atomx;

use Exception;
use GuzzleHttp\Message\Response;

class AtomxClient extends ApiClient {
    const API_BASE = 'https://api.atomx.com/v3/';
    protected $apiBase = null;
    protected $id = null;
    protected $requiresToken = true;

    /**
     * @var AccountStore Store the token for the application
     */
    protected $accountStore = null;

    /**
     * AtomxClient constructor.
     * @param AccountStore|null $accountStore
     * @param int|array $idOrFields
     * @param string $apiBase
     */
    function __construct($accountStore = null, $idOrFields = null)
    {
        if ($accountStore) {
            $this->accountStore = $accountStore;
            $this->apiBase = $accountStore->getApiBase();
        } else {
            $this->apiBase = AtomxClient::API_BASE;
        }

        parent::__construct();

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

        if ($code == 401 && $this->requiresToken) {
            // Unauthorized, invalidate token
            $this->accountStore->storeToken(null);
        }

        throw new ApiException('Request failed, received the following status: ' .
            $response->getStatusCode() . ' Body: ' . $response->getBody()->getContents());
    }

    protected function getDefaultOptions()
    {
        $options = parent::getDefaultOptions();

        if ($this->requiresToken)
            $options['headers'] = ['Authorization' => 'Bearer ' . $this->getToken()];

        return $options;
    }

    private function getToken()
    {
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
