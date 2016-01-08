<?php

namespace LoftDigital\RestBundle\Test;

use Rss\UserApiBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as FrameworkWebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;
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

    /** @var User */
    protected $testUser;

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
        $user = $this->container->getParameter('loft_digital_jwt_authentication.user');
        $this->testUser = $this->container
            ->get('rss_user_api.user_handler')
            ->get((new User())->setEmail($user['email']));

        $authResponse = $this
            ->setUri('/api/login_check', false)
            ->setContentTypeHeader(self::CONTENT_JSON)
            ->setContent(json_encode([
                '_username' => $user['email'],
                '_password' => $user['password'],
            ]))
            ->requestClient(self::METHOD_POST)
            ->getContent();

        $this->assertJson($authResponse);

        $this->token = json_decode($authResponse, true)['token'];
    }

    /**
     * Returns basic URI
     *
     * @return string
     */
    abstract public function getBaseUri();

    /**
     * Set URI
     *
     * @param string $route
     * @param bool $prependBaseUri
     *
     * @return $this
     */
    public function setUri($route = '', $prependBaseUri = true)
    {
        $this->uri =  ($prependBaseUri ? $this->getBaseUri() : '') . $route;

        return $this;
    }

    /**
     * Get HTTP client
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Get crawler
     *
     * @return null|Crawler
     */
    public function getCrawler()
    {
        return $this->client->getCrawler();
    }

    /**
     * Get container
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Set URL parameters
     *
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
     * Set request files
     *
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
     * Set Accept type HTTP header
     *
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
     * Set Content-Type HTTP header
     *
     * @param string $contentType
     *
     * @return $this
     */
    public function setContentTypeHeader($contentType = self::CONTENT_JSON)
    {
        $contentTypeHeader = [
            'CONTENT_TYPE' => $contentType
        ];

        $this->setHeaders($contentTypeHeader);

        return $this;
    }

    /**
     * Set Range HTTP header
     *
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
     * Get request content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set request content
     *
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
     * Request client
     *
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
     * Validate XML response data
     *
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

        $libXmlErrors = array_filter(libxml_get_errors(), function ($error) {
            return $error->code !== 522;
        });

        $this->assertEmpty($libXmlErrors);

        return $xmlData;
    }

    /**
     * Validate JSON response data
     *
     * @param string $json
     * @param bool $assoc
     *
     * @return bool|mixed
     *
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function validateJsonResponse($json, $assoc = false)
    {
        $data = json_decode($json, $assoc);

        $this->assertNotFalse($data);

        if ($data === false) {
            return false;
        }

        return $data;
    }
}
