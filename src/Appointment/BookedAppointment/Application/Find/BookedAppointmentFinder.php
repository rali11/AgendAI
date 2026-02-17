<?php

namespace App\Appointment\BookedAppointment\Application\Find;

use App\Appointment\BookedAppointment\Domain\BookedAppointment;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentRepository;
use App\Appointment\BookedAppointment\Domain\Find\BookedAppointmentFinder as BookedAppointmentFinderDomain;

final class BookedAppointmentFinder
{
    public function __construct(private BookedAppointmentRepository $repository)
    {
    }

    public function __invoke(string $id): BookedAppointment
    {
        $finder = new BookedAppointmentFinderDomain($this->repository);

        return $finder->__invoke($id);
    }
}
