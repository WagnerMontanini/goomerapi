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
$route->get("/restaurants", "Restaurants:index");
$route->post("/restaurants", "Restaurants:create");
$route->get("/restaurants/{restaurant_id}", "Restaurants:read");
$route->put("/restaurants/{restaurant_id}", "Restaurants:update");
$route->delete("/restaurants/{restaurant_id}", "Restaurants:delete");

//categories
$route->get("/restaurants/{restaurant_id}/categories", "ProductsCategories:index");
$route->post("/restaurants/{restaurant_id}/categories", "ProductsCategories:create");
$route->get("/restaurants/{restaurant_id}/categories/{product_category_id}", "ProductsCategories:read");
$route->put("/restaurants/{restaurant_id}/categories/{product_category_id}", "ProductsCategories:update");
$route->delete("/restaurants/{restaurant_id}/categories/{product_category_id}", "ProductsCategories:delete");

//products
$route->get("/restaurants/{restaurant_id}/products", "Products:index");
$route->post("/restaurants/{restaurant_id}/products", "Products:create");
$route->get("/restaurants/{restaurant_id}/products/{product_id}", "Products:read");
$route->put("/restaurants/{restaurant_id}/products/{product_id}", "Products:update");
$route->delete("/restaurants/{restaurant_id}/products/{product_id}", "Products:delete");


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