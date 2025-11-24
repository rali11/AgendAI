<?php

namespace App\RAG\VectorStore\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use NeuronAI\RAG\Document;
use Pgvector\Vector;

#[ORM\Entity]
#[ORM\Table(name: 'document')]
final class DoctrineDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'vector', length: 768)]
    private ?Vector $embedding = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $sourceType = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $sourceName = null;

    #[ORM\Column(nullable: true)]
    private ?int $chunkNumber = null;

    #[ORM\Column]
    private array $metadata = [];

    public static function fromDocument(Document $document): self
    {
        $doctrineDocument = new self();
        $doctrineDocument->content = $document->getContent();
        $doctrineDocument->embedding = !empty($document->getEmbedding()) ? new Vector($document->getEmbedding()) : null;
        $doctrineDocument->sourceType = $document->getSourceType();
        $doctrineDocument->sourceName = $document->getSourceName();
        $doctrineDocument->metadata = $document->metadata;

        return $doctrineDocument;
    }
}
