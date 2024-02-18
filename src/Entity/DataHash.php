<?php

namespace App\Entity;

use App\Repository\DataHashRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DataHashRepository::class)]
#[ORM\Index(name: 'hash_code_idx', columns: ['hash_code'])]
class DataHash
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private string $data = '';

    #[ORM\Column(length: 40)]
    #[Assert\NotBlank]
    private string $hashCode = '';

    public function setData(string $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function setHashCode(string $hashCode): static
    {
        $this->hashCode = $hashCode;

        return $this;
    }

    public function getHashCode(): string
    {
        return $this->hashCode;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getDataForResponse(): mixed
    {
        if (json_validate($this->data)) {
            return json_decode($this->data, true);
        }

        return $this->data;
    }
}
