<?php

declare(strict_types=1);

namespace Schmid\Downloads\Domain\Dto;

/**
 * Read-only projection of a FAL file for the Downloads plugin.
 * Not an Extbase entity — sys_file/sys_file_metadata are already modeled by FAL itself.
 */
final readonly class DownloadFile
{
    /**
     * @param list<string> $categories All categories assigned on the file's metadata
     * @param list<string> $keywords Parsed from the metadata "keywords" field (comma-separated)
     */
    public string $searchText;

    /**
     * @param list<string> $categories
     * @param list<string> $keywords
     */
    public function __construct(
        public int $uid,
        public string $title,
        public string $description,
        public string $filename,
        public string $extension,
        public string $url,
        public string $sizeFormatted,
        public string $category,
        public array $categories,
        public array $keywords,
    ) {
        // Precomputed (not a getSearchText() method call) because Fluid resolves
        // {download.searchText} via property/getter access, not method-call syntax.
        $this->searchText = implode(' ', [
            $this->title,
            $this->description,
            $this->filename,
            implode(' ', $this->categories),
            implode(' ', $this->keywords),
        ]);
    }
}
