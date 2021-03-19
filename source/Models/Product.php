<?php

namespace WagnerMontanini\GoomerApi\Models;

use CoffeeCode\DataLayer\DataLayer;
use WagnerMontanini\GoomerApi\Models\Restaurant;

/**
 * Class Product
 * @package WagnerMontanini\GoomerApi\Models
 */
class Product extends DataLayer
{
    
    /**
     * Product constructor.
     */
    public function __construct()
    {
        parent::__construct(
            "products",
            ["name", "price"]
        );
    }

    public function restaurant(): Product
    {
        $this->restaurant = (new Restaurant())->findById($this->restaurant_id)->data();
        return $this;
    }

}