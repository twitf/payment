<?php
/**
 * Created by PhpStorm.
 * User: twitf
 * Date: 2018/8/22
 * Time: 15:28
 */

namespace twitf\Payment;

use GuzzleHttp\Client;

class Request
{
    public $client;


    /**
     * 初始化请求
     * HttpHelp constructor.
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function __construct()
    {
        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://httpbin.org',
            // You can set any number of default request options.
            'timeout' => 2.0,
        ]);

        $response = $this->client->request('GET', 'test');
        // Send a request to https://foo.com/root
        $response = $this->client->request('GET', '/root');
    }

    public static function request()
    {

    }


}
