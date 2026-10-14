<?php

declare(strict_types=1);

namespace Schmid\Katschutz\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class FrontendUser extends AbstractEntity
{
    protected string $username = '';
    protected string $name = '';
    protected string $firstName = '';
    protected string $lastName = '';
    protected string $email = '';
    protected string $telephone = '';

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTelephone(): string
    {
        return $this->telephone;
    }

    public function getAnzeigename(): string
    {
        $voll = trim($this->firstName . ' ' . $this->lastName);
        return $voll !== '' ? $voll : ($this->name !== '' ? $this->name : $this->username);
    }
}
