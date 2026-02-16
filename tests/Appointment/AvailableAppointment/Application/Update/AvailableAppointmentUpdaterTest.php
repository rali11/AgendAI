<?php

namespace App\Tests\Appointment\AvailableAppointment\Application\Update;

use App\Appointment\AvailableAppointment\Application\Update\AvailableAppointmentUpdater;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentDoesNotExistException;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentExistException;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentPastDateException;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentRepository;
use App\Appointment\AvailableAppointment\Domain\Clock\Clock;
use App\Tests\Appointment\AvailableAppointment\Domain\AvailableAppointmentMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class AvailableAppointmentUpdaterTest extends TestCase
{
    private AvailableAppointmentUpdater $updater;

    private AvailableAppointmentRepository|MockObject $repository;

    private Clock|MockObject $clock;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(AvailableAppointmentRepository::class);
        $this->clock = $this->createMock(Clock::class);
        $this->updater = new AvailableAppointmentUpdater($this->repository, $this->clock);
    }

    public function testItShouldUpdateAvailableAppointment(): void
    {
        $availableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-10-10 10:00:00'),
            durationInMinutes: 30
        );

        $newDate = new \DateTimeImmutable('2024-10-15 11:00:00');
        $newDuration = 45;

        $this->clock->expects($this->once())
            ->method('now')
            ->willReturn(new \DateTimeImmutable('2024-10-01'));

        $this->repository->expects($this->once())
            ->method('search')
            ->with($availableAppointment->id())
            ->willReturn($availableAppointment);

        $this->repository->expects($this->once())
            ->method('searchByOverlapping')
            ->with($newDate, $newDuration)
            ->willReturn([]);

        $this->repository->expects($this->once())
            ->method('update')
            ->with($availableAppointment);

        $this->updater->__invoke(
            $availableAppointment->id(),
            $newDate,
            $newDuration
        );

        $this->assertEquals($newDate, $availableAppointment->date());
        $this->assertEquals($newDuration, $availableAppointment->durationInMinutes());
    }

    public function testItShouldNotUpdateAvailableAppointmentThatDoesNotExist(): void
    {
        $id = 'non-existent-id';

        $this->repository->expects($this->once())
            ->method('search')
            ->with($id)
            ->willReturn(null);

        $this->expectException(AvailableAppointmentDoesNotExistException::class);

        $this->updater->__invoke(
            $id,
            new \DateTimeImmutable('2024-10-15 11:00:00'),
            30
        );
    }

    public function testItShouldNotUpdateAvailableAppointmentByPastDate(): void
    {
        $availableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-10-10 10:00:00')
        );

        $pastDate = new \DateTimeImmutable('2024-09-01 10:00:00');

        $this->repository->expects($this->once())
            ->method('search')
            ->with($availableAppointment->id())
            ->willReturn($availableAppointment);

        $this->clock->expects($this->once())
            ->method('now')
            ->willReturn(new \DateTimeImmutable('2024-10-01'));

        $this->expectException(AvailableAppointmentPastDateException::class);

        $this->updater->__invoke(
            $availableAppointment->id(),
            $pastDate,
            30
        );
    }

    public function testItShouldNotUpdateAvailableAppointmentByOverlapping(): void
    {
        $availableAppointment = AvailableAppointmentMother::create(
            id: 'appointment-1',
            date: new \DateTimeImmutable('2024-10-10 10:00:00'),
            durationInMinutes: 30
        );

        $existingAppointment = AvailableAppointmentMother::create(
            id: 'appointment-2',
            date: new \DateTimeImmutable('2024-10-15 11:00:00'),
            durationInMinutes: 30
        );

        $newDate = new \DateTimeImmutable('2024-10-15 11:00:00');
        $newDuration = 30;

        $this->repository->expects($this->once())
            ->method('search')
            ->with($availableAppointment->id())
            ->willReturn($availableAppointment);

        $this->clock->expects($this->once())
            ->method('now')
            ->willReturn(new \DateTimeImmutable('2024-10-01'));

        $this->repository->expects($this->once())
            ->method('searchByOverlapping')
            ->with($newDate, $newDuration)
            ->willReturn([$existingAppointment]);

        $this->expectException(AvailableAppointmentExistException::class);

        $this->updater->__invoke(
            $availableAppointment->id(),
            $newDate,
            $newDuration
        );
    }

    public function testItShouldAllowUpdateToSameSlotIfSameAppointment(): void
    {
        $availableAppointment = AvailableAppointmentMother::create(
            id: 'appointment-1',
            date: new \DateTimeImmutable('2024-10-10 10:00:00'),
            durationInMinutes: 30
        );

        $newDate = new \DateTimeImmutable('2024-10-10 10:00:00');
        $newDuration = 45;

        $this->repository->expects($this->once())
            ->method('search')
            ->with($availableAppointment->id())
            ->willReturn($availableAppointment);

        $this->clock->expects($this->once())
            ->method('now')
            ->willReturn(new \DateTimeImmutable('2024-10-01'));

        $this->repository->expects($this->once())
            ->method('searchByOverlapping')
            ->with($newDate, $newDuration)
            ->willReturn([$availableAppointment]);

        $this->repository->expects($this->once())
            ->method('update')
            ->with($availableAppointment);

        $this->updater->__invoke(
            $availableAppointment->id(),
            $newDate,
            $newDuration
        );

        $this->assertEquals($newDuration, $availableAppointment->durationInMinutes());
    }
}
