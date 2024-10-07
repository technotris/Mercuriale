<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Manager\ProductManager;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProductController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['name', 'code', 'description']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $fillImage = Action::new('updateImage', 'Update image')
                ->linkToCrudAction('updateImage')->displayIf(fn (Product $product) => is_null($product->getImage())
                );

        return $actions
            ->remove(Crud::PAGE_DETAIL, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $fillImage)
        ;
    }

    public function updateImage(AdminContext $context, ProductManager $productManager, AdminUrlGenerator $adminUrlGenerator): RedirectResponse
    {
        $product = $context->getEntity()->getInstance();
        if (is_null($product->getImage())) {
            $productManager->updateImage($product);
        } else {
            $this->addFlash('error', 'validation impossible');
        }

        $url = $adminUrlGenerator->setAction(Action::DETAIL)
            ->setEntityId($context->getEntity()->getPrimaryKeyValue())
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextField::new('code'),
            ImageField::new('image')->hideOnForm(),
            TextareaField::new('description'),
        ];
    }
}
