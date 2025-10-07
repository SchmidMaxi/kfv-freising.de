<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\JsonResponse;

/**
 * API-Endpunkt für /api/feuerwehren/details
 */
class ApiMiddleware implements MiddlewareInterface
{
    /**
     * Verarbeitet die HTTP-Anfrage, um API-Daten zurückzugeben.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        
        // PRÜFUNG: WIRD DIE MIDDLEWARE ÜBERHAUPT ERREICHT?
        \TYPO3\CMS\Core\Utility\DebugUtility::debug('Middleware erreicht!', 'DEBUG');

        $path = $request->getUri()->getPath();

        // Prüfen, ob die URL mit dem gewünschten API-Pfad übereinstimmt.
        // Wir verwenden hier einen Beispielpfad.
        if (strpos($path, '/api/feuerwehren/details') === 0) {

            // Hier würden Sie Ihre Logik implementieren:
            // - Datenbankabfragen (z.B. über Doctrine DBAL oder Repository)
            // - Validierung von GET/POST-Parametern
            // - Sicherheitschecks (z.B. API-Key-Validierung)

            $data = [
                'status' => 'success',
                'feuerwehr' => 'Musterstadt',
                'einsatz_bereit' => true,
                'mannschaften' => 3,
                'fahrzeuge' => [
                    ['kennzeichen' => 'MS-LF-1', 'typ' => 'LF 20'],
                    ['kennzeichen' => 'MS-MTW-2', 'typ' => 'MTW'],
                ],
                'abgerufen_um' => (new \DateTime())->format(\DateTimeInterface::ATOM),
            ];

            // Gibt eine JSON-Antwort zurück und beendet die Anfrage.
            return new JsonResponse($data);
        }

        // Wenn die URL nicht passt, geben Sie die Anfrage an den nächsten TYPO3-Handler weiter.
        return $handler->handle($request);
    }
}