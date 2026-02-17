<?php

namespace App\Appointment\BookedAppointment\Domain;

final class BookedAppointmentAlreadyExistsException extends \Exception
{
    public function __construct()
    {
        parent::__construct('A booked appointment already exists for this available appointment.');
    }
}
