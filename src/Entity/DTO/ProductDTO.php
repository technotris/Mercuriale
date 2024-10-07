<?php

namespace App\Entity\DTO;

use App\Entity\Product;
use Symfony\Component\Validator\Constraints as Assert;

class ProductDTO
{
    #[Assert\NotBlank(message: 'name is required')]
    public string $name;
    #[Assert\NotBlank(message: 'code is required')]
    #[Assert\Length(max: 6)]
    public string $code;

    #[Assert\NotBlank(message: 'price is required')]
    #[Assert\GreaterThan(0)]
    #[Assert\LessThan(1000)]
    #[Assert\Type('numeric')]
    public string $price;

    // some product name have detail of how many kg
    // is the price for the amount of kg in the product name or the 1kg
    // check if price are right
    public function __construct(string $name, string $code, string $price)
    {
        $this->name = $name;
        $this->code = $code;
        $this->price = $price;
    }

    public function convertToObject(): Product
    {
        $newProduct = new Product();
        $newProduct->setCode($this->code);
        $newProduct->setName($this->name);

        return $newProduct;
    }

    public function convertPriceInCents(): int
    {
        return (int) (((float) $this->price) * 100);
    }
}
