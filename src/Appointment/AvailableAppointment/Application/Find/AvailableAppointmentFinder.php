<?php

namespace App\Appointment\AvailableAppointment\Application\Find;

use App\Appointment\AvailableAppointment\Domain\AvailableAppointment;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentDoesNotExistException;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentRepository;

final class AvailableAppointmentFinder
{
    public function __construct(private AvailableAppointmentRepository $repository)
    {
    }

    public function __invoke(string $id): ?AvailableAppointment
    {
        $availableAppointment = $this->repository->search($id);

        if (null === $availableAppointment) {
            throw new AvailableAppointmentDoesNotExistException();
        }

        return $availableAppointment;
    }
}
