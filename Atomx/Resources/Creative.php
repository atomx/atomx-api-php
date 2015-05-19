<?php namespace Atomx\Resources;

use Atomx\AtomxClient;
use InvalidArgumentException;

class Creative extends AtomxClient {
    protected $endpoint = 'creative';

    /**
     * @param $state State of the creative (active/inactive)
     */
    public function setState($state)
    {
        if (!in_array($state, ['active', 'inactive']))
            throw new InvalidArgumentException('API: Invalid state provided');

        $this->state = strtoupper($state);
    }

    public function setBanner($filename, $extension)
    {
        if (!in_array($extension, ['jpg', 'gif', 'png', 'swf']))
            throw new InvalidArgumentException('API: Invalid extension provided');

        if (!file_exists($filename))
            throw new InvalidArgumentException('API: Banner file does not exists');

        $this->content   = base64_encode(file_get_contents($filename));
        $this->extension = strtoupper($extension);
    }

    public function setTypes($types)
    {
        if (!is_array($types))
            $types = [$types];

        foreach ($types as $type) {
            if (!in_array($type, ['iframe', 'popup', 'popunder', 'javascript']))
                throw new InvalidArgumentException('API: Invalid banner type provided');
        }

        $this->types = array_map('strtoupper', $types);
    }

    public function setContentType($contentType)
    {
        if (!in_array($contentType, ['image', 'flash', 'iframe', 'javascript', 'vast']))
                throw new InvalidArgumentException('API: Invalid banner contentType provided');

        $this->contentType = strtoupper($contentType);
    }


    // url
    // javascript
    // title
    // mopup
    // click_url
}
