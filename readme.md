# Postman extractor

A package to transform GuzzleHttp calls to a postman collection in code.

## Installation
This can be done through composer:
```
composer require alessiodh/postmanexporter
```

## Usage
### Creating a new postman collection
```php
use Alessiodh\PostmanExporter\PostmanExporter;

$exporter = new PostmanExporter('My collection name');
```

### Adding Listeners for adding to call collection
PostmanExporter provides 2 middleware handlers. The request middleware is required, this will automaticly document the request to the call collection. The response middleware is optional and will add the response to the request. 

```php
$middleware = new PostmanExporterMiddleware();
$stack = new HandlerStack();
$stack->setHandler(new CurlHandler());
$stack->push($middleware->documentRequest($exporter));
$stack->push($middleware->documentResponse($exporter));

$client = new Client(['handler' => $stack]);
```

When the handlers are added, every request executed with that specific client will be documented.

### Adding names and descriptions to calls
`exporter_name` is the name that will be given to the call in postman this parameter is required to be provided or the call will not be registered. `exporter_description` is an optional parameter and will provide the description to the call.

```php
$resp = $client->get('https://google.com?foo=bar&t=b',
    [
        'exporter_name' => 'Get test',
        'exporter_description' => 'Will perform a get request to google with 2 get parameters'
    ]
);
```

### Exporting results
To export the collection you can either export it as a json document that can be directly imported in postman or get the JSON returned as a string.

#### Export to JSON document
This function will write the file to `Storage::disk('public')` as a file named `postman.json`. It will not look if the file exists but instead will jsut overwrite it.
```php
$exporter->exportToPostman();
```

#### Get JSON returned
```php
$exporter->getJsonOfCalls();
```

### Notice
This is still a simple implementation of exporting calls to postman. Every contribution is welcome.

