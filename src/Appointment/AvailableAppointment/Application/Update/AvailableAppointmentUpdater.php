<?php

namespace App\Appointment\AvailableAppointment\Application\Update;

use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentExistException;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentPastDateException;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentRepository;
use App\Appointment\AvailableAppointment\Domain\Clock\Clock;
use App\Appointment\AvailableAppointment\Domain\Find\AvailableAppointmentFinder;

final class AvailableAppointmentUpdater
{
    public function __construct(private AvailableAppointmentRepository $repository, private Clock $clock)
    {
    }

    public function __invoke(string $id, \DateTimeImmutable $date, int $durationInMinutes): void
    {
        $finder = new AvailableAppointmentFinder($this->repository);
        $availableAppointment = $finder->__invoke($id);

        $dateNow = $this->clock->now();
        if ($date < $dateNow) {
            throw new AvailableAppointmentPastDateException();
        }

        $overlappingAppointments = $this->repository->searchByOverlapping($date, $durationInMinutes);
        $overlappingAppointments = array_filter(
            $overlappingAppointments,
            fn ($appointment) => $appointment->id() !== $id
        );

        if (count($overlappingAppointments) > 0) {
            throw new AvailableAppointmentExistException();
        }

        $availableAppointment->update($date, $durationInMinutes);
        $this->repository->update($availableAppointment);
    }
}
