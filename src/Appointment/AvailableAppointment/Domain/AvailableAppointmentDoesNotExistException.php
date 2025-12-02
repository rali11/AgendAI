<?php

namespace App\Appointment\AvailableAppointment\Domain;

final class AvailableAppointmentDoesNotExistException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Available appointment does not exist.');
    }
}