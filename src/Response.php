<?php

namespace Desidus\Rudder;

class Response
{
    const HTTP_OK = 200;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_FOUND = 302;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    const HTTP_LOCKED = 423;      
    const HTTP_UNPROCESSABLE_ENTITY = 422;             

    private $status;
    private $headers = [];
    private $data;

    public function __construct($status, $headers, $data) 
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->data = $data;
    }

    public static function json($data, $status = self::HTTP_OK)
    {
        return new Response($status, [
            'Content-Type: application/json'
        ], 
            is_string($data) 
                ? $data 
                : json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE)
        );
    }

    public function apply()
    {
        http_response_code($this->status);

        foreach($this->headers as $header)
        {
            header($header);
        }

        echo $this->data;
    }

    public static function send($data, $status = self::HTTP_OK)
    {
        $request = App::request();

        http_response_code($status);

        if ($request->wantJSON())
        {
            header('Content-Type: application/json');
            echo is_string($data) ? $data : json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        }
        else
        {
            header('Content-Type: text/html');
            echo $data;
        }
    }

}

