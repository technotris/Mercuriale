<?php

namespace App\Shared\Application\Controller\Admin;

use App\Catalog\Domain\Entity\SupplierProduct;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class SupplierProductController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SupplierProduct::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('import'),
            AssociationField::new('supplier'),
            AssociationField::new('product'),
            MoneyField::new('price')->setCurrency('EUR'),
        ];
    }
}
