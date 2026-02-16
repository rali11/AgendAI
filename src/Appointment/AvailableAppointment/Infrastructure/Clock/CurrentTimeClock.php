<?php

namespace App\Appointment\AvailableAppointment\Infrastructure\Clock;

use App\Appointment\AvailableAppointment\Domain\Clock\Clock;

class CurrentTimeClock implements Clock
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
