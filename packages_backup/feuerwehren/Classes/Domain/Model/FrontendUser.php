<?php
declare(strict_types=1);

namespace Schmid\Feuerwehren\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

final class FrontendUser extends AbstractEntity
{
    protected string $username = '';
    protected string $name = '';
    protected string $first_name = '';
    protected string $last_name = '';
    protected string $email = '';

    public function getUsername(): string { return $this->username; }
    public function setUsername(string $username): void { $this->username = $username; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }

    public function getFirstName(): string { return $this->first_name; }
    public function setFirstName(string $first_name): void { $this->first_name = $first_name; }

    public function getLastName(): string { return $this->last_name; }
    public function setLastName(string $last_name): void { $this->last_name = $last_name; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }
}
