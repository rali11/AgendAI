<?php

namespace App\Tests\Appointment\AvailableAppointment\Application\Find;

use App\Appointment\AvailableAppointment\Application\Find\AvailableAppointmentFinder;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentDoesNotExistException;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentRepository;
use App\Tests\Appointment\AvailableAppointment\Domain\AvailableAppointmentMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class AvailableAppointmentFinderTest extends TestCase
{
    private AvailableAppointmentRepository|MockObject $repository;

    private AvailableAppointmentFinder $finder;

    public function setUp(): void
    {
        $this->repository = $this->createMock(AvailableAppointmentRepository::class);
        $this->finder = new AvailableAppointmentFinder($this->repository);
    }

    public function testItShouldFindAnExistingAvailableAppointment(): void
    {
        $availableAppointment = AvailableAppointmentMother::random();

        $this->repository->expects($this->once())
            ->method('search')
            ->with($availableAppointment->id())
            ->willReturn($availableAppointment);

        $found = ($this->finder)($availableAppointment->id());

        $this->assertEquals($availableAppointment, $found);
    }

    public function testItShouldNotFindAnInexistentAvailableAppointment(): void
    {
        $availableAppointment = AvailableAppointmentMother::random();

        $this->repository->expects($this->once())
            ->method('search')
            ->with($availableAppointment->id())
            ->willReturn(null);

        $this->expectException(AvailableAppointmentDoesNotExistException::class);

        $this->finder->__invoke($availableAppointment->id());
    }
}
