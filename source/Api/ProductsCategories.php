<?php

namespace WagnerMontanini\GoomerApi\Api;

use WagnerMontanini\GoomerApi\Support\Pager;
use WagnerMontanini\GoomerApi\Models\ProductCategory;
use WagnerMontanini\GoomerApi\Models\Restaurant;

/**
 * Class ProductsCategories
 * @package WagnerMontanini\GoomerApi
 */
class ProductsCategories extends GoomerApi
{
    /**
     * ProductsCategories constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * list all products_categories
     * @param array $data
     * @throws \Exception
     */
    public function index(array $data): void
    {   
        $values = $this->headers;

        if (empty($data["restaurant_id"]) || !$restaurant_id = filter_var($data["restaurant_id"], FILTER_VALIDATE_INT) ) {
            $this->call(
                400,
                "invalid_data",
                "É preciso informar o ID do restaurante para verificar as categorias"
            )->back();
            return;
        }

        //get products_categories
        $products_categories = (new ProductCategory())->find("restaurant_id=:restaurant_id","restaurant_id={$restaurant_id}");
        
        if (!$products_categories->count()) {
            $this->call(
                404,
                "not_found",
                "Nada encontrado para sua busca."
            )->back(["results" => 0]);
            return;
        }
        
        $page = (!empty($values["page"]) ? $values["page"] : 1);
        $pager = new Pager(url("/{restaurant_id}/categories"));
        $pager->pager($products_categories->count(), 2, $page);

        $response["results"] = $products_categories->count();
        $response["page"] = $pager->page();
        $response["pages"] = $pager->pages();

        foreach ($products_categories->limit($pager->limit())->offset($pager->offset())->order("name ASC")->fetch(true) as $product_category) {
            $response["products_categories"][] = $product_category->restaurant()->data();
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
        $request = $this->requestLimit("productsCategoriesCreate", 5, 60);
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
                "Você tentou cadastrar uma categoria em um restaurante que não existe"
            )->back();
            return;
        }

        if ( empty($data["name"]) ) {
            $this->call(
                400,
                "empty_data",
                "Para criar informe o nome da categoria"
            )->back();
            return ;
        }

        $product_category = new ProductCategory();
        $product_category->restaurant_id = $restaurant_id;
        $product_category->name = filter_var($data["name"], FILTER_SANITIZE_STRIPPED);
        $product_category->save();

        if($product_category->fail()){
            $this->call(
                400,
                "empty_data",
                $product_category->fail()->getMessage()
            )->back();
            return;
        }

        $this->back(["product_category" => $product_category->restaurant()->data()]);
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
                "É preciso informar o ID do restaurante para verificar a categoria"
            )->back();
            return;
        }

        
        if (empty($data["product_category_id"]) || !$product_category_id = filter_var($data["product_category_id"], FILTER_VALIDATE_INT) ) {
            $this->call(
                400,
                "invalid_data",
                "É preciso informar o ID da categoria que deseja consultar"
            )->back();
            return;
        }

        $product_category = (new ProductCategory())->find("restaurant_id = :restaurant_id AND id = :id",
                                "restaurant_id={$restaurant_id}&id=$product_category_id")->fetch();

        if (!$product_category) {
            $this->call(
                404,
                "not_found",
                "Você tentou acessar uma categoria que não existe neste restaurante"
            )->back();
            return;
        }

        $response["product_category"] = $product_category->restaurant()->data();
        
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
                "É preciso informar o ID do restaurante para atualizar a categoria"
            )->back();
            return;
        }

        if (empty($data["product_category_id"]) || !$product_category_id = filter_var($data["product_category_id"], FILTER_VALIDATE_INT) ) {
            $this->call(
                400,
                "invalid_data",
                "É preciso informar o ID da categoria que deseja atualizar"
            )->back();
            return;
        }

        $product_category = (new ProductCategory())->find("restaurant_id = :restaurant_id AND id = :id",
                                "restaurant_id={$restaurant_id}&id=$product_category_id")->fetch();

        if (!$product_category) {
            $this->call(
                404,
                "not_found",
                "Você tentou atualizar uma categoria de um restaurante que não existe"
            )->back();
            return;
        }
        
        $product_category->name = (!empty($data["name"])) ? filter_var($data["name"], FILTER_SANITIZE_STRIPPED) : $product_category->name;
        $product_category->save();

        if($product_category->fail()){
            $this->call(
                400,
                "empty_data",
                $product_category->fail()->getMessage()
            )->back();
            return;
        }

        $this->back(["product_category" => $product_category->restaurant()->data()]);
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
                "É preciso informar o ID do restaurante para deletar a categoria"
            )->back();
            return;
        }

        if (empty($data["product_category_id"]) || !$product_category_id = filter_var($data["product_category_id"], FILTER_VALIDATE_INT) ) {
            $this->call(
                400,
                "invalid_data",
                "É preciso informar o ID da categoria que deseja deletar"
            )->back();
            return;
        }

        $product_category = (new ProductCategory())->find("restaurant_id = :restaurant_id AND id = :id",
                                "restaurant_id={$restaurant_id}&id=$product_category_id")->fetch();

        if (!$product_category) {
            $this->call(
                404,
                "not_found",
                "Você tentou excluir uma categoria de um restaurante que não existe"
            )->back();
            return;
        }

        $product_category->destroy();
        $this->call(
            200,
            "success",
            "A categoria foi excluído com sucesso",
            "accepted"
        )->back();
    }
}