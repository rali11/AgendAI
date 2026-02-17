<?php

namespace App\Appointment\AvailableAppointment\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class AvailableAppointment
{
    private Collection $bookedAppointments;

    public function __construct(
        private string $id,
        private \DateTimeImmutable $date,
        private int $durationInMinutes,
        private bool $isActive,
    ) {
        $this->bookedAppointments = new ArrayCollection();
    }

    public static function create(string $id, \DateTimeImmutable $date, int $durationInMinutes): self
    {
        $availableAppointment = new self(
            $id,
            $date,
            $durationInMinutes,
            true
        );

        return $availableAppointment;
    }

    public function remove(): void
    {
        $this->isActive = false;
    }

    public function update(\DateTimeImmutable $date, int $durationInMinutes): void
    {
        $this->date = $date;
        $this->durationInMinutes = $durationInMinutes;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function date(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function durationInMinutes(): int
    {
        return $this->durationInMinutes;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
