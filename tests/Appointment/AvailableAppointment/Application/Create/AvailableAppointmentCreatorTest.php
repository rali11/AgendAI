<?php

namespace App\Tests\Appointment\AvailableAppointment\Application\Create;

use App\Appointment\AvailableAppointment\Application\Create\AvailableAppointmentCreator;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentExistsException;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentRepository;
use App\Tests\Appointment\AvailableAppointment\Domain\AvailableAppointmentMother;
use PHPUnit\Framework\TestCase;

final class AvailableAppointmentCreatorTest extends TestCase
{
    private AvailableAppointmentCreator $creator;

    private $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(AvailableAppointmentRepository::class);
        $this->creator = new AvailableAppointmentCreator($this->repository);
    }

    public function testItShouldCreateAvailableAppointment(): void
    {
        $availableAppointment = AvailableAppointmentMother::random();

        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($availableAppointment));

        ($this->creator)(
            $availableAppointment->id(),
            $availableAppointment->date(),
            $availableAppointment->durationInMinutes()
        );
    }

    public function testItShouldNotCreateAvailableAppointmentInSameRange(): void
    {
        $existedAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-10-10 10:00:00'),
            durationInMinutes: 30
        );

        $newAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-10-10 10:00:00'),
            durationInMinutes: 10
        );

        $this->repository->expects($this->once())
            ->method('searchByDateAndDuration')
            ->with($newAvailableAppointment->date(), $newAvailableAppointment->durationInMinutes())
            ->willReturn($existedAvailableAppointment);

        $this->expectException(AvailableAppointmentExistsException::class);

        ($this->creator)(
            $newAvailableAppointment->id(),
            $newAvailableAppointment->date(),
            $newAvailableAppointment->durationInMinutes()
        );
    }
}
