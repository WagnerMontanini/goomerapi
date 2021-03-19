<?php

namespace WagnerMontanini\GoomerApi\Models;

use CoffeeCode\DataLayer\DataLayer;

/**
 * Class Restaurant
 * @package WagnerMontanini\GoomerApi\Models
 */
class Restaurant extends DataLayer
{
    
    /**
     * Restaurant constructor.
     */
    public function __construct()
    {
        parent::__construct(
            "restaurants",
            ["name", "address"]
        );
    }

}