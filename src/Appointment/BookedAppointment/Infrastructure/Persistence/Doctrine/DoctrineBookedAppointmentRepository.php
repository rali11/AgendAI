<?php

namespace App\Appointment\BookedAppointment\Infrastructure\Persistence\Doctrine;

use App\Appointment\BookedAppointment\Domain\BookedAppointment;
use App\Appointment\BookedAppointment\Domain\BookedAppointmentRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineBookedAppointmentRepository extends ServiceEntityRepository implements BookedAppointmentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookedAppointment::class);
    }

    public function save(BookedAppointment $bookedAppointment): void
    {
        $this->getEntityManager()->persist($bookedAppointment);
        $this->getEntityManager()->flush();
    }

    public function update(BookedAppointment $bookedAppointment): void
    {
        $this->getEntityManager()->flush();
    }

    public function search(string $id): ?BookedAppointment
    {
        $queryBuilder = $this->createQueryBuilder('bookedAppointment');
        $queryBuilder
            ->where(
                $queryBuilder->expr()->andX(
                    'bookedAppointment.id = :id',
                    'bookedAppointment.isActive = true'
                )
            )
            ->setParameter('id', $id);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function searchByAvailableAppointmentId(string $availableAppointmentId): array
    {
        $queryBuilder = $this->createQueryBuilder('bookedAppointment');
        $queryBuilder
            ->where(
                $queryBuilder->expr()->andX(
                    'bookedAppointment.availableAppointmentId = :availableAppointmentId',
                    'bookedAppointment.isActive = true'
                )
            )
            ->setParameter('availableAppointmentId', $availableAppointmentId);

        return $queryBuilder->getQuery()->getResult();
    }
}
