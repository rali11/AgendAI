<?php

namespace App\Tests\Appointment\AvailableAppointment\Application\Create;

use App\Appointment\AvailableAppointment\Application\Create\AvailableAppointmentCreator;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentExistException;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentPastDateException;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentRepository;
use App\Appointment\AvailableAppointment\Domain\Clock\Clock;
use App\Tests\Appointment\AvailableAppointment\Domain\AvailableAppointmentMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class AvailableAppointmentCreatorTest extends TestCase
{
    private AvailableAppointmentCreator $creator;

    private AvailableAppointmentRepository|MockObject $repository;

    private Clock|MockObject $clock;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(AvailableAppointmentRepository::class);
        $this->clock = $this->createMock(Clock::class);
        $this->creator = new AvailableAppointmentCreator($this->repository, $this->clock);
    }

    public function testItShouldCreateAvailableAppointment(): void
    {
        $availableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-10-10 10:00:00')
        );

        $this->clock->expects($this->once())
            ->method('now')
            ->willReturn(new \DateTimeImmutable('2024-10-01'));

        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($availableAppointment));

        $this->repository->expects($this->once())
            ->method('searchByOverlapping')
            ->with($availableAppointment->date(), $availableAppointment->durationInMinutes())
            ->willReturn([]);

        $this->creator->__invoke(
            $availableAppointment->id(),
            $availableAppointment->date(),
            $availableAppointment->durationInMinutes()
        );
    }

    public function testItShouldNotCreateAvailableAppointmentByPastDate(): void
    {
        $availableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-10-10 10:00:00')
        );

        $this->clock->expects($this->once())
            ->method('now')
            ->willReturn(new \DateTimeImmutable('2024-10-10 10:01:00'));

        $this->expectException(AvailableAppointmentPastDateException::class);

        $this->creator->__invoke(
            $availableAppointment->id(),
            $availableAppointment->date(),
            $availableAppointment->durationInMinutes()
        );
    }

    public function testItShouldNotCreateAvailableAppointmentByOverlapping(): void
    {
        $existingAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-10-10 10:00:00'),
            durationInMinutes: 30
        );

        $newAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-10-10 10:00:00'),
            durationInMinutes: 10
        );

        $this->clock->expects($this->once())
            ->method('now')
            ->willReturn(new \DateTimeImmutable('2024-10-01'));

        $this->repository->expects($this->once())
            ->method('searchByOverlapping')
            ->with($newAvailableAppointment->date(), $newAvailableAppointment->durationInMinutes())
            ->willReturn([$existingAvailableAppointment]);

        $this->expectException(AvailableAppointmentExistException::class);

        $this->creator->__invoke(
            $newAvailableAppointment->id(),
            $newAvailableAppointment->date(),
            $newAvailableAppointment->durationInMinutes()
        );
    }
}
