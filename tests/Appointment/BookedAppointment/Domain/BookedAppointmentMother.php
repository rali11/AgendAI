<?php

namespace App\Tests\Appointment\BookedAppointment\Domain;

use App\Appointment\BookedAppointment\Domain\BookedAppointment;
use Ramsey\Uuid\Uuid;

final class BookedAppointmentMother
{
    public static function create(
        ?string $id = null,
        ?string $availableAppointmentId = null,
        ?string $email = null,
        ?bool $isActive = null,
    ): BookedAppointment {
        return new BookedAppointment(
            $id ?? Uuid::uuid4()->toString(),
            $availableAppointmentId ?? Uuid::uuid4()->toString(),
            $email ?? 'test@example.com',
            $isActive ?? true
        );
    }

    public static function random(): BookedAppointment
    {
        return self::create();
    }
}
