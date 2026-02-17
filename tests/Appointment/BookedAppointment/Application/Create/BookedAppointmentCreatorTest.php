<?php

namespace App\Tests\Appointment\BookedAppointment\Application\Create;

use App\Appointment\BookedAppointment\Application\Create\BookedAppointmentCreator;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentAlreadyExistsException;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentInvalidEmailException;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentRepository;
use App\Tests\Appointment\BookedAppointment\Domain\BookedAppointmentMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BookedAppointmentCreatorTest extends TestCase
{
    private BookedAppointmentCreator $creator;

    private BookedAppointmentRepository|MockObject $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(BookedAppointmentRepository::class);
        $this->creator = new BookedAppointmentCreator($this->repository);
    }

    public function testItShouldCreateBookedAppointment(): void
    {
        $bookedAppointment = BookedAppointmentMother::random();

        $this->repository->expects($this->once())
            ->method('searchByAvailableAppointmentId')
            ->with($bookedAppointment->availableAppointmentId())
            ->willReturn([]);

        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($bookedAppointment));

        $this->creator->__invoke(
            $bookedAppointment->id(),
            $bookedAppointment->availableAppointmentId(),
            $bookedAppointment->email()
        );
    }

    public function testItShouldNotCreateBookedAppointmentWhenAlreadyExists(): void
    {
        $existingBookedAppointment = BookedAppointmentMother::random();

        $newBookedAppointment = BookedAppointmentMother::create(
            availableAppointmentId: $existingBookedAppointment->availableAppointmentId()
        );

        $this->repository->expects($this->once())
            ->method('searchByAvailableAppointmentId')
            ->with($newBookedAppointment->availableAppointmentId())
            ->willReturn([$existingBookedAppointment]);

        $this->expectException(BookedAppointmentAlreadyExistsException::class);

        $this->creator->__invoke(
            $newBookedAppointment->id(),
            $newBookedAppointment->availableAppointmentId(),
            $newBookedAppointment->email()
        );
    }

    public function testItShouldNotCreateBookedAppointmentWithInvalidEmail(): void
    {
        $bookedAppointment = BookedAppointmentMother::random();

        $this->repository->expects($this->once())
            ->method('searchByAvailableAppointmentId')
            ->with($bookedAppointment->availableAppointmentId())
            ->willReturn([]);

        $this->expectException(BookedAppointmentInvalidEmailException::class);

        $this->creator->__invoke(
            $bookedAppointment->id(),
            $bookedAppointment->availableAppointmentId(),
            'invalid-email'
        );
    }
}
