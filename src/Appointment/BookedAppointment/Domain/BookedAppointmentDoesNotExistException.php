<?php

namespace App\Appointment\BookedAppointment\Domain;

final class BookedAppointmentDoesNotExistException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Booked appointment does not exist.');
    }
}
