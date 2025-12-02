<?php

namespace App\Tests\Appointment\AvailableAppointment\Application;

use App\Appointment\AvailableAppointment\Application\AvailableAppointmentFinder;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentRepository;
use App\Tests\Appointment\AvailableAppointment\Domain\AvailableAppointmentMother;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentDoesNotExistException;
use PHPUnit\Framework\TestCase;

final class AvailableAppointmentFinderTest extends TestCase
{
    private $repository;

    private AvailableAppointmentFinder $finder;

    public function setUp(): void
    {
        $this->repository = $this->createMock(AvailableAppointmentRepository::class);
        $this->finder = new AvailableAppointmentFinder($this->repository);
    }

    public function testItShouldFindAvailableAppointment(): void
    {
        $availableAppointment = AvailableAppointmentMother::random();

        $this->repository->expects($this->once())
            ->method('search')
            ->with($availableAppointment->id())
            ->willReturn($availableAppointment);

        $found = ($this->finder)($availableAppointment->id());

        $this->assertEquals($availableAppointment, $found);
    }

    public function testItShouldNotFindAvailableAppointment(): void
    {
        $availableAppointment = AvailableAppointmentMother::random();

        $this->repository->expects($this->once())
            ->method('search')
            ->with($availableAppointment->id())
            ->willReturn(null);

        $this->expectException(AvailableAppointmentDoesNotExistException::class);

        ($this->finder)($availableAppointment->id());
    }
}
