<?php

namespace App\Catalog\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity()]
#[ORM\UniqueConstraint(name: 'UNIQ_PRODUCT_CODE', fields: ['code'])]
class Product
{
    use TimestampableEntity;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;
    #[Assert\NotBlank(message: 'code is required')]
    #[Assert\Length(max: 6)]
    #[ORM\Column(type: 'string', length: 6, unique: true)]
    private string $code;

    #[Assert\NotBlank(message: 'name is required')]
    #[ORM\Column]
    private string $name;
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $image;
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    /**
     * @var Collection<int, SupplierProduct>
     */
    #[ORM\OneToMany(targetEntity: SupplierProduct::class, mappedBy: 'product')]
    private Collection $listings;

    public function __construct()
    {
        $this->listings = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection<int, SupplierProduct>
     */
    public function getListings(): Collection
    {
        return $this->listings;
    }

    /**
     * @param Collection<int, SupplierProduct> $listings
     */
    public function setListings(Collection $listings): void
    {
        $this->listings = $listings;
    }

    public function __toString(): string
    {
        return sprintf('[%s] %s', $this->code, $this->name);
    }
}
