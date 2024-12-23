<?php

namespace App\Product\Application\Service;

use App\Catalog\Domain\Entity\Product;
use App\Shared\Application\Service\UnsplashService;
use Doctrine\ORM\EntityManagerInterface;

class UpdateProductImageService
{
    public function __construct(private EntityManagerInterface $em,
        private UnsplashService $unsplashService)
    {
    }

    public function updateImage(Product $product, bool $force = false): bool
    {
        if (!$force && !is_null($product->getImage())) {
            return false;
        }
        // first word is descriptive of the main type of the product
        $keyword = strtok($product->getName(), ' ');
        $imageUrl = $this->unsplashService->searchImage($keyword);
        $product->setImage($imageUrl);
        $this->em->flush();

        return true;
    }
}
