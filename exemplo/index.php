<?php
ob_start();

require __DIR__ . "/../vendor/autoload.php";
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