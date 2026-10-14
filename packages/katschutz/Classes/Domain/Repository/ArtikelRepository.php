<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Repository;

use Schmid\Katschutz\Domain\Model\Artikel;
use Schmid\Katschutz\Domain\Model\Kategorie;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * @extends AbstractArchivRepository<Artikel>
 */
final class ArtikelRepository extends AbstractArchivRepository
{
    /**
     * @return QueryResultInterface<int, Artikel>
     */
    public function findeFuerKatalog(?Kategorie $kategorie, string $suche): QueryResultInterface
    {
        $query = $this->createQuery();
        $bedingungen = [];
        if ($kategorie !== null) {
            $bedingungen[] = $query->equals('kategorie', $kategorie);
        }
        $suche = trim($suche);
        if ($suche !== '') {
            $like = '%' . addcslashes($suche, '%_') . '%';
            $bedingungen[] = $query->logicalOr(
                $query->like('title', $like),
                $query->like('beschreibung', $like),
            );
        }
        if ($bedingungen !== []) {
            $query->matching($query->logicalAnd(...$bedingungen));
        }
        return $query->execute();
    }
}
