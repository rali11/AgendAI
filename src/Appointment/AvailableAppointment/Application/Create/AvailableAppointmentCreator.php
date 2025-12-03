<?php

namespace App\Appointment\AvailableAppointment\Application\Create;

use App\Appointment\AvailableAppointment\Domain\AvailableAppointment;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentExistsException;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentRepository;

final class AvailableAppointmentCreator
{
    public function __construct(private AvailableAppointmentRepository $repository)
    {
    }

    public function __invoke(string $id, \DateTimeImmutable $date, int $durationInMinutes): void
    {
        $availableAppointment = AvailableAppointment::create(
            $id,
            $date,
            $durationInMinutes
        );

        $result = $this->repository->searchByRange($date, $durationInMinutes);

        if (count($result) > 0) {
            throw new AvailableAppointmentExistsException();
        }

        $this->repository->save($availableAppointment);
    }
}
