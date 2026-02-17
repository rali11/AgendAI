<?php

namespace App\Appointment\BookedAppointment\Domain;

interface BookedAppointmentRepository
{
    public function save(BookedAppointment $bookedAppointment): void;

    public function update(BookedAppointment $bookedAppointment): void;

    public function search(string $id): ?BookedAppointment;

    public function searchByAvailableAppointmentId(string $availableAppointmentId): array;
}
