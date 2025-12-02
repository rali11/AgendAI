<?php

namespace App\Appointment\AvailableAppointment\Application;

use App\Appointment\AvailableAppointment\Domain\AvailableAppointment;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentExistsException;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentRepository;

final class AvailableAppointmentCreator
{
    public function __construct(private AvailableAppointmentRepository $repository)
    {
    }

    public function __invoke(string $id, \DateTimeImmutable $date, int $durationInMinutes): void
    {
        $availableAppointment = AvailableAppointment::create(
            $id,
            $date,
            $durationInMinutes
        );

        if (null !== $this->repository->searchByDateAndDuration($date, $durationInMinutes)) {
            throw new AvailableAppointmentExistsException();
        }

        $this->repository->save($availableAppointment);
    }
}
