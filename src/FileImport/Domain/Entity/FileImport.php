<?php

namespace App\FileImport\Domain\Entity;

use App\Product\Domain\Entity\Supplier;
use App\Product\Domain\Entity\SupplierProduct;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity()]
#[ORM\UniqueConstraint(name: 'UNIQ_FILE_NAME', fields: ['filename'])]
class FileImport
{
    use TimestampableEntity;
    // @TODO: approveBy
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;
    #[Assert\File(mimeTypes: ['text/csv'])]
    #[Vich\UploadableField(mapping: 'imports', fileNameProperty: 'filename')]
    private ?File $importFile = null;
    #[ORM\Column(nullable: true)]
    private ?string $filename = null;
    #[ORM\ManyToOne(targetEntity: Supplier::class, inversedBy: 'imports')]
    private Supplier $supplier;
    // @TODO workflow
    /**
     * @var Collection<int, SupplierProduct>
     */
    #[ORM\OneToMany(targetEntity: SupplierProduct::class, mappedBy: 'import')]
    private Collection $items;
    #[ORM\Column]
    private string $status = 'draft';

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setFilename(?string $filename): void
    {
        $this->filename = $filename;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return Collection<int, SupplierProduct>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @param Collection<int, SupplierProduct> $items
     */
    public function setItems(Collection $items): void
    {
        $this->items = $items;
    }

    public function getSupplier(): Supplier
    {
        return $this->supplier;
    }

    public function setSupplier(Supplier $supplier): void
    {
        $this->supplier = $supplier;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $importFile
     */
    public function setImportFile(?File $importFile = null): void
    {
        $this->importFile = $importFile;

        if (null !== $importFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime();
        }
    }

    public function getImportFile(): ?File
    {
        return $this->importFile;
    }

    public function __toString(): string
    {
        return sprintf('#%d %s', $this->getId(), $this->getFilename());
    }
}
