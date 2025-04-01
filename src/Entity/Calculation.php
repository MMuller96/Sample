<?php

namespace App\Entity;

use App\Constants\CalculationConstants;
use App\Repository\CalculationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CalculationRepository::class)]
class Calculation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column]
    private ?int $installments = null;

    #[ORM\Column]
    private ?float $interest_rate = CalculationConstants::INTEREST_RATE;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column]
    private ?bool $excluded = false;

    #[ORM\Column]
    private array $schedule = [];

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'installments' => $this->installments,
            'interest_rate' => $this->interest_rate,
            'created_at' => $this->created_at,
            'excluded' => $this->excluded,
            'schedule' => $this->schedule,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getInstallments(): ?int
    {
        return $this->installments;
    }

    public function setInstallments(int $installments): static
    {
        $this->installments = $installments;

        return $this;
    }

    public function getInterestRate(): ?float
    {
        return $this->interest_rate;
    }

    public function setInterestRate(float $interest_rate): static
    {
        $this->interest_rate = $interest_rate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function isExcluded(): ?bool
    {
        return $this->excluded;
    }

    public function setExcluded(bool $excluded): static
    {
        $this->excluded = $excluded;

        return $this;
    }

    public function getSchedule(): array
    {
        return $this->schedule;
    }

    public function setSchedule(array $schedule): static
    {
        $this->schedule = $schedule;

        return $this;
    }
}
