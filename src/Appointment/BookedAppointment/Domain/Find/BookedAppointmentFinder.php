<?php

namespace App\Appointment\BookedAppointment\Domain\Find;

use App\Appointment\BookedAppointment\Domain\BookedAppointment;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentDoesNotExistException;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentRepository;

final class BookedAppointmentFinder
{
    public function __construct(private BookedAppointmentRepository $repository)
    {
    }

    public function __invoke(string $id): BookedAppointment
    {
        $bookedAppointment = $this->repository->search($id);

        if (null === $bookedAppointment) {
            throw new BookedAppointmentDoesNotExistException();
        }

        return $bookedAppointment;
    }
}
