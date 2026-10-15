<?php

declare(strict_types=1);

namespace Schmid\Sitepackage\Authentication;

use Causal\Oidc\Event\ModifyResourceOwnerEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;

/**
 * Passt die UserInfo-Antwort des KFV-Logins an causal/oidc an.
 *
 * - causal/oidc ordnet FE-Gruppen über den Claim "Roles" zu (Abgleich mit fe_groups.tx_oidc_pattern).
 *   Das IAM liefert die fertig gemappten Rollen für TYPO3 als "portal_roles".
 * - Nicht bestätigte E-Mail-Adressen werden nicht übernommen.
 */
#[AsEventListener(identifier: 'sitepackage/oidc-map-iam-claims')]
final class MapIamClaimsListener
{
    public function __invoke(ModifyResourceOwnerEvent $event): void
    {
        $resourceOwner = $event->getResourceOwner();

        $portalRoles = $resourceOwner['portal_roles'] ?? [];
        $resourceOwner['Roles'] = is_array($portalRoles)
            ? array_values(array_filter($portalRoles, 'is_string'))
            : [];

        if (($resourceOwner['email_verified'] ?? false) !== true) {
            unset($resourceOwner['email']);
        }

        $event->setResourceOwner($resourceOwner);
    }
}
