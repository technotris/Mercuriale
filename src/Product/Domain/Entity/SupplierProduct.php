<?php

namespace App\Product\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use App\Product\Domain\Entity\Supplier;
use App\Product\Domain\Entity\Product;
use App\FileImport\Domain\Entity\FileImport;

#[ORM\Entity()]
class SupplierProduct
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;
    // stored in cents in case of operation like taxes
    #[ORM\Column(type: 'integer')]
    private int $price;

    #[ORM\ManyToOne(targetEntity: Supplier::class, inversedBy: 'products')]
    private Supplier $supplier;
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'listings')]
    private Product $product;
    #[ORM\ManyToOne(targetEntity: FileImport::class, inversedBy: 'items')]
    private FileImport $import;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getSupplier(): Supplier
    {
        return $this->supplier;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getImport(): FileImport
    {
        return $this->import;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    public function setSupplier(Supplier $supplier): void
    {
        $this->supplier = $supplier;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function setImport(FileImport $import): void
    {
        $this->import = $import;
    }

    public function __toString(): string
    {
        return sprintf('%s : %s€', $this->product->__toString(), $this->price / 100);
    }
}
