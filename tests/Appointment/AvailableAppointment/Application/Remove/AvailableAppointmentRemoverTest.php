<?php

namespace App\Tests\Appointment\AvailableAppointment\Application\Remove;

use App\Appointment\AvailableAppointment\Application\Remove\AvailableAppointmentRemover;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentRepository;
use App\Tests\Appointment\AvailableAppointment\Domain\AvailableAppointmentMother;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class AvailableAppointmentRemoverTest extends TestCase
{
    private AvailableAppointmentRemover $remover;

    private AvailableAppointmentRepository|MockObject $repository;

    public function setUp(): void
    {
        $this->repository = $this->createMock(AvailableAppointmentRepository::class);
        $this->remover = new AvailableAppointmentRemover($this->repository);
    }

    public function testItShouldRemoveExistingAvailableAppointment(): void
    {
        $availableAppointment = AvailableAppointmentMother::random();

        $this->repository
            ->expects($this->once())
            ->method('search')
            ->with($availableAppointment->Id())
            ->willReturn($availableAppointment);

        $this->repository
            ->expects($this->once())
            ->method('update')
            ->with($availableAppointment);

        $availableAppointment->remove();
        $this->remover->__invoke($availableAppointment->id());
    }
}
