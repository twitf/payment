<?php

namespace twitf\Payment;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

trait HttpRequest
{
    /**
     * 发送get请求
     * @param $uri
     * @param array $query
     * @param array $headers
     * @return mixed
     */
    protected function get($uri, $query = [], $headers = [])
    {
        return $this->request('get', $uri, [
            'headers' => $headers,
            'query' => $query,
        ]);
    }

    /**
     * 发送post请求
     * @param $uri
     * @param $data
     * @param array $options
     * @return mixed
     */
    protected function post($uri, $data, $options = [])
    {
        if (!is_array($data)) {
            $options['body'] = $data;
        } else {
            $options['form_params'] = $data;
        }

        return $this->request('post', $uri, $options);
    }

    /**
     * 发送请求
     * @param $method
     * @param $uri
     * @param array $options
     * @return mixed
     */
    protected function request($method, $uri, $options = [])
    {
        return $this->formatResponse($this->getHttpClient($this->getBaseOptions())->{$method}($uri, $options));
    }

    /**
     * 获取配置
     * @return array
     */
    protected function getBaseOptions()
    {
        $options = [
            'base_uri' => property_exists($this, 'baseUri') ? $this->baseUri : '',
            'timeout' => property_exists($this, 'timeout') ? $this->timeout : 5.0,
            'connect_timeout' => property_exists($this, 'connect_timeout') ? $this->connect_timeout : 5.0,
        ];
        return $options;
    }


    /**
     * 返回HttpClient对象
     * @param array $options
     * @return Client
     */
    protected function getHttpClient(array $options = [])
    {
        return new Client($options);
    }

    /**
     * 格式化响应
     * @param ResponseInterface $response
     * @return mixed|string
     */
    protected function formatResponse(ResponseInterface $response)
    {
        $contentType = $response->getHeaderLine('Content-Type');
        $contents = $response->getBody()->getContents();
        if (false !== stripos($contentType, 'json') || stripos($contentType, 'javascript')) {
            return json_decode($contents, true);
        } elseif (false !== stripos($contentType, 'xml')) {
            return json_decode(json_encode(simplexml_load_string($contents, 'SimpleXMLElement', LIBXML_NOCDATA), JSON_UNESCAPED_UNICODE), true);
        }

        return $contents;
    }
}
