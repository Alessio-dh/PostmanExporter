<?php


namespace Alessiodh\PostmanExporter;


class Headers
{
    private $excludedHeaders = ['host','content-length','content-type','user-agent'];
    private $headers;

    public function __construct($headers){
        $this->headers = $headers;
    }

    public function getPostmanFormattedHeaders(){
        $headers = [];
        foreach ($this->headers as $key => $header) {
            if(in_array(strtolower($key),$this->excludedHeaders)){
                continue;
            }

            if(!isset($header[0])){
                continue;
            }

            $headers[] = [
                'key' => $key,
                'value' => ltrim($header[0]),
                "disabled"=> false,
                "description" => null
            ];
        }

        return $headers;
    }
}
