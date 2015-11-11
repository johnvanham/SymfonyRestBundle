<?php

namespace LoftDigital\RestBundle\Test;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as FrameworkWebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Abstraction for functional tests
 *
 * All functional tests should extend this class
 *
 * @author Lukas Hajdu <lukas@loftdigital.com>
 * @copyright Loft Digital <www.loftdigital.com>, 2015
 * @package LoftDigital\RestBundle\Test
 */
abstract class WebTestCase extends FrameworkWebTestCase
{
    /** @var array */
    protected $headers = [];

    /** @var string */
    protected $token;

    /** @var string */
    protected $uri;

    /** @var array */
    protected $parameters = [];

    /** @var array */
    protected $files = [];

    /** @var Client */
    protected $client;

    /** @var string */
    protected $content;

    /** @var ContainerInterface */
    protected $container;

    const ACCEPT_JSON_V1_0 = 'application/json;version=1.0';
    const ACCEPT_XML_V1_0 = 'application/xml;version=1.0';

    const CONTENT_JSON = 'application/json';
    const CONTENT_XML = 'application/xml';

    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->container = static::createClient()->getKernel()->getContainer();
        $user = $this->container->getParameter('loft_digital_rest_oauth.user');
        $this->token = $user['token'];
    }

    /**
     * Returns basic URI
     *
     * @return string
     */
    abstract public function getBaseUri();

    /**
     * @param $route
     *
     * @return $this
     */
    public function setUri($route = '')
    {
        $this->uri =  $this->getBaseUri() . $route;

        return $this;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @return null|\Symfony\Component\DomCrawler\Crawler
     */
    public function getCrawler()
    {
        return $this->client->getCrawler();
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param array $parameters
     *
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param array $files
     *
     * @return $this
     */
    public function setFiles(array $files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * Set HTTP headers
     *
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * @param string $acceptType
     *
     * @return $this
     */
    public function setAcceptTypeHeader($acceptType = self::ACCEPT_JSON_V1_0)
    {
        $acceptTypeHeader = [
            'HTTP_ACCEPT' => $acceptType
        ];

        $this->setHeaders($acceptTypeHeader);

        return $this;
    }

    /**
     * @param string $contentType
     *
     * @return $this
     */
    public function setContentTypeHeader($contentType = self::CONTENT_JSON)
    {
        $contentTypeHeader = [
            'HTTP_CONTENT_TYPE' => $contentType
        ];

        $this->setHeaders($contentTypeHeader);

        return $this;
    }

    /**
     * @param $range
     * @param $max
     * @param $offset
     * @param $order
     *
     * @return $this
     */
    public function setRangeHeader($range, $max, $offset = 0, $order = self::ORDER_ASC)
    {
        $rangeHeader = [
            'HTTP_RANGE' => "$range;order=$order,max=$max,offset=$offset"
        ];

        $this->setHeaders($rangeHeader);

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set authorization header
     *
     * @return $this
     */
    public function authorize()
    {
        $authorizationHeader = [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->token
        ];

        $this->setHeaders($authorizationHeader);

        return $this;
    }

    /**
     * @param string $method
     *
     * @return null|Response
     */
    public function requestClient($method = self::METHOD_GET)
    {
        $this->client = $this->createClient();
        $this->client->request(
            $method,
            $this->uri,
            $this->parameters,
            $this->files,
            $this->headers,
            $this->content
        );

        return $this->client->getResponse();
    }

    /**
     * @param string $xml
     *
     * @return bool|\SimpleXMLElement
     */
    public function validateXmlResponse($xml)
    {
        try {
            libxml_use_internal_errors(true);
            $xmlData = new \SimpleXMLElement($xml);
        } catch (\Exception $e) {
            return false;
        }

        $this->assertEmpty(libxml_get_errors());

        return $xmlData;
    }

    /**
     * @param string $json
     *
     * @return bool|mixed
     */
    public function validateJsonResponse($json)
    {
        $data = json_decode($json);

        $this->assertNotFalse($data);

        if ($data === false) {
            return false;
        }

        return $data;
    }

    /**
     * @param \SimpleXMLElement $xmlData
     * @param string $jsonData
     */
    public function validateDataEquals($xmlData, $jsonData)
    {
        foreach ($xmlData as $item => $value) {
            $this->assertEquals((string) $value, $jsonData->{$item});
        }
    }
}
