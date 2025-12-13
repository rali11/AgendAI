<?php

namespace App\Tests\Appointment\AvailableAppointment\Infrastructure;

use App\Appointment\AvailableAppointment\Infrastructure\DoctrineAvailableAppointmentRepository;
use App\Tests\Appointment\AvailableAppointment\Domain\AvailableAppointmentMother;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DoctrineAvailableAppointmentRepositoryTest extends KernelTestCase
{
    private DoctrineAvailableAppointmentRepository $repository;

    public function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->repository = $container->get(DoctrineAvailableAppointmentRepository::class);

        $entityManager = $container->get('doctrine')->getManager();
        $connection = $entityManager->getConnection();
        $connection->executeStatement('DELETE FROM available_appointment');
    }

    public function testItShouldSaveAnAvailableAppointment(): void
    {
        $availableAppointment = AvailableAppointmentMother::random();

        $this->expectNotToPerformAssertions();

        $this->repository->save($availableAppointment);
    }

    public function testItShouldFindAnAvailableAppointmentById(): void
    {
        $availableAppointment = AvailableAppointmentMother::random();

        $this->repository->save($availableAppointment);

        $fetchedAvailableAppointment = $this->repository->search($availableAppointment->Id());

        $this->assertEquals($availableAppointment, $fetchedAvailableAppointment);
    }

    public function testItShouldUpdateExistingAvailableAppointment(): void
    {
        $availableAppointment = AvailableAppointmentMother::random();
        $this->repository->save($availableAppointment);

        $availableAppointment->remove();
        $this->repository->update($availableAppointment);

        $fetchedAvailableAppointment = $this->repository->search($availableAppointment->Id());

        $this->assertNull($fetchedAvailableAppointment);
    }

    public function testItShouldFindExistingAvailableAppointmentOverlappedByNewOne(): void
    {
        $existingAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-07-01 10:00:00'),
            durationInMinutes: 30
        );

        $newAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-07-01 10:15:00'),
            durationInMinutes: 15
        );

        $this->repository->save($existingAvailableAppointment);

        $fetchedAvailableAppointments = $this->repository->searchByOverlapping(
            $newAvailableAppointment->date(),
            $newAvailableAppointment->durationInMinutes()
        );

        $this->assertEquals($fetchedAvailableAppointments[0], $existingAvailableAppointment);
    }

    public function testItShouldNotFindExistingAvailableAppointmentOverlappedByNewOne(): void
    {
        $existingAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-07-01 10:00:00'),
            durationInMinutes: 30
        );

        $newAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-07-01 10:30:00'),
            durationInMinutes: 30
        );

        $this->repository->save($existingAvailableAppointment);

        $fetchedAvailableAppointments = $this->repository->searchByOverlapping(
            $newAvailableAppointment->date(),
            $newAvailableAppointment->durationInMinutes()
        );

        $this->assertEmpty($fetchedAvailableAppointments);
    }

    public function testItShouldFindExistingAvailableAppointmentWhenNewOneStartsEarlierAndOverlaps(): void
    {
        $existingAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-07-01 10:00:00'),
            durationInMinutes: 30
        );

        $newAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-07-01 09:45:00'),
            durationInMinutes: 30
        );

        $this->repository->save($existingAvailableAppointment);

        $fetchedAvailableAppointments = $this->repository->searchByOverlapping(
            $newAvailableAppointment->date(),
            $newAvailableAppointment->durationInMinutes()
        );

        $this->assertEquals($fetchedAvailableAppointments[0], $existingAvailableAppointment);
    }

    public function testItShouldFindExistingAvailableAppointmentWhenNewOneStartsLaterAndOverlaps(): void
    {
        $existingAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-07-01 10:00:00'),
            durationInMinutes: 30
        );

        $newAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-07-01 10:20:00'),
            durationInMinutes: 30
        );

        $this->repository->save($existingAvailableAppointment);

        $fetchedAvailableAppointments = $this->repository->searchByOverlapping(
            $newAvailableAppointment->date(),
            $newAvailableAppointment->durationInMinutes()
        );

        $this->assertEquals($fetchedAvailableAppointments[0], $existingAvailableAppointment);
    }

    public function testItShouldFindExistingAvailableAppointmentWhenNewOneCompletelyContainsIt(): void
    {
        $existingAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-07-01 10:15:00'),
            durationInMinutes: 15
        );

        $newAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-07-01 10:00:00'),
            durationInMinutes: 60
        );

        $this->repository->save($existingAvailableAppointment);

        $fetchedAvailableAppointments = $this->repository->searchByOverlapping(
            $newAvailableAppointment->date(),
            $newAvailableAppointment->durationInMinutes()
        );

        $this->assertEquals($fetchedAvailableAppointments[0], $existingAvailableAppointment);
    }

    public function testItShouldFindExistingAvailableAppointmentWhenNewOneHasExactSameTiming(): void
    {
        $existingAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-07-01 10:00:00'),
            durationInMinutes: 30
        );

        $newAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-07-01 10:00:00'),
            durationInMinutes: 30
        );

        $this->repository->save($existingAvailableAppointment);

        $fetchedAvailableAppointments = $this->repository->searchByOverlapping(
            $newAvailableAppointment->date(),
            $newAvailableAppointment->durationInMinutes()
        );

        $this->assertEquals($fetchedAvailableAppointments[0], $existingAvailableAppointment);
    }

    public function testItShouldNotFindAnyAvailableAppointmentWhenNewOneIsOnDifferentDay(): void
    {
        $existingAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-07-01 10:00:00'),
            durationInMinutes: 30
        );

        $newAvailableAppointment = AvailableAppointmentMother::create(
            date: new \DateTimeImmutable('2024-07-02 10:00:00'),
            durationInMinutes: 30
        );

        $this->repository->save($existingAvailableAppointment);

        $fetchedAvailableAppointments = $this->repository->searchByOverlapping(
            $newAvailableAppointment->date(),
            $newAvailableAppointment->durationInMinutes()
        );

        $this->assertEmpty($fetchedAvailableAppointments);
    }
}
