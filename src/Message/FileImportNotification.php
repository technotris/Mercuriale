<?php

namespace App\Message;

class FileImportNotification
{
    public function __construct(
        private int $fileImportId,
    ) {
    }

    public function getFileImportId(): int
    {
        return $this->fileImportId;
    }
}
