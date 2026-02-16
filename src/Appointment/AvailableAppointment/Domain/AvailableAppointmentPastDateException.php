<?php

namespace App\Appointment\AvailableAppointment\Domain;

class AvailableAppointmentPastDateException extends \Exception
{
    public function __construct()
    {
        parent::__construct('No se puede crear una cita disponible en una fecha pasada.');
    }
}
