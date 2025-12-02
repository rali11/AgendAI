<?php

namespace App\Appointment\AvailableAppointment\Domain;

interface AvailableAppointmentRepository
{
    public function save(AvailableAppointment $availableAppointment): void;

    public function search(string $id): ?AvailableAppointment;

    public function searchByDateAndDuration(\DateTimeImmutable $date, int $durationInMinutes): ?AvailableAppointment;
}
