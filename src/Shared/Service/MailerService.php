<?php

namespace App\Shared\Application\Service;

use App\FileImport\Domain\Entity\FileImport;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    /**
     * send report completion of parsing / integration of Import file.
     *
     * @param array<mixed> $results
     */
    public function sendImportCompleteNotification(FileImport $fileImport, array $results): bool
    {
        $text = sprintf('Import #%d succesful: %s/%s', $fileImport->getId(), $results['successful'], $results['total']);
        $errorText = '';
        foreach ($results['errors'] as $errors) {
            $errorText .= $errors."\n";
        }

        $email = (new Email())
            ->from('noreply@example.com')
            ->to('chunhoo.ngo@gmail.com')
            ->subject('Report for mercuriale: #'.$fileImport->getId())
            ->text($text."\n".$errorText);

        $this->mailer->send($email);

        return true;
    }
}
