<?php

namespace App\Shared\Application\Controller\Admin;

use App\FileImport\Domain\Entity\FileImport;
use App\Product\Domain\Entity\Product;
use App\Product\Domain\Entity\Supplier;
use App\Product\Domain\Entity\SupplierProduct;
use App\Auth\Domain\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('@EasyAdmin/page/content.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Mercuriale')
//            ->setFaviconPath('favicon.ico')
            ->setTranslationDomain('admin');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToCrud('Users', null, User::class),
            MenuItem::linkToCrud('Products', null, Product::class),
            MenuItem::linkToCrud('Suppliers', null, Supplier::class),
            MenuItem::linkToCrud('SupplierProducts', null, SupplierProduct::class),
            MenuItem::linkToCrud('FileImports', null, FileImport::class),
        ];
    }
}
