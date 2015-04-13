<?php namespace Atomx;

use Exception;
use GuzzleHttp\Message\Response;

/*
 * TODO: Ability to sync back from atomx to DA
 */

class AtomxClient extends ApiClient {
    protected $apiBase = null;
    protected $id = null;

    /**
     * @var AccountStore Store the token for the application
     */
    private $accountStore;
    private $shouldSendToken = true;

    function __construct(AccountStore $accountStore, $idOrFields = null)
    {
        parent::__construct();

        $this->apiBase = $accountStore->getApiBase();

        assert(isset($this->apiBase), 'Set the API endpoint before continuing');

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
        // TODO: Handle an invalid token/not logged in message
        // Probably disable exceptions and interpret the status code ourselves
        return json_decode(parent::handleResponse($response), true);
    }

    protected function getDefaultOptions()
    {
        $options = parent::getDefaultOptions();

        if ($this->shouldSendToken)
            $options['cookies'] = ['auth_tkt' => $this->getToken()];

        return $options;
    }

    private function login()
    {
        $this->shouldSendToken = false;

        $response = $this->getUrl('login', [
            'email'    => $this->accountStore->getUsername(),
            'password' => $this->accountStore->getPassword()
        ]);

        $this->shouldSendToken = true;

        if ($response['success'] !== true)
            throw new ApiException('Unable to login into Atomx API. Error: ' . $response['error']);

        $token = $response['auth_tkt'] . '!userid_type:int';

        $this->accountStore->storeToken($token);

        return $token;
    }

    private function getToken()
    {
        $token = $this->accountStore->getToken();

        if ($token !== null) {
            return $token;
        }

        return $this->login();
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
}
