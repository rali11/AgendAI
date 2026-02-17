<?php

namespace App\Appointment\BookedAppointment\Application\Update;

use App\Appointment\BookedAppointment\Domain\BookedAppointmentAlreadyExistsException;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentRepository;
use App\Appointment\BookedAppointment\Domain\Find\BookedAppointmentFinder;

final class BookedAppointmentUpdater
{
    public function __construct(private BookedAppointmentRepository $repository)
    {
    }

    public function __invoke(string $id, string $availableAppointmentId, string $email): void
    {
        $finder = new BookedAppointmentFinder($this->repository);
        $bookedAppointment = $finder->__invoke($id);

        $overlapping = $this->repository->searchByAvailableAppointmentId($availableAppointmentId);
        $overlapping = array_filter(
            $overlapping,
            fn ($appointment) => $appointment->id() !== $id
        );

        if (count($overlapping) > 0) {
            throw new BookedAppointmentAlreadyExistsException();
        }

        $bookedAppointment->update($availableAppointmentId, $email);
        $this->repository->update($bookedAppointment);
    }
}
