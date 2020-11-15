<?php


namespace Alessiodh\PostmanExporter;


use Psr\Http\Message\RequestInterface;

class Request
{
    private $request;

    public function __construct(RequestInterface $request){
        $this->request = $request;
    }

    public function getPostmanFormattedRequest(){
        $newRequest = [];
        $uriData = $this->request->getUri();
        $newRequest['method'] = $this->request->getMethod();
        $newRequest['url'] = [
            'raw' => $uriData->getScheme().'://'.$uriData->getHost().$this->request->getRequestTarget(),
            'host' => explode('.',$uriData->getHost()),
            'port' => $uriData->getPort(),
            'query' => $this->getGetParameters(),
            'path' => $uriData->getPath(),
            'protocol' => $uriData->getScheme()
        ];

        return $newRequest;
    }

    private function getGetParameters(){
        $parameters = [];
        parse_str($this->request->getUri()->getQuery(),$getParams);
        if($getParams){
            foreach ($getParams as $key => $value) {
                $parameters[] = [
                    'key' => $key,
                    'value' => $value
                ];
            }
        }

        return $parameters;
    }
}
