<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Service;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Schmid\Katschutz\Domain\Model\Bestellung;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;

/**
 * E-Mails zu Bestellungen. Fehler beim Versand dürfen den Ablauf nicht stoppen,
 * werden aber geloggt.
 */
final readonly class BenachrichtigungService
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
    ) {}

    public function neueBestellung(Bestellung $bestellung, KatschutzSettings $settings, ServerRequestInterface $request): void
    {
        if ($settings->mailVerwalter === []) {
            return;
        }
        $this->sende(
            $settings->mailVerwalter,
            sprintf('KatS-Bestellung %s: %s', $bestellung->getNummer(), $bestellung->getFeuerwehr()?->getName() ?? ''),
            'KatschutzNeueBestellung',
            $bestellung,
            $settings,
            $request,
        );
    }

    public function statusGeaendert(Bestellung $bestellung, KatschutzSettings $settings, ServerRequestInterface $request): void
    {
        $email = $bestellung->getBesteller()?->getEmail() ?? '';
        if ($email === '') {
            return;
        }
        $this->sende(
            [$email],
            sprintf('KatS-Bestellung %s: %s', $bestellung->getNummer(), $bestellung->getStatus()->getLabel()),
            'KatschutzStatus',
            $bestellung,
            $settings,
            $request,
        );
    }

    /**
     * @param list<string> $empfaenger
     */
    private function sende(
        array $empfaenger,
        string $betreff,
        string $template,
        Bestellung $bestellung,
        KatschutzSettings $settings,
        ServerRequestInterface $request,
    ): void {
        try {
            $mail = new FluidEmail();
            $mail->to(...array_map(static fn(string $a): Address => new Address($a), $empfaenger))
                ->subject($betreff)
                ->format(FluidEmail::FORMAT_BOTH)
                ->setTemplate($template)
                ->setRequest($request)
                ->assign('bestellung', $bestellung);
            if ($settings->mailAbsender !== '') {
                $mail->from(new Address($settings->mailAbsender));
            }
            $this->mailer->send($mail);
        } catch (\Throwable $e) {
            $this->logger->error('KatS: Mailversand fehlgeschlagen', [
                'bestellung' => $bestellung->getUid(),
                'exception' => $e,
            ]);
        }
    }
}
