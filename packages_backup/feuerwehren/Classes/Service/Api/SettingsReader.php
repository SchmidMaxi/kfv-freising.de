<?php
// EXT:feuerwehren/Classes/Service/Api/SettingsReader.php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Service\Api;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

final class SettingsReader
{
    /** @return mixed */
    public function get(ServerRequestInterface $request, string $path, mixed $default = null): mixed
    {
        /** @var Site|null $site */
        $site = $request->getAttribute('site');
        if (!$site instanceof Site) {
            return $default;
        }
        $settings = $site->getConfiguration()['settings'] ?? [];
        // Pfad mit Punkten, z. B. "feuerwehren.tileSource"
        $segments = explode('.', $path);
        $value = $settings;
        foreach ($segments as $seg) {
            if (!is_array($value) || !array_key_exists($seg, $value)) {
                return $default;
            }
            $value = $value[$seg];
        }
        return $value;
    }
}
