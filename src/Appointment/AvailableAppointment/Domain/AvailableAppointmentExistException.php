<?php

namespace App\Appointment\AvailableAppointment\Domain;

final class AvailableAppointmentExistException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Ya existe una cita disponible en el mismo rango de fecha y duración.');
    }
}
