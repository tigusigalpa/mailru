<?php
/**
 * PHP handler of Mail.ru for business (https://biz.mail.ru) Sendbox service API
 *
 * @link https://biz.mail.ru/sendbox/
 * @link https://help.mail.ru/biz/sendbox/api/description
 * @license MIT
 * @author Igor Sazonov <sovletig@gmail.com>
 */

namespace Tigusigalpa\MailRu\Sendbox;

use GuzzleHttp\Client;

abstract class Sendbox
{
    /**
     * API ID
     *
     * @access protected
     * @var string
     */
    protected $id = '';

    /**
     * API Secret
     *
     * @access protected
     * @var string
     */
    protected $secret = '';

    /**
     * Access token
     *
     * @access protected
     * @var string
     */
    protected $token = '';

    /**
     * All errors for debugging
     *
     * @access protected
     * @var array
     */
    protected $errors = [];

    /**
     * API language
     *
     * @access protected
     * @var string
     */
    protected $lang = 'ru';

    /**
     * API url
     */
    const API_URL = 'https://mailer-api.i.bizml.ru';

    /**
     * Available languages
     */
    const LANGS = ['ru', 'en', 'ua'];

    /**
     * Available currencies
     */
    const CURRENCIES = ['RUB', 'USD', 'UAH'];

    public function __construct($id, $secret)
    {
        $this->id = $id;
        $this->secret = $secret;
        if (!$this->token) {
            $this->token = $this->getToken();
        }
    }

    /**
     * Set Mail.ru Sendbox class language
     *
     * @param string $lang
     *
     * @return void
     */
    protected function setLang($lang)
    {
        if (in_array($lang, self::LANGS)) {
            $this->lang = $lang;
        }
    }

    /**
     * Set Mail.ru Sendbox class error
     *
     * @param string $message
     * @param int $code
     *
     * @return void
     */
    protected function setError($message, $code = 0)
    {
        if ($code) {
            $this->errors[$code] = $message;
        } else {
            $this->errors[] = $message;
        }
    }

    /**
     * Get class errors data array/string
     *
     * @param bool $print
     *
     * @return array|string
     */
    public function getErrors($print = false)
    {
        if ($this->errors) {
            $data = ['errors' => $this->errors];
            if ($print) {
                return \GuzzleHttp\json_encode($data);
            }
            return $data;
        }
        return [];
    }

    /**
     * Make a HTTP request
     *
     * @param string $path
     * @param array $data
     * @param bool $formParams
     * @param string $method
     * @param array $options
     * @param int $timeout
     * @param bool $handleResponse
     *
     * @return bool|mixed|\Psr\Http\Message\StreamInterface|string
     */
    protected function request(
        $path,
        array $data,
        $formParams = false,
        $method = 'post',
        array $options = [],
        $timeout = 5,
        $handleResponse = true
    ) {
        if (!isset($options['timeout'])) {
            $options['timeout'] = $timeout;
        }
        if ($this->token) {
            $options['headers']['Authorization'] = 'Bearer ' . $this->token;
        }
        $client = new Client($options);
        $path = self::API_URL . '/' . ltrim($path, '/');
        $response = null;
        $method = strtolower($method);
        switch ($method) {
            case 'post':
            case 'put':
            case 'patch':
            case 'delete':
            case 'get':
                try {
                    $response = call_user_func_array([$client, $method], [$path, !$formParams ? $data :
                        ['form_params' => $data]]);
                } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                    $response = \GuzzleHttp\json_decode($e->getResponse()->getBody(), 1);
                    $this->setError($response['message'], $response['error_code']);
                }
                break;
        }
        if ($response && ($response instanceof \GuzzleHttp\Psr7\Response)) {
            $statusCode = $response->getStatusCode();
            if ($statusCode == 200) {
                $body = $response->getBody();
                return $handleResponse ? $this->handleResponse($body) : $body;
            } else {
                $this->setError('Error status code: ' . $statusCode, $statusCode);
            }
        }
        return '';
    }

    /**
     * Handle HTTP request
     *
     * @param string $path
     * @param string $method
     * @param array $data
     * @param bool $result
     *
     * @return array|bool|mixed|\Psr\Http\Message\StreamInterface|string
     */
    protected function handleRequest($path, $method, array $data = [], $result = false)
    {
        if ($response = $this->request($path, $data, true, $method)) {
            if (!$result) {
                return $response;
            } else {
                if (isset($response['result']) && $response['result'] === true) {
                    return true;
                } else {
                    $this->setError(\GuzzleHttp\json_encode($response));
                }
            }
        }
        return $this->getErrors();
    }

    /**
     * Handle response
     *
     * @param string $response
     *
     * @return bool|array
     */
    protected function handleResponse($response)
    {
        $response = \GuzzleHttp\json_decode($response, 1);
        if (is_array($response)) {
            return $response;
        }
        $this->setError('Response is not a valid JSON');
        return false;
    }

    /**
     * Get authentication token
     *
     * @return string
     */
    protected function getToken()
    {
        if ($response = $this->request(
            'oauth/access_token',
            [
                'grant_type' => 'client_credentials',
                'client_id' => $this->id,
                'client_secret' => $this->secret
            ],
            true
        )) {
            if (isset($response['access_token']) && !empty($response['access_token'])) {
                return $response['access_token'];
            } else {
                $this->setError('Empty token');
            }
        }
        return '';
    }
}
