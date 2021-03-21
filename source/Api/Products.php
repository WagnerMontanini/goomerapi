<?php

namespace WagnerMontanini\GoomerApi\Api;

use WagnerMontanini\GoomerApi\Support\Pager;
use WagnerMontanini\GoomerApi\Models\Product;
use WagnerMontanini\GoomerApi\Models\Restaurant;
use WagnerMontanini\GoomerApi\Models\ProductCategory;

/**
 * Class Products
 * @package WagnerMontanini\GoomerApi
 */
class Products extends GoomerApi
{
    /**
     * Products constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * list all products
     * @param array $data
     * @throws \Exception
     */
    public function index(array $data): void
    {   
        $where = "";
        $params = "";
        $values = $this->headers;
        
        if (empty($data["restaurant_id"]) || !$restaurant_id = filter_var($data["restaurant_id"], FILTER_VALIDATE_INT) ) {
            $this->call(
                400,
                "invalid_data",
                "É preciso informar o ID do restaurante para verificar os produtos"
            )->back();
            return;
        }
        $category_id = explode('&',$_SERVER['REQUEST_URI']);
        
        if (!empty($category_id) && count($category_id) == 2) {
            $category = explode('=',$category_id[1]);
            if($category[0] == 'category_id'){
                $category_id = filter_var($category[1], FILTER_VALIDATE_INT);
                $where  = " AND category_id=:category_id";
                $params = "&category_id={$category_id}";
            }
        }

        //get products
        $products = (new Product())->find("restaurant_id=:restaurant_id{$where}","restaurant_id={$restaurant_id}{$params}");
        
        if (!$products->count()) {
            $this->call(
                404,
                "not_found",
                "Nada encontrado para sua busca."
            )->back(["results" => 0]);
            return;
        }

        $page = (!empty($values["page"]) ? $values["page"] : 1);
        $pager = new Pager(url("/restaurants/{restaurant_id}/products"));
        $pager->pager($products->count(), 10, $page);

        $response["results"] = $products->count();
        $response["page"] = $pager->page();
        $response["pages"] = $pager->pages();

        foreach ($products->limit($pager->limit())->offset($pager->offset())->order("name ASC")->fetch(true) as $product) {
            $response["products"][] = $product->restaurant()->category()->data();
        }

        $this->back($response);
        return;
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function create(array $data): void
    {
        $request = $this->requestLimit("productsCreate", 5, 60);
        if (!$request) {
            return;
        }

        if (empty($data["restaurant_id"]) || !$restaurant_id = filter_var($data["restaurant_id"], FILTER_VALIDATE_INT) ) {
            $this->call(
                400,
                "invalid_data",
                "É preciso informar o ID do restaurante para criar os produtos"
            )->back();
            return;
        }
        
        $restaurant = (new Restaurant())->findById($restaurant_id);

        if (!$restaurant) {
            $this->call(
                404,
                "not_found",
                "Você tentou cadastrar um produto em um restaurante que não existe"
            )->back();
            return;
        }

        if ( empty($data["category_id"]) || !$category_id = filter_var($data["category_id"], FILTER_VALIDATE_INT) || empty($data["name"]) || empty($data["price"]) ) {
            $this->call(
                400,
                "empty_data",
                "Para criar informe o ID da categoria do produto, o nome do produto e o preço do produto"
            )->back();
            return ;
        }

        $category = (new ProductCategory())->findById($category_id);

        if (!$category) {
            $this->call(
                404,
                "not_found",
                "Você tentou cadastrar um produto em uma categoria que não existe"
            )->back();
            return;
        }

        $product = new Product();
        $product->restaurant_id = $restaurant_id;
        $product->category_id = $category_id;
        $product->name = filter_var($data["name"], FILTER_SANITIZE_STRIPPED);
        $product->price = filter_var($data["price"], FILTER_SANITIZE_STRIPPED);
        $product->image = (!empty($data["image"])) ? filter_var($data["image"], FILTER_SANITIZE_STRIPPED) : null;
        $product->description = (!empty($data["description"])) ? filter_var($data["description"], FILTER_SANITIZE_STRIPPED) : null;
        $product->old_price = (!empty($data["old_price"])) ? filter_var($data["old_price"], FILTER_SANITIZE_STRIPPED) : 0.00;
        $product->save();

        if($product->fail()){
            $this->call(
                400,
                "empty_data",
                $product->fail()->getMessage()
            )->back();
            return;
        }

        $this->back(["product" => $product->restaurant()->category()->data()]);
        return;
    }

    /**
     * @param array $data
     */
    public function read(array $data): void
    {
        if (empty($data["restaurant_id"]) || !$restaurant_id = filter_var($data["restaurant_id"], FILTER_VALIDATE_INT) ) {
            $this->call(
                400,
                "invalid_data",
                "É preciso informar o ID do restaurante para verificar o produto"
            )->back();
            return;
        }

        
        if (empty($data["product_id"]) || !$product_id = filter_var($data["product_id"], FILTER_VALIDATE_INT) ) {
            $this->call(
                400,
                "invalid_data",
                "É preciso informar o ID do produto que deseja consultar"
            )->back();
            return;
        }

        $product = (new Product())->find("restaurant_id = :restaurant_id AND id = :id",
                                "restaurant_id={$restaurant_id}&id=$product_id")->fetch();

        if (!$product) {
            $this->call(
                404,
                "not_found",
                "Você tentou acessar um produto que não existe neste restaurante"
            )->back();
            return;
        }

        $response["product"] = $product->restaurant()->category()->data();
        
        $this->back($response);
    }

    /**
     * @param array $data
     */
    public function update(array $data): void
    {
        if ( empty($data["restaurant_id"]) || !$restaurant_id = filter_var($data["restaurant_id"], FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "invalid_data",
                "É preciso informar o ID do restaurante para atualizar o produto"
            )->back();
            return;
        }

        if (empty($data["product_id"]) || !$product_id = filter_var($data["product_id"], FILTER_VALIDATE_INT) ) {
            $this->call(
                400,
                "invalid_data",
                "É preciso informar o ID do produto que deseja atualizar"
            )->back();
            return;
        }

        $product = (new Product())->find("restaurant_id = :restaurant_id AND id = :id",
                                "restaurant_id={$restaurant_id}&id=$product_id")->fetch();

        if (!$product) {
            $this->call(
                404,
                "not_found",
                "Você tentou atualizar um produto de um restaurante que não existe"
            )->back();
            return;
        }
        
        $product->name = (!empty($data["name"])) ? filter_var($data["name"], FILTER_SANITIZE_STRIPPED) : $product->name;
        $product->price = (!empty($data["price"])) ? filter_var($data["price"], FILTER_SANITIZE_STRIPPED) : $product->price;
        $product->image = (!empty($data["image"])) ? filter_var($data["image"], FILTER_SANITIZE_STRIPPED) : $product->image;
        $product->description = (!empty($data["description"])) ? filter_var($data["description"], FILTER_SANITIZE_STRIPPED) : $product->description;
        $product->old_price = (!empty($data["old_price"])) ? filter_var($data["old_price"], FILTER_SANITIZE_STRIPPED) : $product->old_price;
        $product->save();

        if($product->fail()){
            $this->call(
                400,
                "empty_data",
                $product->fail()->getMessage()
            )->back();
            return;
        }

        $this->back(["product" => $product->restaurant()->category()->data()]);
        return;
    }

    /**
     * @param array $data
     */
    public function delete(array $data): void
    {
        if (empty($data["restaurant_id"]) || !$restaurant_id = filter_var($data["restaurant_id"], FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "invalid_data",
                "É preciso informar o ID do restaurante para deletar o produto"
            )->back();
            return;
        }

        if (empty($data["product_id"]) || !$product_id = filter_var($data["product_id"], FILTER_VALIDATE_INT) ) {
            $this->call(
                400,
                "invalid_data",
                "É preciso informar o ID do produto que deseja deletar"
            )->back();
            return;
        }

        $product = (new Product())->find("restaurant_id = :restaurant_id AND id = :id",
                                "restaurant_id={$restaurant_id}&id=$product_id")->fetch();

        if (!$product) {
            $this->call(
                404,
                "not_found",
                "Você tentou excluir um produto de um restaurante que não existe"
            )->back();
            return;
        }

        $product->destroy();
        $this->call(
            200,
            "success",
            "O produto foi excluído com sucesso",
            "accepted"
        )->back();
    }
}