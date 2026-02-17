<?php

namespace App\Tests\Appointment\BookedAppointment\Application\Find;

use App\Appointment\BookedAppointment\Application\Find\BookedAppointmentFinder;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentDoesNotExistException;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentRepository;
use App\Tests\Appointment\BookedAppointment\Domain\BookedAppointmentMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BookedAppointmentFinderTest extends TestCase
{
    private BookedAppointmentRepository|MockObject $repository;

    private BookedAppointmentFinder $finder;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(BookedAppointmentRepository::class);
        $this->finder = new BookedAppointmentFinder($this->repository);
    }

    public function testItShouldFindAnExistingBookedAppointment(): void
    {
        $bookedAppointment = BookedAppointmentMother::random();

        $this->repository->expects($this->once())
            ->method('search')
            ->with($bookedAppointment->id())
            ->willReturn($bookedAppointment);

        $found = ($this->finder)($bookedAppointment->id());

        $this->assertEquals($bookedAppointment, $found);
    }

    public function testItShouldNotFindAnInexistentBookedAppointment(): void
    {
        $bookedAppointment = BookedAppointmentMother::random();

        $this->repository->expects($this->once())
            ->method('search')
            ->with($bookedAppointment->id())
            ->willReturn(null);

        $this->expectException(BookedAppointmentDoesNotExistException::class);

        $this->finder->__invoke($bookedAppointment->id());
    }
}
