<?php

namespace Alessiodh\PostmanExporter;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class PostmanExporterMiddleware
{
    public function documentRequest(PostmanExporter $exporter){
        return function (callable $handler) use ($exporter) {
            return function (RequestInterface $request, array $options) use ($handler,$exporter) {
                if (array_key_exists('exporter_name', $options)){
                    $exporterDescription = (array_key_exists('exporter_description',$options) ? $options['exporter_description'] : '');
                    $exporter->addCall($options['exporter_name'],$exporterDescription,$request);
                }
                return $handler($request, $options);
            };
        };
    }

    public function documentResponse(PostmanExporter $exporter){
        return function (callable $handler) use ($exporter) {
            return function (
                RequestInterface $request,
                array $options
            ) use ($handler, $exporter) {
                $promise = $handler($request, $options);
                return $promise->then(
                    function (ResponseInterface $response) use ($exporter, $options) {
                        if (array_key_exists('exporter_name', $options)){
                            $exporter->addResponseToCall($options['exporter_name'],$response);
                        }
                        return $response;
                    }
                );
            };
        };
    }
}
