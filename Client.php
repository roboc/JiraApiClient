<?php

namespace GorkaLaucirica\JiraApiClient;

use Buzz\Browser;
use Buzz\Client\Curl;
use GorkaLaucirica\JiraApiClient\Auth\AuthInterface;
use GorkaLaucirica\JiraApiClient\Exception\BadRequestException;

class Client
{
    protected $baseUrl;

    protected $curlOptions = [];

    protected $browser;

    public function __construct( $baseUrl )
    {
        $this->baseUrl = $baseUrl;
    }

    public function get($resource, $query = array())
    {
        $browser = $this->getBrowserInstance();

        $url = $this->baseUrl . $resource;
        if(count($query) > 0) {
            $url .= "?";
        }

        foreach($query as $key => $value) {
            $url .= "$key=$value&";
        }

        $response = $browser->get($url);

        if($browser->getLastResponse()->getStatusCode() != 200) {
            throw new BadRequestException();
        }

        return json_decode($response->getContent(), true);
    }

    public function post($resource, $content)
    {
        $browser = $this->getBrowserInstance();

        $url = $this->baseUrl . $resource;

        $headers = array(
            'Content-Type' => 'application/json',
        );

        $response = $browser->post($url, $headers, json_encode($content));

        if($browser->getLastResponse()->getStatusCode() > 299) {
            throw new BadRequestException($response);
        }

        return json_decode($response->getContent(), true);
    }

    public function put($resource, $content)
    {
        $browser = $this->getBrowserInstance();

        $url = $this->baseUrl . $resource;

        $headers = array(
            'Content-Type' => 'application/json',
        );

        $response = $browser->put($url, $headers, json_encode($content));

        if($browser->getLastResponse()->getStatusCode() > 299) {
            throw new BadRequestException($response);
        }

        return json_decode($response->getContent(), true);
    }

    public function getCurlOptions()
    {
        return (array) $this->curlOptions;
    }

    public function setCurlOptions( array $options )
    {
        $this->curlOptions = $options;
    }

    protected function getCurlInstance()
    {
        $curl = new Curl();

        foreach( $this->getCurlOptions() as $option => $value )
        {
            $curl->setOption( $option, $value );
        }

        return $curl;
    }

    protected function getBrowserInstance()
    {
        if( $this->browser === null )
        {
            $this->browser = new Browser( $this->getCurlInstance() );
        }

        return $this->browser;
    }
} 
