<?php

namespace App\Appointment\AvailableAppointment\Application\Find;

use App\Appointment\AvailableAppointment\Domain\AvailableAppointment;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentRepository;
use App\Appointment\AvailableAppointment\Domain\Find\AvailableAppointmentFinder as AvailableAppointmentFinderDomain;

final class AvailableAppointmentFinder
{
    public function __construct(private AvailableAppointmentRepository $repository)
    {
    }

    public function __invoke(string $id): ?AvailableAppointment
    {
        $finder = new AvailableAppointmentFinderDomain($this->repository);

        return $finder->__invoke($id);
    }
}
