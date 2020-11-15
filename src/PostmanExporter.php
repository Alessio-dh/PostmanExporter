<?php

namespace Alessiodh\PostmanExporter;

use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PostmanExporter
{
    private $calls;
    private $collectionName;

    public function __construct($collectionName)
    {
        $this->calls = collect();
        $this->collectionName = $collectionName;
    }

    public function addCall($name,$description, RequestInterface $request){
        $emptyCall = $this->getEmptyCallStructure();

        $emptyCall['name'] = $name;
        $emptyCall['description'] = $description;

        $requestObject = new Request($request);
        $emptyCall['request'] = $requestObject->getPostmanFormattedRequest();

        $headers = $request->getHeaders();
        if($headers){
            $headersObject = new Headers($headers);
            $emptyCall['request']['header'] = $headersObject->getPostmanFormattedHeaders();
        }

        $bodyData = $request->getBody()->getContents();
        if($bodyData){
            $newBody = new Body($bodyData);
            if($newBody) {
                $emptyCall['request']['body'] = $newBody->getPostmanFormattedBody();
            }
        }

        $this->calls->push($emptyCall);
        return $this;
    }

    public function addResponseToCall($name, ResponseInterface $response){
        foreach ($this->calls as $index => $call) {
            if ($call['name'] == $name) {
                $copy = $call;
                $headers = $response->getHeaders();
                $myHeaaders = new Headers($headers);
                $copy['response'][] =
                    [
                        'body' => $response->getBody()->getContents(),
                        'name' => $name,
                        'header' => $myHeaaders->getPostmanFormattedHeaders(),
                        'originalRequest' => $copy['request'],
                        'code' => $response->getStatusCode(),
                        'status' => ($response->getStatusCode() == 200 ? 'OK' : 'ERROR'),
                    ];
                $this->calls->forget($index);
                $this->calls->push($copy);
                break;
            }
        }
        return $this;
    }

    public function removeCall($name){
        $this->calls = $this->calls->filter(function ($call) use ($name) {
            return ((array_key_exists('name',$call) && $call['name'] != $name)? $call: false);
        });
        return $this;
    }

    public function exportToPostman(){
        $file = fopen(Storage::disk('public')->path('postman.json'),'wa+');
        fwrite($file, json_encode([
            'info' => ['name' => $this->collectionName, "schema" => "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"],
            'item' => $this->calls->values()
        ]));
        fclose($file);
        return $this;
    }

    public function getJsonOfCalls(){
        return json_encode([
            'info' => ['name' => $this->collectionName, "schema" => "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"],
            'item' => $this->calls->values()
        ]);
    }

    private function getEmptyCallStructure(){
        return [
            'name' => '',
            'request' => [
                'method' => '',
                'url' => [
                    'raw' => '',
                    'protocol' => 'https',
                    'port' => null,
                    'query' => null,
                    "host" => [],
                    'path' => ''
                ],
                'header' => [],
            ],
        ];
    }
}
