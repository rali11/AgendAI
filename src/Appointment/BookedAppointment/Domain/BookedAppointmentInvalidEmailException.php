<?php

namespace App\Appointment\BookedAppointment\Domain;

final class BookedAppointmentInvalidEmailException extends \Exception
{
    public function __construct(string $email)
    {
        parent::__construct(sprintf('The email <%s> is not valid.', $email));
    }
}
