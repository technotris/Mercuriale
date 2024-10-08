<?php

namespace App\Manager;

use App\Entity\FileImport;

class CSVMercurialeManager extends AbstractMercurialeManager
{
    // CSV import
    public function parse(FileImport $fileImport): bool
    {
        $file = $fileImport->getImportFile();
        if ('csv' !== strtolower($file->getExtension())) {
            return false;
        }
        // Parse CSV
        $import = $file->openFile();
        $import->setFlags(\SplFileObject::READ_CSV);
        // expected format: product_name,code,price
        $lines = [];
        foreach ($import as $line) {
            list($productName, $code, $price) = $line;
            $lines[] = [$productName, $code, $price];
        }

        $this->process($fileImport, $lines);

        return true;
    }
}
