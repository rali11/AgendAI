<?php

namespace App\Appointment\AvailableAppointment\Application\Remove;

use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentRepository;
use App\Appointment\AvailableAppointment\Domain\Find\AvailableAppointmentFinder;

final class AvailableAppointmentRemover
{
    public function __construct(private AvailableAppointmentRepository $repository)
    {
    }

    public function __invoke(string $id)
    {
        $finder = new AvailableAppointmentFinder($this->repository);
        $availableAppointment = $finder->__invoke($id);

        if ($availableAppointment) {
            $availableAppointment->remove();
            $this->repository->update($availableAppointment);
        }
    }
}
