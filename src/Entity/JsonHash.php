<?php

namespace App\Entity;

use App\Repository\JsonHashRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: JsonHashRepository::class)]
class JsonHash
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(type: Types::JSON)]
    #[Assert\NotBlank]
    #[Assert\Json]
    private string $jsonData = '';

    #[ORM\Column(length: 40, unique: true)]
    private string $hashCode = '';

    public function setJsonData(string $jsonData): static
    {
        $this->jsonData = $jsonData;

        return $this;
    }

    public function setHashCode(string $hashCode): static
    {
        $this->hashCode = $hashCode;

        return $this;
    }

    public function __toString(): string
    {
        return json_encode([
            'item' => [
                'hash' => $this->hashCode,
                'json' => json_decode($this->jsonData)
            ]
        ]);
    }
}
