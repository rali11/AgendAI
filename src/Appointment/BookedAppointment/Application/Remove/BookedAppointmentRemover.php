<?php

namespace App\Appointment\BookedAppointment\Application\Remove;

use App\Appointment\BookedAppointment\Domain\BookedAppointmentRepository;
use App\Appointment\BookedAppointment\Domain\Find\BookedAppointmentFinder;

final class BookedAppointmentRemover
{
    public function __construct(private BookedAppointmentRepository $repository)
    {
    }

    public function __invoke(string $id): void
    {
        $finder = new BookedAppointmentFinder($this->repository);
        $bookedAppointment = $finder->__invoke($id);

        $bookedAppointment->remove();
        $this->repository->update($bookedAppointment);
    }
}
