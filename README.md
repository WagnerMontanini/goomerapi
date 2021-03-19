# GoomerApi Library Test

[![Maintainer](http://img.shields.io/badge/maintainer-@wagnermontanini-blue.svg?style=flat-square)](https://twitter.com/wagnermontanini)
[![Source Code](http://img.shields.io/badge/source-wagnermontanini/goomerapi-blue.svg?style=flat-square)](https://github.com/wagnermontanini/goomerapi)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/wagnermontanini/goomerapi.svg?style=flat-square)](https://packagist.org/packages/wagnermontanini/goomerapi)
[![Latest Version](https://img.shields.io/github/release/wagnermontanini/goomerapi.svg?style=flat-square)](https://github.com/wagnermontanini/goomerapi/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build](https://img.shields.io/scrutinizer/build/g/wagnermontanini/goomerapi.svg?style=flat-square)](https://scrutinizer-ci.com/g/wagnermontanini/goomerapi)
[![Quality Score](https://img.shields.io/scrutinizer/g/wagnermontanini/goomerapi.svg?style=flat-square)](https://scrutinizer-ci.com/g/wagnermontanini/goomerapi)
[![Total Downloads](https://img.shields.io/packagist/dt/wagnermontanini/goomerapi.svg?style=flat-square)](https://packagist.org/packages/cwagnermontanini/goomerapi)

###### GoomerApi Library is a RESTful API capable of managing the restaurants and products on your menu.

GoomerApi Library é uma API RESTful capaz de gerenciar os restaurantes e os produtos do seu cardápio.

Você pode saber mais **[clicando aqui](https://goomer.com.br)**.

### Highlights

- Simple installation (Instalação simples)
- Abstraction of all API methods (Abstração de todos os métodos da API)
- Composer ready and PSR-2 compliant (Pronto para o composer e compatível com PSR-2)

## Installation

Uploader is available via Composer:

```bash
"wagnermontanini/goomerapi": "^1.0"
```

or run

```bash
composer require wagnermontanini/goomerapi
```

## Documentation

###### For details on how to use, see a sample folder in the component directory. In it you will have an example of use for each class. It works like this:

Para mais detalhes sobre como usar, veja uma pasta de exemplo no diretório do componente. Nela terá um arquivo json para importação no postman com as rotas utilizadas. Ela funciona assim:

#### apache

```apacheconfig
RewriteEngine On
#Options All -Indexes
## ROUTER WWW Redirect.
#RewriteCond %{HTTP_HOST} !^www\. [NC]
#RewriteRule ^ https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
## ROUTER HTTPS Redirect
#RewriteCond %{HTTP:X-Forwarded-Proto} !https
#RewriteCond %{HTTPS} off
#RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
# ROUTER URL Rewrite
RewriteCond %{SCRIPT_FILENAME} !-f
RewriteCond %{SCRIPT_FILENAME} !-d
RewriteRule ^(.*)$ index.php?route=/$1 [L,QSA]
```

#### nginx

````nginxconfig
location / {
  if ($script_filename !~ "-f"){
    rewrite ^(.*)$ /index.php?route=/$1 break;
  }
}
````

#### CONFIGURAÇÕES Config.php:

```php
<?php
/**
 * DATABASE
 */
define("DATA_LAYER_CONFIG", [
    "driver" => "mysql",
    "host" => "localhost",
    "port" => "3306",
    "dbname" => "goomerapi",
    "username" => "root",
    "passwd" => "",
    "options" => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
]);

/**
 * PROJECT URLs
 */
define("CONF_URL_BASE", "https://www.goomerapi.com.br");
define("CONF_URL_TEST", "http://goomerapi.test");


/**
 * UPLOAD
 */
define("CONF_UPLOAD_DIR", "storage");
define("CONF_UPLOAD_IMAGE_DIR", "images");
define("CONF_UPLOAD_FILE_DIR", "files");
define("CONF_UPLOAD_MEDIA_DIR", "medias");

/**
 * IMAGES
 */
define("CONF_IMAGE_CACHE", CONF_UPLOAD_DIR . "/" . CONF_UPLOAD_IMAGE_DIR . "/cache");
define("CONF_IMAGE_SIZE", 2000);
define("CONF_IMAGE_QUALITY", ["jpg" => 75, "png" => 5]);
```

#### ROTAS index.php:

```php
<?php
ob_start();

require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/Config.php"; 

/**
 * BOOTSTRAP
 */

use CoffeeCode\Router\Router;

/**
 * API ROUTES
 * index
 */
$route = new Router(url(), ":");
$route->namespace("WagnerMontanini\GoomerApi\Api");

//restaurants
$route->group("/v1");
$route->get("/", "Restaurants:index");
$route->post("/", "Restaurants:create");
$route->get("/{restaurant_id}", "Restaurants:read");
$route->put("/{restaurant_id}", "Restaurants:update");
$route->delete("/{restaurant_id}", "Restaurants:delete");

//products
$route->get("/{restaurant_id}/", "Products:index");
$route->post("/{restaurant_id}/", "Products:create");
$route->get("/{restaurant_id}/{product_id}", "Products:read");
$route->put("/{restaurant_id}/{product_id}", "Products:update");
$route->delete("/{restaurant_id}/{product_id}", "Products:delete");


/**
 * ROUTE
 */
$route->dispatch();

/**
 * ERROR REDIRECT
 */
if ($route->error()) {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code(404);

    echo json_encode([
        "errors" => [
            "type " => "endpoint_not_found",
            "message" => "Não foi possível processar a requisição"
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

ob_end_flush();
```

## Contributing

Please see [CONTRIBUTING](https://github.com/wagnermontanini/goomerapi/blob/master/CONTRIBUTING.md) for details.

## Support

###### Security: If you discover any security related issues, please email wagnermontanini@hotmail.com.br instead of using the issue tracker.

Se você descobrir algum problema relacionado à segurança, envie um e-mail para wagnermontanini@hotmail.com.br em vez de usar o rastreador de problemas.

Thank you

## Credits

- [Wagner Montanini](https://github.com/wagnermontanini) (Developer)
- [All Contributors](https://github.com/wagnermontanini/goomerapi/contributors) (This Rock)

## License

The MIT License (MIT). Please see [License File](https://github.com/wagnermontanini/goomerapi/blob/master/LICENSE) for more information.