<?php namespace Atomx\Resources;

use Atomx\AtomxClient;
use Atomx\Resources\Traits\NameTrait;
use Atomx\Resources\Traits\StateTrait;
use InvalidArgumentException;

class Creative extends AtomxClient {
    use NameTrait, StateTrait;

    protected $endpoint = 'creative';

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

        $this->content_type = strtoupper($contentType);
    }

    public function setCategory($category)
    {
        $this->category_id = $category;
    }

    public function setAttributes($attributes)
    {
        $attributes = ($attributes) ? $attributes : [];
        $this->attributes = $attributes;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setClickUrl($clickUrl)
    {
        $this->click_url = $clickUrl;
    }

    public function setJavascript($js)
    {
        $this->javascript = $js;
    }

    public function setMopup($mopup)
    {
        $this->mopup = $mopup;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setExtension($ext)
    {
        $this->extension = $ext;
    }

    public function setSize($size)
    {
        $this->size_id = $size;
    }
}
