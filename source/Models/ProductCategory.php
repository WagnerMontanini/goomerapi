<?php

namespace WagnerMontanini\GoomerApi\Models;

use CoffeeCode\DataLayer\DataLayer;
use WagnerMontanini\GoomerApi\Models\Restaurant;

/**
 * Class ProductCategory
 * @package WagnerMontanini\GoomerApi\Models
 */
class ProductCategory extends DataLayer
{
    
    /**
     * ProductCategory constructor.
     */
    public function __construct()
    {
        parent::__construct(
            "products_categories",
            ["restaurant_id", "name"]
        );
    }

    public function restaurant(): ProductCategory
    {
        $this->restaurant = (new Restaurant())->findById($this->restaurant_id)->data();
        return $this;
    }

}