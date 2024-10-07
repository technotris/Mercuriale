<?php

namespace App\Tests;

use App\Entity\DTO\ProductDTO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = static::getContainer()->get('validator');
    }

    public function testDTO(): void
    {
        $name = 'Poireau masqué';
        $code = 'PHERO';
        $price = '1.23';
        $productDTO = new ProductDTO($name, $code, $price);
        $violations = $this->validator->validate($productDTO);
        $this->assertEmpty($violations);
    }

    public function testWrongPriceDTO(): void
    {
        $name = 'Poireau masqué';
        $code = 'PHERO';
        $wrongprice = 'a';
        $productpriceDTO = new ProductDTO($name, $code, $wrongprice);
        $violations = $this->validator->validate($productpriceDTO);
        // behavior: is not numeric and comparison letter > 1000
        $this->assertCount(2, $violations);
    }

    public function testLowPriceDTO(): void
    {
        $name = 'Poireau masqué';
        $code = 'PHERO';
        $wrongprice = '-1';
        $productpriceDTO = new ProductDTO($name, $code, $wrongprice);
        $violations = $this->validator->validate($productpriceDTO);
        $this->assertCount(1, $violations);
    }

    public function testHighPriceDTO(): void
    {
        $name = 'Poireau masqué';
        $code = 'PHERO';
        $wrongprice = '1001';
        $productpriceDTO = new ProductDTO($name, $code, $wrongprice);
        $violations = $this->validator->validate($productpriceDTO);
        $this->assertCount(1, $violations);
    }

    public function testMissingCodeDTO(): void
    {
        $name = 'Poireau masqué';
        $code = '';
        $price = '1.23';
        $productpriceDTO = new ProductDTO($name, $code, $price);
        $violations = $this->validator->validate($productpriceDTO);
        $this->assertCount(1, $violations);
    }

    public function testLongCodeDTO(): void
    {
        $name = 'Poireau masqué';
        $wrongcode = 'PHERORRRRRR';
        $price = '1.23';
        $productpriceDTO = new ProductDTO($name, $wrongcode, $price);
        $violations = $this->validator->validate($productpriceDTO);
        $this->assertCount(1, $violations);
    }

    public function testMissingNameDTO(): void
    {
        $name = '';
        $code = 'PHERO';
        $price = '1.23';
        $productpriceDTO = new ProductDTO($name, $code, $price);
        $violations = $this->validator->validate($productpriceDTO);
        $this->assertCount(1, $violations);
    }

    public function testPriceInCentsDTO(): void
    {
        $name = '';
        $code = 'PHERO';
        $price = '1.23';
        $productpriceDTO = new ProductDTO($name, $code, $price);
        $this->assertSame(123, $productpriceDTO->convertPriceInCents());

        $price = '1.234';
        $productpriceDTO = new ProductDTO($name, $code, $price);
        $this->assertSame(123, $productpriceDTO->convertPriceInCents());

        $price = '1.239';
        $productpriceDTO = new ProductDTO($name, $code, $price);
        $this->assertSame(123, $productpriceDTO->convertPriceInCents());

        $price = '1.239';
        $productpriceDTO = new ProductDTO($name, $code, $price);
        $this->assertNotEquals(124, $productpriceDTO->convertPriceInCents());
    }
}
