<?php

namespace App\Tests\Appointment\BookedAppointment\Application\Remove;

use App\Appointment\BookedAppointment\Application\Remove\BookedAppointmentRemover;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentRepository;
use App\Tests\Appointment\BookedAppointment\Domain\BookedAppointmentMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class BookedAppointmentRemoverTest extends TestCase
{
    private BookedAppointmentRemover $remover;

    private BookedAppointmentRepository|MockObject $repository;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(BookedAppointmentRepository::class);
        $this->remover = new BookedAppointmentRemover($this->repository);
    }

    public function testItShouldRemoveExistingBookedAppointment(): void
    {
        $bookedAppointment = BookedAppointmentMother::random();

        $this->repository
            ->expects($this->once())
            ->method('search')
            ->with($bookedAppointment->id())
            ->willReturn($bookedAppointment);

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with($bookedAppointment);

        $bookedAppointment->remove();
        $this->remover->__invoke($bookedAppointment->id());
    }
}
