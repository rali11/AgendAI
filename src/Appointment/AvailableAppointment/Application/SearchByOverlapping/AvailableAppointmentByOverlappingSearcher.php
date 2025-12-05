<?php

namespace App\Appointment\AvailableAppointment\Application\SearchByOverlapping;

use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentRepository;

final class AvailableAppointmentByOverlappingSearcher
{
    private AvailableAppointmentRepository $repository;

    public function __construct(AvailableAppointmentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(\DateTimeImmutable $date, int $durationInMinutes): array
    {
        return $this->repository->searchByOverlapping($date, $durationInMinutes);
    }
}
