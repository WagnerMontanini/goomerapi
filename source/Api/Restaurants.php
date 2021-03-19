<?php

namespace WagnerMontanini\GoomerApi\Api;

use WagnerMontanini\GoomerApi\Support\Pager;
use WagnerMontanini\GoomerApi\Models\Restaurant;

/**
 * Class Restaurants
 * @package WagnerMontanini\GoomerApi
 */
class Restaurants extends GoomerApi
{
    /**
     * Restaurants constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * list all restaurants
     */
    public function index(): void
    {
        //get restaurants
        $restaurants = (new Restaurant())->find();

        if (!$restaurants->count()) {
            $this->call(
                404,
                "not_found",
                "Nada encontrado para sua busca."
            )->back(["results" => 0]);
            return;
        }

        $page = (!empty($values["page"]) ? $values["page"] : 1);
        $pager = new Pager(url("/"));
        $pager->pager($restaurants->count(), 10, $page);

        $response["results"] = $restaurants->count();
        $response["page"] = $pager->page();
        $response["pages"] = $pager->pages();

        foreach ($restaurants->limit($pager->limit())->offset($pager->offset())->order("name ASC")->fetch(true) as $restaurant) {
            $response["restaurants"][] = $restaurant->data();
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
        $request = $this->requestLimit("restaurantsCreate", 5, 60);
        if (!$request) {
            return;
        }
        
        if ( empty($data["name"]) || empty($data["address"]) ) {
            $this->call(
                400,
                "empty_data",
                "Para criar informe o nome da empresa e o endereço"
            )->back();
            return ;
        }

        $restaurant = new Restaurant();
        $restaurant->name = filter_var($data["name"], FILTER_SANITIZE_STRIPPED);
        $restaurant->address = filter_var($data["address"], FILTER_SANITIZE_STRIPPED);
        $restaurant->image = (!empty($data["image"])) ? filter_var($data["image"], FILTER_SANITIZE_STRIPPED) : null;
        $restaurant->is_active = (!empty($data["is_active"])) ? filter_var($data["is_active"], FILTER_SANITIZE_STRIPPED) : 0;
        $restaurant->is_accepted = (!empty($data["is_accepted"])) ? filter_var($data["is_accepted"], FILTER_SANITIZE_STRIPPED) : 0;
        $restaurant->is_schedulable = (!empty($data["is_schedulable"])) ? filter_var($data["is_schedulable"], FILTER_SANITIZE_STRIPPED) : 0;
        $restaurant->schedule_data = (!empty($data["schedule_data"])) ? $data["schedule_data"] : null;
        $restaurant->save();

        if($restaurant->fail()){
            $this->call(
                400,
                "empty_data",
                $restaurant->fail()->getMessage()
            )->back();
            return;
        }

        $this->back(["restaurant" => $restaurant->data()]);
        return;
    }

    /**
     * @param array $data
     */
    public function read(array $data): void
    {
        if (empty($data["restaurant_id"]) || !$restaurant_id = filter_var($data["restaurant_id"], FILTER_VALIDATE_INT)) {
            $this->call(
                400,
                "invalid_data",
                "É preciso informar o ID do restaurante que deseja consultar"
            )->back();
            return;
        }

        $restaurant = (new Restaurant())->findById($restaurant_id);

        if (!$restaurant) {
            $this->call(
                404,
                "not_found",
                "Você tentou acessar um restaurante que não existe"
            )->back();
            return;
        }

        $response["restaurant"] = $restaurant->data();
        
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
                "Informe o ID do restaurante que deseja atualizar"
            )->back();
            return;
        }

        $restaurant = (new Restaurant())->findById($restaurant_id);

        if (!$restaurant) {
            $this->call(
                404,
                "not_found",
                "Você tentou atualizar um restaurante que não existe"
            )->back();
            return;
        }
        
        $restaurant->name = (!empty($data["name"])) ? filter_var($data["name"], FILTER_SANITIZE_STRIPPED) : $restaurant->name;
        $restaurant->address = (!empty($data["address"])) ? filter_var($data["address"], FILTER_SANITIZE_STRIPPED) : $restaurant->address;
        $restaurant->image = (!empty($data["image"])) ? filter_var($data["image"], FILTER_SANITIZE_STRIPPED) : $restaurant->image;
        $restaurant->is_active = (!empty($data["is_active"])) ? filter_var($data["is_active"], FILTER_SANITIZE_STRIPPED) : $restaurant->is_active;
        $restaurant->is_accepted = (!empty($data["is_accepted"])) ? filter_var($data["is_accepted"], FILTER_SANITIZE_STRIPPED) : $restaurant->is_accepted;
        $restaurant->is_schedulable = (!empty($data["is_schedulable"])) ? filter_var($data["is_schedulable"], FILTER_SANITIZE_STRIPPED) : $restaurant->is_schedulable;
        $restaurant->schedule_data = (!empty($data["schedule_data"])) ? $data["schedule_data"] : $restaurant->schedule_data;
        $restaurant->save();
        
        if($restaurant->fail()){
            $this->call(
                400,
                "empty_data",
                $restaurant->fail()->getMessage()
            )->back();
            return;
        }

        $this->back(["restaurant" => $restaurant->data()]);
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
                "Informe o ID do restaurante que deseja deletar"
            )->back();
            return;
        }

        $restaurant = (new Restaurant())->findById($restaurant_id);

        if (!$restaurant) {
            $this->call(
                404,
                "not_found",
                "Você tentou excluir um restaurante que não existe"
            )->back();
            return;
        }

        $restaurant->destroy();
        $this->call(
            200,
            "success",
            "O restaurante foi excluído com sucesso",
            "accepted"
        )->back();
    }
}