<?php

namespace App\Product\Application\Manager;

use App\Product\Domain\Entity\Product;
use App\Shared\Application\Service\UnsplashService;
use Doctrine\ORM\EntityManagerInterface;

class ProductManager
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
