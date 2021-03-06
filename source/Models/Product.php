<?php

namespace WagnerMontanini\GoomerApi\Models;

use CoffeeCode\DataLayer\DataLayer;
use WagnerMontanini\GoomerApi\Models\Restaurant;
use WagnerMontanini\GoomerApi\Models\ProductCategory;

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
            ["restaurant_id", "category_id", "name", "price"]
        );
    }

    public function restaurant(): Product
    {
        $this->restaurant = (new Restaurant())->findById($this->restaurant_id)->data();
        return $this;
    }

    public function category(): Product
    {
        $this->category = (new ProductCategory())->findById($this->category_id)->data();
        return $this;
    }

}