<?php

namespace App\Tests\Appointment\AvailableAppointment\Domain;

use App\Appointment\AvailableAppointment\Domain\AvailableAppointment;
use Ramsey\Uuid\Uuid;

final class AvailableAppointmentMother
{
    public static function create(
        ?string $id = null,
        ?\DateTimeImmutable $date = null,
        ?int $durationInMinutes = null,
        ?bool $isActive = null,
    ): AvailableAppointment {
        return new AvailableAppointment(
            $id ?? Uuid::uuid4()->toString(),
            $date ?? new \DateTimeImmutable('2024-01-01 09:00:00'),
            $durationInMinutes ?? 30,
            $isActive ?? true
        );
    }

    public static function random(): AvailableAppointment
    {
        return self::create();
    }
}
