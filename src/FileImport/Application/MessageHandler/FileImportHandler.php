<?php

namespace App\FileImport\Application\MessageHandler;

use App\FileImport\Domain\Entity\FileImport;
use App\FileImport\Application\Service\CSVImportService;
use App\FileImport\Application\Message\FileImportNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

// we treat file import async as it can be time consuming if files are big and treatment heavy
#[AsMessageHandler]
class FileImportHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private CSVImportService $manager,
        private WorkflowInterface $importValidation,
    ) {
    }

    // launch treatment of file import
    public function __invoke(FileImportNotification $message): void
    {
        // find the entry
        $fileImport = $this->em->getRepository(FileImport::class)->find($message->getFileImportId());
        // verify it has not been treated already
        if (!$this->importValidation->can($fileImport, 'to_review')) {
            // @todo create a message warning to send for monitoring purpose
            return;
        }
        // @todo check extension of file to trigger proper parser
        $this->manager->parse($fileImport);
    }
}
