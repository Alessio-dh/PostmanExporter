<?php


namespace Alessiodh\PostmanExporter;


class Body
{
    private $body;

    public function __construct($body){
        $this->body = $body;
    }

    public function getPostmanFormattedBody(){
        try{
            $returnData = [
                'mode' => 'formdata',
                'disabled' => false
            ];

            $body = json_decode($this->body);
            foreach ($body as $key => $item) {
                $returnData['formdata'][] = [
                    'key' => $key,
                    'value' => $item,
                    'disabled' => false,
                    'description' => ''
                ];
            }
        }catch (\Exception $ex){
            $returnData = null;
        }

        return $returnData;
    }
}
