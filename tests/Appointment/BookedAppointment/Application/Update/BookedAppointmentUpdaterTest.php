<?php

namespace App\Tests\Appointment\BookedAppointment\Application\Update;

use App\Appointment\BookedAppointment\Application\Update\BookedAppointmentUpdater;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentAlreadyExistsException;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentDoesNotExistException;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentInvalidEmailException;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentRepository;
use App\Tests\Appointment\BookedAppointment\Domain\BookedAppointmentMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BookedAppointmentUpdaterTest extends TestCase
{
    private BookedAppointmentUpdater $updater;

    private BookedAppointmentRepository|MockObject $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(BookedAppointmentRepository::class);
        $this->updater = new BookedAppointmentUpdater($this->repository);
    }

    public function testItShouldUpdateBookedAppointment(): void
    {
        $bookedAppointment = BookedAppointmentMother::create(
            availableAppointmentId: 'available-1',
            email: 'old@example.com'
        );

        $newAvailableAppointmentId = 'available-2';
        $newEmail = 'new@example.com';

        $this->repository->expects($this->once())
            ->method('search')
            ->with($bookedAppointment->id())
            ->willReturn($bookedAppointment);

        $this->repository->expects($this->once())
            ->method('searchByAvailableAppointmentId')
            ->with($newAvailableAppointmentId)
            ->willReturn([]);

        $this->repository->expects($this->once())
            ->method('update')
            ->with($bookedAppointment);

        $this->updater->__invoke(
            $bookedAppointment->id(),
            $newAvailableAppointmentId,
            $newEmail
        );

        $this->assertEquals($newAvailableAppointmentId, $bookedAppointment->availableAppointmentId());
        $this->assertEquals($newEmail, $bookedAppointment->email());
    }

    public function testItShouldNotUpdateBookedAppointmentThatDoesNotExist(): void
    {
        $id = 'non-existent-id';

        $this->repository->expects($this->once())
            ->method('search')
            ->with($id)
            ->willReturn(null);

        $this->expectException(BookedAppointmentDoesNotExistException::class);

        $this->updater->__invoke(
            $id,
            'available-1',
            'test@example.com'
        );
    }

    public function testItShouldNotUpdateBookedAppointmentWhenAvailableAppointmentAlreadyBooked(): void
    {
        $bookedAppointment = BookedAppointmentMother::create(
            id: 'booked-1',
            availableAppointmentId: 'available-1'
        );

        $existingBookedAppointment = BookedAppointmentMother::create(
            id: 'booked-2',
            availableAppointmentId: 'available-2'
        );

        $this->repository->expects($this->once())
            ->method('search')
            ->with($bookedAppointment->id())
            ->willReturn($bookedAppointment);

        $this->repository->expects($this->once())
            ->method('searchByAvailableAppointmentId')
            ->with('available-2')
            ->willReturn([$existingBookedAppointment]);

        $this->expectException(BookedAppointmentAlreadyExistsException::class);

        $this->updater->__invoke(
            $bookedAppointment->id(),
            'available-2',
            'test@example.com'
        );
    }

    public function testItShouldAllowUpdateToSameAvailableAppointment(): void
    {
        $bookedAppointment = BookedAppointmentMother::create(
            id: 'booked-1',
            availableAppointmentId: 'available-1',
            email: 'old@example.com'
        );

        $newEmail = 'new@example.com';

        $this->repository->expects($this->once())
            ->method('search')
            ->with($bookedAppointment->id())
            ->willReturn($bookedAppointment);

        $this->repository->expects($this->once())
            ->method('searchByAvailableAppointmentId')
            ->with('available-1')
            ->willReturn([$bookedAppointment]);

        $this->repository->expects($this->once())
            ->method('update')
            ->with($bookedAppointment);

        $this->updater->__invoke(
            $bookedAppointment->id(),
            'available-1',
            $newEmail
        );

        $this->assertEquals($newEmail, $bookedAppointment->email());
    }

    public function testItShouldNotUpdateBookedAppointmentWithInvalidEmail(): void
    {
        $bookedAppointment = BookedAppointmentMother::random();

        $this->repository->expects($this->once())
            ->method('search')
            ->with($bookedAppointment->id())
            ->willReturn($bookedAppointment);

        $this->repository->expects($this->once())
            ->method('searchByAvailableAppointmentId')
            ->with('available-1')
            ->willReturn([]);

        $this->expectException(BookedAppointmentInvalidEmailException::class);

        $this->updater->__invoke(
            $bookedAppointment->id(),
            'available-1',
            'invalid-email'
        );
    }
}
