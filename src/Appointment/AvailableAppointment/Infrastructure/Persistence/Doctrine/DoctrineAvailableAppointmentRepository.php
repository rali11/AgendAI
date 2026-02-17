<?php

namespace App\Appointment\AvailableAppointment\Infrastructure\Persistence\Doctrine;

use App\Appointment\AvailableAppointment\Domain\AvailableAppointment;
use App\Appointment\AvailableAppointment\Domain\AvailableAppointmentRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrineAvailableAppointmentRepository extends ServiceEntityRepository implements AvailableAppointmentRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AvailableAppointment::class);
    }

    public function save(AvailableAppointment $availableAppointment): void
    {
        $this->getEntityManager()->persist($availableAppointment);
        $this->getEntityManager()->flush();
    }

    public function update(AvailableAppointment $availableAppointment): void
    {
        $this->getEntityManager()->flush();
    }

    public function search(string $id): ?AvailableAppointment
    {
        $queryBuilder = $this->createQueryBuilder('availableAppointment');
        $queryBuilder
            ->where(
                $queryBuilder->expr()->andX(
                    'availableAppointment.id = :id',
                    'availableAppointment.isActive = true'
                )
            )
            ->setParameter('id', $id);

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function searchByOverlapping(\DateTimeImmutable $date, int $durationInMinutes): array
    {
        $endDateTime = $date->modify("+{$durationInMinutes} minutes");

        $queryBuilder = $this->createQueryBuilder('availableAppointment');
        $queryBuilder
            ->where(
                $queryBuilder->expr()->andX(
                    'availableAppointment.date < :endTime',
                    'DATE_ADD(availableAppointment.date, availableAppointment.durationInMinutes, \'MINUTE\') > :startTime'
                )
            )
            ->setParameter('startTime', $date)
            ->setParameter('endTime', $endDateTime);

        return $queryBuilder->getQuery()->getResult();
    }
}
