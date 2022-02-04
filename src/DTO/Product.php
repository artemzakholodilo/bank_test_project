<?php

namespace App\DTO;

/**
 * Class Product
 * @package App\DTO
 */
class Product
{
    public const TYPE_PRIVATE = 'private';

    public const TYPE_BUSINESS = 'business';

    public \DateTime $date;

    public $privacy;

    public $type;

    public $amount;

    public $currency;

    public function __construct(

    ) {

    }
}