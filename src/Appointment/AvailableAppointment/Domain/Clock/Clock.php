<?php

namespace App\Appointment\AvailableAppointment\Domain\Clock;

interface Clock
{
    public function now(): \DateTimeImmutable;
}
