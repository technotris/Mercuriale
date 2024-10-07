<?php

namespace App\Controller\Admin;

use App\Entity\FileImport;
use App\Manager\CSVMercurialeManager;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Workflow\WorkflowInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class FileImportController extends AbstractCrudController
{
    public function __construct(private WorkflowInterface $importValidation)
    {
    }

    public static function getEntityFqcn(): string
    {
        return FileImport::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $dryParse = Action::new('dryParse', 'dryparse')
                ->linkToCrudAction('dryParse');
        $validate = Action::new('validate', 'validate')
                ->linkToCrudAction('validate')->displayIf(fn (FileImport $fileImport) => $this->importValidation->can($fileImport, 'approve')
                );
        $reject = Action::new('reject', 'reject')
                ->linkToCrudAction('reject')->displayIf(fn (FileImport $fileImport) => $this->importValidation->can($fileImport, 'reject')
                );

        return $actions
                        ->remove(Crud::PAGE_INDEX, Action::DELETE)
                        ->remove(Crud::PAGE_INDEX, Action::EDIT)
                        ->remove(Crud::PAGE_DETAIL, Action::DELETE)
                        ->remove(Crud::PAGE_DETAIL, Action::EDIT)
                        ->add(Crud::PAGE_INDEX, Action::DETAIL)
                        ->add(Crud::PAGE_INDEX, $dryParse)
                        ->add(Crud::PAGE_DETAIL, $validate)
                        ->add(Crud::PAGE_DETAIL, $reject)
        ;
    }

    public function dryParse(AdminContext $context, CSVMercurialeManager $manager, AdminUrlGenerator $adminUrlGenerator): RedirectResponse
    {
        $fileImport = $context->getEntity()->getInstance();
        $manager->parse($fileImport);
        $url = $adminUrlGenerator->setAction(Action::DETAIL)
            ->setEntityId($context->getEntity()->getPrimaryKeyValue())
            ->generateUrl();

        return $this->redirect($url);
    }

    public function validate(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $em): RedirectResponse
    {
        $fileImport = $context->getEntity()->getInstance();
        if ($this->importValidation->can($fileImport, 'approve')) {
            $this->importValidation->apply($fileImport, 'approve');
            $em->flush();
        } else {
            $this->addFlash('error', 'validation impossible');
        }

        $url = $adminUrlGenerator->setAction(Action::DETAIL)
            ->setEntityId($context->getEntity()->getPrimaryKeyValue())
            ->generateUrl();

        return $this->redirect($url);
    }

    public function reject(AdminContext $context, AdminUrlGenerator $adminUrlGenerator, EntityManagerInterface $em): RedirectResponse
    {
        $fileImport = $context->getEntity()->getInstance();
        if ($this->importValidation->can($fileImport, 'reject')) {
            $this->importValidation->apply($fileImport, 'reject');
            $em->flush();
        } else {
            $this->addFlash('error', 'refus impossible');
        }
        $url = $adminUrlGenerator->setAction(Action::DETAIL)
            ->setEntityId($context->getEntity()->getPrimaryKeyValue())
            ->generateUrl();

        return $this->redirect($url);
    }

    // @TODO add possibility to reparse file ?
    // @TODO add possibility to delete if mistaken ? Then delete the message too or add check on the parsing for existence of entity and file
    //
    // pre persist add check if formatted filename standard to check if supplier is match with filename
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('supplier'),
            // should not be editable
            // check if extension lock
            TextField::new('importFile')->setFormType(VichFileType::class)->onlyOnForms(),
            TextField::new('filename')->hideOnForm(),
            DateTimeField::new('createdAt')->hideOnForm(),
            TextField::new('status')->hideOnForm(),
            CollectionField::new('items')->onlyOnDetail()->setTemplatePath('admin/supplierProduct/listing.html.twig'),
        ];
    }
}
