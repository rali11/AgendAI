<?php

namespace App\Appointment\AvailableAppointment\Domain;

final class AvailableAppointment
{
    public function __construct(
        private string $id,
        private \DateTimeImmutable $date,
        private int $durationInMinutes,
        private bool $isActive,
    ) {
    }

    public static function create(
        string $id,
        \DateTimeImmutable $date,
        int $durationInMinutes,
    ): self {
        $availableAppointment = new self(
            $id,
            $date,
            $durationInMinutes,
            true
        );

        return $availableAppointment;
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
