<?php

namespace App\Appointment\BookedAppointment\Domain;

class BookedAppointment
{
    public function __construct(
        private string $id,
        private string $availableAppointmentId,
        private string $email,
        private bool $isActive,
    ) {
    }

    public static function create(string $id, string $availableAppointmentId, string $email): self
    {
        self::ensureIsValidEmail($email);

        return new self($id, $availableAppointmentId, $email, true);
    }

    public function remove(): void
    {
        $this->isActive = false;
    }

    public function update(string $availableAppointmentId, string $email): void
    {
        self::ensureIsValidEmail($email);
        $this->availableAppointmentId = $availableAppointmentId;
        $this->email = $email;
    }

    private static function ensureIsValidEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new BookedAppointmentInvalidEmailException($email);
        }
    }

    public function id(): string
    {
        return $this->id;
    }

    public function availableAppointmentId(): string
    {
        return $this->availableAppointmentId;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}