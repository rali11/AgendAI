<?php

namespace App\Appointment\AvailableAppointment\Application\Create;

use App\Appointment\AvailableAppointment\Domain\AvailableAppointment;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentExistException;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentRepository;
use App\Appointment\AvailableAppointment\Domain\Clock\Clock;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentPastDateException;


final class AvailableAppointmentCreator
{
    public function __construct(private AvailableAppointmentRepository $repository, private Clock $clock)
    {
    }

    public function __invoke(string $id, \DateTimeImmutable $date, int $durationInMinutes): void
    {
        $availableAppointment = AvailableAppointment::create(
            $id,
            $date,
            $durationInMinutes
        );

        $dateNow = $this->clock->now();
        if ($availableAppointment->date() < $dateNow) {
            throw new AvailableAppointmentPastDateException();
        }

        $result = $this->repository->searchByOverlapping($availableAppointment->date(), $availableAppointment->durationInMinutes());
        if (count($result) > 0) {
            throw new AvailableAppointmentExistException();
        }

        $this->repository->save($availableAppointment);
    }
}
