<?php

namespace App\Tests\Appointment\AvailableAppointment\Application\SearchByRange;

use App\Appointment\AvailableAppointment\Application\SearchByRange\AvailableAppointmentByRangeSearcher;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentRepository;
use App\Tests\Appointment\AvailableAppointment\Domain\AvailableAppointmentMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class AvailableAppointmentByRangeSearcherTest extends TestCase
{
    private AvailableAppointmentRepository|MockObject $repository;

    private AvailableAppointmentByRangeSearcher $searcher;

    public function setUp(): void
    {
        $this->repository = $this->createMock(AvailableAppointmentRepository::class);
        $this->searcher = new AvailableAppointmentByRangeSearcher($this->repository);
    }

    public function testItShouldFoundAvailableAppointmentsInExistingRange(): void
    {
        $existingAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-10-10 10:00:00'),
            durationInMinutes: 30
        );

        $newAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-10-10 10:00:00'),
            durationInMinutes: 10
        );

        $this->repository
            ->expects($this->once())
            ->method('searchByOverlapping')
            ->with($newAvailableAppointment->date(), $newAvailableAppointment->durationInMinutes())
            ->willReturn([$existingAvailableAppointment]);

        $result = $this->searcher->__invoke($newAvailableAppointment->date(), $newAvailableAppointment->durationInMinutes());

        $this->assertEquals($existingAvailableAppointment, $result[0]);
    }
}
