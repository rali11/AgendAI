<?php

namespace App\Appointment\BookedAppointment\Application\Create;

use App\Appointment\BookedAppointment\Domain\BookedAppointment;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentAlreadyExistsException;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentRepository;

final class BookedAppointmentCreator
{
    public function __construct(private BookedAppointmentRepository $repository)
    {
    }

    public function __invoke(string $id, string $availableAppointmentId, string $email): void
    {
        $result = $this->repository->searchByAvailableAppointmentId($availableAppointmentId);
        if (count($result) > 0) {
            throw new BookedAppointmentAlreadyExistsException();
        }

        $bookedAppointment = BookedAppointment::create($id, $availableAppointmentId, $email);

        $this->repository->save($bookedAppointment);
    }
}
