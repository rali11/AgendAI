<?php

namespace App\Tests\Appointment\BookedAppointment\Infrastructure;

use App\Appointment\AvailableAppointment\Infrastructure\DoctrineAvailableAppointmentRepository;
use App\Appointment\BookedAppointment\Infrastructure\DoctrineBookedAppointmentRepository;
use App\Tests\Appointment\AvailableAppointment\Domain\AvailableAppointmentMother;
use App\Tests\Appointment\BookedAppointment\Domain\BookedAppointmentMother;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DoctrineBookedAppointmentRepositoryTest extends KernelTestCase
{
    private DoctrineBookedAppointmentRepository $repository;
    private DoctrineAvailableAppointmentRepository $availableAppointmentRepository;

    public function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->repository = $container->get(DoctrineBookedAppointmentRepository::class);
        $this->availableAppointmentRepository = $container->get(DoctrineAvailableAppointmentRepository::class);

        $entityManager = $container->get('doctrine')->getManager();
        $connection = $entityManager->getConnection();
        $connection->executeStatement('DELETE FROM booked_appointment');
        $connection->executeStatement('DELETE FROM available_appointment');
    }

    public function testItShouldSaveABookedAppointment(): void
    {
        $availableAppointment = AvailableAppointmentMother::random();
        $this->availableAppointmentRepository->save($availableAppointment);

        $bookedAppointment = BookedAppointmentMother::create(
            availableAppointmentId: $availableAppointment->id()
        );

        $this->expectNotToPerformAssertions();

        $this->repository->save($bookedAppointment);
    }

    public function testItShouldFindABookedAppointmentById(): void
    {
        $availableAppointment = AvailableAppointmentMother::random();
        $this->availableAppointmentRepository->save($availableAppointment);

        $bookedAppointment = BookedAppointmentMother::create(
            availableAppointmentId: $availableAppointment->id()
        );
        $this->repository->save($bookedAppointment);

        $fetchedBookedAppointment = $this->repository->search($bookedAppointment->id());

        $this->assertEquals($bookedAppointment, $fetchedBookedAppointment);
    }

    public function testItShouldReturnNullWhenBookedAppointmentDoesNotExist(): void
    {
        $fetchedBookedAppointment = $this->repository->search(Uuid::uuid4()->toString());

        $this->assertNull($fetchedBookedAppointment);
    }

    public function testItShouldUpdateExistingBookedAppointment(): void
    {
        $availableAppointment = AvailableAppointmentMother::random();
        $this->availableAppointmentRepository->save($availableAppointment);

        $bookedAppointment = BookedAppointmentMother::create(
            availableAppointmentId: $availableAppointment->id()
        );
        $this->repository->save($bookedAppointment);

        $bookedAppointment->remove();
        $this->repository->update($bookedAppointment);

        $fetchedBookedAppointment = $this->repository->search($bookedAppointment->id());

        $this->assertNull($fetchedBookedAppointment);
    }

    public function testItShouldUpdateEmailAndAvailableAppointmentId(): void
    {
        $availableAppointment = AvailableAppointmentMother::random();
        $this->availableAppointmentRepository->save($availableAppointment);

        $newAvailableAppointment = AvailableAppointmentMother::random();
        $this->availableAppointmentRepository->save($newAvailableAppointment);

        $bookedAppointment = BookedAppointmentMother::create(
            availableAppointmentId: $availableAppointment->id(),
            email: 'original@example.com'
        );
        $this->repository->save($bookedAppointment);

        $newEmail = 'updated@example.com';
        $bookedAppointment->update($newAvailableAppointment->id(), $newEmail);
        $this->repository->update($bookedAppointment);

        $fetchedBookedAppointment = $this->repository->search($bookedAppointment->id());

        $this->assertEquals($newEmail, $fetchedBookedAppointment->email());
        $this->assertEquals($newAvailableAppointment->id(), $fetchedBookedAppointment->availableAppointmentId());
    }

    public function testItShouldSearchByAvailableAppointmentId(): void
    {
        $availableAppointment = AvailableAppointmentMother::random();
        $this->availableAppointmentRepository->save($availableAppointment);

        $bookedAppointment1 = BookedAppointmentMother::create(
            availableAppointmentId: $availableAppointment->id(),
            email: 'user1@example.com'
        );
        $bookedAppointment2 = BookedAppointmentMother::create(
            availableAppointmentId: $availableAppointment->id(),
            email: 'user2@example.com'
        );
        $this->repository->save($bookedAppointment1);
        $this->repository->save($bookedAppointment2);

        $fetchedBookedAppointments = $this->repository->searchByAvailableAppointmentId($availableAppointment->id());

        $this->assertCount(2, $fetchedBookedAppointments);
    }

    public function testItShouldReturnEmptyArrayWhenNoBookedAppointmentsForAvailableAppointment(): void
    {
        $availableAppointment = AvailableAppointmentMother::random();
        $this->availableAppointmentRepository->save($availableAppointment);

        $fetchedBookedAppointments = $this->repository->searchByAvailableAppointmentId($availableAppointment->id());

        $this->assertEmpty($fetchedBookedAppointments);
    }

    public function testItShouldNotReturnRemovedBookedAppointmentsWhenSearchingByAvailableAppointmentId(): void
    {
        $availableAppointment = AvailableAppointmentMother::random();
        $this->availableAppointmentRepository->save($availableAppointment);

        $activeBookedAppointment = BookedAppointmentMother::create(
            availableAppointmentId: $availableAppointment->id(),
            email: 'active@example.com'
        );
        $removedBookedAppointment = BookedAppointmentMother::create(
            availableAppointmentId: $availableAppointment->id(),
            email: 'removed@example.com'
        );
        $this->repository->save($activeBookedAppointment);
        $this->repository->save($removedBookedAppointment);

        $removedBookedAppointment->remove();
        $this->repository->update($removedBookedAppointment);

        $fetchedBookedAppointments = $this->repository->searchByAvailableAppointmentId($availableAppointment->id());

        $this->assertCount(1, $fetchedBookedAppointments);
        $this->assertEquals($activeBookedAppointment->id(), $fetchedBookedAppointments[0]->id());
    }
}
