<?php

namespace Tigusigalpa\MailRu\Tests\Sendbox;

use GuzzleHttp\Exception\BadResponseException;
use PHPUnit\Framework\TestCase;

class SendboxTest extends TestCase
{
    private $http = null;

    private $timeout = 5;

    private $apiUrl = 'https://mailer-api.i.bizml.ru';

    public function setUp()
    {
        $this->http = new \GuzzleHttp\Client();
    }

    public function tearDown()
    {
        $this->http = null;
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testClientID()
    {
        return getenv('CLIENT_ID');
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testClientSecret()
    {
        return getenv('CLIENT_SECRET');
    }

    public function testGet()
    {
        $response = null;
        try {
            $response = $this->http->get($this->apiUrl);
        } catch (BadResponseException $e) {
        }
        $this->assertInstanceOf(\GuzzleHttp\Psr7\Response::class, $response);
        $code = $response->getStatusCode();
        $this->assertEquals(200, $code);
        if ($code !== 200) {
            $this->fail('Error response code on a GET request: ' . $code);
        }
    }

    /**
     * @depends testClientID
     * @depends testClientSecret
     * @depends testGet
     */
    public function testGetToken($clientId, $clientSecret)
    {
        $response = null;
        try {
            $response = $this->http->post(
                $this->apiUrl . '/oauth/access_token',
                [
                    'form_params' => [
                        'grant_type' => 'client_credentials',
                        'client_id' => $clientId,
                        'client_secret' => $clientSecret
                    ]
                ]
            );
        } catch (BadResponseException $e) {
            $exceptionBody = $e->getResponse()->getBody();
            $this->fail('Token request exception: ' . (string) $exceptionBody);
            $exceptionResponseBody = \GuzzleHttp\json_decode($exceptionBody, 1);
            $this->assertIsArray($exceptionResponseBody);
            if (is_array($exceptionResponseBody)
                && isset($exceptionResponseBody['error_code']) && !empty($exceptionResponseBody['error_code'])
                && isset($exceptionResponseBody['message']) && !empty($exceptionResponseBody['message'])) {
                $this->fail('Error response code on token request with code: "' . $exceptionResponseBody['error_code'] .
                        '" and message: "' . $exceptionResponseBody['message'] . '"');
            }
        }
        $this->assertInstanceOf(\GuzzleHttp\Psr7\Response::class, $response);
        $responseBody = \GuzzleHttp\json_decode($response->getBody(), 1);
        $this->assertIsArray($responseBody);
        $this->assertArrayHasKey('access_token', $responseBody);
        $this->assertNotEmpty($responseBody['access_token']);
        return $responseBody['access_token'];
    }

    /**
     * @depends testGetToken
     * @doesNotPerformAssertions
     */
    public function testRequest($token)
    {
        $timeout = getenv('TIMEOUT') ? getenv('TIMEOUT') : $this->timeout;
        $options = [
            'timeout' => $timeout,
            'header' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ];
        return new \GuzzleHttp\Client($options);
    }
}
