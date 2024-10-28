<?php

namespace App\FileImport\Application\Service;

use App\Catalog\Domain\Entity\DTO\ProductDTO;
use App\FileImport\Domain\Entity\FileImport;
use App\Catalog\Domain\Entity\Product;
use App\Catalog\Domain\Entity\SupplierProduct;
use App\Shared\Application\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Workflow\WorkflowInterface;

// Interface allowing templating of methods for different import file format
abstract class AbstractImportService
{
    public function __construct(private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private WorkflowInterface $importValidation,
        private MailerService $mailer)
    {
    }

    abstract public function parse(FileImport $fileImport): bool;
    // @TODO possibility to only parse to convert to json data and save it in the FileImport model
    // but possible issue with file size and data size and query performance
    // price of data storage vs price of cdn/processing

    // check duplicata in the same file with different price ?
    /**
     * @param array<mixed> $lines
     */
    public function process(FileImport $fileImport, array $lines): void
    {
        $total = count($lines);
        $successful = 0;
        $errorList = [];
        foreach ($lines as $line) {
            list($productName, $code, $priceRepresentation) = $line;
            $dto = new ProductDTO($productName, $code, $priceRepresentation);
            $errors = $this->validator->validate($dto);
            if ($errors->count() > 0) {
                $errorList[] = (string) $errors.'code :'.$code;
                continue;
            }
            $product = $this->em->getRepository(Product::class)->findOneBy(['code' => $code]);
            if (!$product) {
                // @TODO : add verification product name on a different code
                $newProduct = $dto->convertToObject();
                // create product
                $this->em->persist($newProduct);
                $this->em->flush();
                $product = $newProduct;
            }
            // prevent issues with storing in float and computation between floats
            $priceInCents = $dto->convertPriceInCents();
            $error = $this->insertDb($fileImport, $product, $priceInCents);
            if ($error) {
                $errorList[] = $error;
                continue;
            }
            ++$successful;
        }
        $results = [
            'total' => $total,
            'successful' => $successful,
            'errors' => $errorList,
        ];
        $this->advanceWorkflow($fileImport, $results);
        $this->sendEmailNotification($fileImport, $results);
    }

    public function insertDb(FileImport $fileImport, Product $product, int $price): string|false
    {
        // check if already inserted in db
        $existingSupplierProduct = $this->em->getRepository(SupplierProduct::class)->findOneBy([
            'import' => $fileImport->getId(),
            'product' => $product->getId(),
        ]);
        if ($existingSupplierProduct) {
            // add warning duplicate or already parsed
            if ($existingSupplierProduct->getPrice() !== $price) {
                // issue in the pricing
                return sprintf('duplicate issue with pricing: code #%s (actual %s != new %s)', $product->getCode(), $existingSupplierProduct->getPrice(), $price);
            }

            return sprintf('duplicate #%s', $product->getCode());
        }

        $supplierProduct = new SupplierProduct();
        $supplierProduct->setSupplier($fileImport->getSupplier());
        $supplierProduct->setProduct($product);
        $supplierProduct->setPrice($price);
        $supplierProduct->setImport($fileImport);

        $this->em->persist($supplierProduct);
        $this->em->flush();

        return false;
    }

    /**
     * @param array<mixed> $results
     */
    public function advanceWorkflow(FileImport $fileImport, array $results): void
    {
        $errors = $results['errors'];
        if (!empty($errors)) {
            // parse error at line x of this file
        }
        // workflow: transitioned to 100% complete if all lines are inserted
        // workflow: transitioned to to_review if new product or faulty
        if ($this->importValidation->can($fileImport, 'to_review')) {
            $this->importValidation->apply($fileImport, 'to_review');
            $this->em->flush();
        }
    }

    // send a report of new product new price and faulty errors like price over 1000
    // can add over verification like if price fluctuation is high
    /**
     * @param array<mixed> $results
     */
    public function sendEmailNotification(FileImport $fileImport, array $results): void
    {
        $this->mailer->sendImportCompleteNotification($fileImport, $results);
    }
}
