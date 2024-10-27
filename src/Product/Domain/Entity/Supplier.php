<?php

namespace App\Product\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\FileImport\Domain\Entity\FileImport;
use App\Product\Domain\Entity\SupplierProduct;

#[ORM\Entity()]
#[ORM\UniqueConstraint(name: 'UNIQ_SUPPLIER_NAME', fields: ['name'])]
class Supplier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private string $name;
    /**
     * @var Collection<int, SupplierProduct>
     */
    #[ORM\OneToMany(targetEntity: SupplierProduct::class, mappedBy: 'supplier')]
    private Collection $products;

    /**
     * @var Collection<int, FileImport>
     */
    #[ORM\OneToMany(targetEntity: FileImport::class, mappedBy: 'supplier')]
    private Collection $imports;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->imports = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int, SupplierProduct>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param Collection<int, SupplierProduct> $products
     */
    public function setProducts(Collection $products): void
    {
        $this->products = $products;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int, FileImport>
     */
    public function getImports(): Collection
    {
        return $this->imports;
    }

    /**
     * @param Collection<int, FileImport> $imports
     */
    public function setImports(Collection $imports): void
    {
        $this->imports = $imports;
    }
}
