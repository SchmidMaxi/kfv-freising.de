<?php

declare(strict_types=1);

namespace Schmid\Downloads\Controller;

use Psr\Http\Message\ResponseInterface;
use Schmid\Downloads\Service\DownloadFileService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

final class DownloadController extends ActionController
{
    private const CATEGORY_FALLBACK = 'Sonstige';

    public function __construct(
        private readonly DownloadFileService $downloadFileService,
    ) {}

    public function listAction(): ResponseInterface
    {
        $collectionUids = $this->parseCsvToIntList((string)($this->settings['fileCollections'] ?? ''));
        $allowedExtensions = array_values(array_filter(array_map(
            static fn(string $extension): string => strtolower(trim($extension)),
            explode(',', (string)($this->settings['allowedExtensions'] ?? ''))
        )));

        $downloads = $this->downloadFileService->listFromCollections($collectionUids, $allowedExtensions);

        $groups = [];
        foreach ($downloads as $download) {
            $groups[$download->category][] = $download;
        }
        uksort($groups, static function (string $a, string $b): int {
            if (($a === self::CATEGORY_FALLBACK) !== ($b === self::CATEGORY_FALLBACK)) {
                return $a === self::CATEGORY_FALLBACK ? 1 : -1;
            }

            return $a <=> $b;
        });

        $this->view->assignMultiple([
            'groups' => $groups,
            'categories' => array_keys($groups),
            'downloads' => $downloads,
        ]);

        return $this->htmlResponse();
    }

    /**
     * @return list<int>
     */
    private function parseCsvToIntList(string $csv): array
    {
        return array_values(array_filter(array_map('intval', explode(',', $csv))));
    }
}
