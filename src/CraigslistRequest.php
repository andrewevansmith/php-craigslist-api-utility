<?php

namespace Craigslist;

class CraigslistRequest
{
    protected $city;
    protected $url;

    // For dynamically building the request URL
    protected $category;
    protected $query;

    // Flags the API to crawl further and get listing details
    protected $follow_links = false;

    /**
     * @param array $config associative array of configuration settings listed above
     */
    public function __construct( array $config=[] )
    {
        foreach ($config as $k=>$v) {
            $this->$k = $v;
        }
    }

    public function follow()
    {
        return $this->follow_links;
    }

    /**
     * @throws \Exception 
     * @return string The full URL of the request
     */
    public function url()
    {
        if (isset($this->url) && strlen($this->url) > 0) {
            $url =  'https://' . $this->city . '.craigslist.org/' . $this->url;
        } elseif (isset($this->category) && strlen($this->category) > 0) {
            $q = (isset($this->query) && strlen($this->query)) ? '&query=' . urlencode($this->query) : '';
            $url = 'https://' . urlencode($this->city) . '.craigslist.org/search/' . urlencode($this->category) . '?format=rss' . $q;
        } else {
            throw new \Exception('Inproper configuration, could not generate URL.');
        }
        return $url;
    }

}
