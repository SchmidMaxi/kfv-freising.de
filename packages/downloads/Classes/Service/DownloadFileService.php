<?php

declare(strict_types=1);

namespace Schmid\Downloads\Service;

use Doctrine\DBAL\ArrayParameterType;
use Schmid\Downloads\Domain\Dto\DownloadFile;
use TYPO3\CMS\Core\Collection\PersistableCollectionInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileCollectionRepository;
use TYPO3\CMS\Core\Resource\FileReference;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Resolves file collections into DownloadFile DTOs, enriched with
 * sys_category (via sys_category_record_mm) and sys_file_metadata.keywords.
 * Both fields are core sys_file_metadata features — editors tag files
 * directly in Filelist, no custom backend UI needed.
 */
final readonly class DownloadFileService
{
    private const TABLE_MM = 'sys_category_record_mm';
    private const TABLE_METADATA = 'sys_file_metadata';
    private const CATEGORY_FALLBACK = 'Sonstige';

    public function __construct(
        private FileCollectionRepository $fileCollectionRepository,
        private ConnectionPool $connectionPool,
    ) {}

    /**
     * @param list<int> $collectionUids
     * @param list<string> $allowedExtensions lowercase, no dot; empty = no restriction
     * @return list<DownloadFile>
     */
    public function listFromCollections(array $collectionUids, array $allowedExtensions = []): array
    {
        $files = $this->resolveFiles($collectionUids);

        if ($files === []) {
            return [];
        }

        $metadataUidByFileUid = [];
        foreach ($files as $file) {
            $metadataUidByFileUid[$file->getUid()] = (int)($file->getMetaData()->get()['uid'] ?? 0);
        }

        $categoriesByMetadataUid = $this->fetchCategoriesByMetadataUid(array_values($metadataUidByFileUid));

        $downloads = [];
        foreach ($files as $file) {
            $extension = strtolower($file->getExtension());

            if ($allowedExtensions !== [] && !in_array($extension, $allowedExtensions, true)) {
                continue;
            }

            $metaData = $file->getMetaData()->get();
            $metadataUid = $metadataUidByFileUid[$file->getUid()];
            $categories = $categoriesByMetadataUid[$metadataUid] ?? [];
            $keywords = array_values(array_filter(array_map(
                'trim',
                explode(',', (string)($metaData['keywords'] ?? ''))
            )));

            $downloads[] = new DownloadFile(
                uid: $file->getUid(),
                title: (string)($metaData['title'] ?? '' ?: $file->getName()),
                description: (string)($metaData['description'] ?? ''),
                filename: $file->getName(),
                extension: $extension,
                url: (string)($file->getPublicUrl() ?? ''),
                sizeFormatted: GeneralUtility::formatSize($file->getSize()),
                category: $categories[0] ?? self::CATEGORY_FALLBACK,
                categories: $categories,
                keywords: $keywords,
            );
        }

        usort($downloads, static fn(DownloadFile $a, DownloadFile $b): int => $a->title <=> $b->title);

        return $downloads;
    }

    /**
     * @param list<int> $collectionUids
     * @return list<File>
     */
    private function resolveFiles(array $collectionUids): array
    {
        $filesByUid = [];

        foreach ($collectionUids as $collectionUid) {
            try {
                $collection = $this->fileCollectionRepository->findByUid($collectionUid);
            } catch (\TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException) {
                continue;
            }

            if (!$collection instanceof PersistableCollectionInterface) {
                continue;
            }

            $collection->loadContents();

            foreach ($collection as $item) {
                $file = $item instanceof FileReference ? $item->getOriginalFile() : $item;

                if (!$file instanceof File) {
                    continue;
                }

                $filesByUid[$file->getUid()] = $file;
            }
        }

        return array_values($filesByUid);
    }

    /**
     * @param list<int> $metadataUids
     * @return array<int, list<string>>
     */
    private function fetchCategoriesByMetadataUid(array $metadataUids): array
    {
        $metadataUids = array_values(array_unique(array_filter($metadataUids)));

        if ($metadataUids === []) {
            return [];
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_MM);
        $rows = $queryBuilder
            ->select('mm.uid_foreign AS metadata_uid', 'category.title AS category_title')
            ->from(self::TABLE_MM, 'mm')
            ->join(
                'mm',
                'sys_category',
                'category',
                $queryBuilder->expr()->eq('category.uid', $queryBuilder->quoteIdentifier('mm.uid_local'))
            )
            ->where(
                $queryBuilder->expr()->eq(
                    'mm.tablenames',
                    $queryBuilder->createNamedParameter(self::TABLE_METADATA)
                ),
                $queryBuilder->expr()->eq(
                    'mm.fieldname',
                    $queryBuilder->createNamedParameter('categories')
                ),
                $queryBuilder->expr()->in(
                    'mm.uid_foreign',
                    $queryBuilder->createNamedParameter($metadataUids, ArrayParameterType::INTEGER)
                )
            )
            ->orderBy('category.sorting')
            ->executeQuery()
            ->fetchAllAssociative();

        $result = [];
        foreach ($rows as $row) {
            $result[(int)$row['metadata_uid']][] = (string)$row['category_title'];
        }

        return $result;
    }
}
