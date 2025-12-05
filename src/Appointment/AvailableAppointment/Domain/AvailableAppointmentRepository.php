<?php

namespace App\Appointment\AvailableAppointment\Domain;

interface AvailableAppointmentRepository
{
    public function save(AvailableAppointment $availableAppointment): void;

    public function search(string $id): ?AvailableAppointment;

    public function searchByOverlapping(\DateTimeImmutable $date, int $durationInMinutes): array;
}
