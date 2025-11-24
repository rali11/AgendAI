<?php

namespace App\EventListener\Command;

use Doctrine\ORM\EntityManagerInterface;
use Pgvector\Doctrine\PgvectorSetup;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'console.command')]
final class PgVectorSetupCommandListener
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(): void
    {
        PgvectorSetup::registerTypes($this->entityManager);
        $this->entityManager->getConnection()->executeStatement('CREATE EXTENSION IF NOT EXISTS vector;');
    }
}
