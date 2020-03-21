<?php

namespace GorkaLaucirica\JiraApiClient;

use GorkaLaucirica\JiraApiClient\Auth\AuthInterface;
use GorkaLaucirica\JiraApiClient\Exception\BadRequestException;
use GuzzleHttp\Exception\ClientException;

class Client
{
    protected $baseUrl;

    protected $requestOptions = [];

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

        try {
            $response = $browser->get($url, $this->getRequestOptions());
        } catch (ClientException $e) {
            throw new BadRequestException($e->getMessage());
        }

        if ($response->getStatusCode() != 200) {
            throw new BadRequestException($url . ': ' . (string)$response->getBody());
        }

        return json_decode($response->getBody(), true);
    }

    public function post($resource, $content)
    {
        $browser = $this->getBrowserInstance();

        $url = $this->baseUrl . $resource;

        $options = $this->getRequestOptions();
        $options['body'] = json_encode($content);
        $options['headers']['Content-Type'] = 'application/json';

        $response = $browser->post($url, $options);

        if($response->getStatusCode() > 299) {
            throw new BadRequestException($url . ': ' . (string)$response->getBody());
        }

        return json_decode($response->getBody(), true);
    }

    public function put($resource, $content)
    {
        $browser = $this->getBrowserInstance();

        $url = $this->baseUrl . $resource;

        $options = $this->getRequestOptions();
        $options['body'] = json_encode($content);
        $options['headers']['Content-Type'] = 'application/json';

        $response = $browser->put($url, $options);

        if($response->getStatusCode() > 299) {
            throw new BadRequestException($url . ': ' . (string)$response->getBody());
        }

        return json_decode($response->getBody(), true);
    }

    public function getRequestOptions()
    {
        return (array) $this->requestOptions;
    }

    public function setRequestOptions( array $options )
    {
        $this->requestOptions = $options;
    }

    protected function getBrowserInstance()
    {
        if( $this->browser === null )
        {
            $this->browser = new \GuzzleHttp\Client();
        }

        return $this->browser;
    }
}
