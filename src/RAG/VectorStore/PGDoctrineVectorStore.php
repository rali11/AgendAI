<?php

namespace App\RAG\VectorStore;

use App\RAG\VectorStore\Entity\DoctrineDocument;
use Doctrine\ORM\EntityManager;
use NeuronAI\RAG\Document;
use NeuronAI\RAG\VectorStore\VectorStoreInterface;
use Pgvector\Doctrine\PgvectorSetup;
use Pgvector\Vector;

final class PGDoctrineVectorStore implements VectorStoreInterface
{
    public function __construct(private EntityManager $entityManager, private int $k = 4)
    {
        PgvectorSetup::registerTypes($entityManager);
        $entityManager->getConnection()->executeStatement('CREATE EXTENSION IF NOT EXISTS vector;');
    }

    public function addDocument(Document $document): VectorStoreInterface
    {
        $this->addDocuments([$document]);

        return $this;
    }

    /**
     * @param Document[] $documents
     */
    public function addDocuments(array $documents): VectorStoreInterface
    {
        foreach ($documents as $document) {
            $doctrineDocument = DoctrineDocument::fromDocument($document);
            $this->entityManager->persist($doctrineDocument);
        }

        $this->entityManager->flush();

        return $this;
    }

    /**
     * Return docs most similar to the embedding.
     *
     * @param float[] $embedding
     *
     * @return Document[]
     */
    public function similaritySearch(array $embedding): iterable
    {
        $sql = '
            SELECT 
                id, 
                metadata, 
                content,
                embedding, 
                1 - (embedding <=> :embedding) as cosine_similarity
            FROM document
            ORDER BY embedding <=> :embedding
            LIMIT :limit
        ';

        $databaseConnection = $this->entityManager->getConnection();
        $results = $databaseConnection
            ->executeQuery($sql, ['embedding' => new Vector($embedding), 'limit' => $this->k])
            ->fetchAllAssociative();

        return array_map(function ($row) {
            $document = new Document();
            $document->id = $row['id'];
            $document->embedding = new Vector($row['embedding'])->toArray();
            $document->content = $row['content'];
            $document->setScore($row['cosine_similarity']);

            $metadata = json_decode($row['metadata'], true);
            foreach ($metadata as $key => $value) {
                $document->addMetadata($key, $value);
            }

            return $document;
        }, $results);
    }

    public function deleteBySource(string $sourceType, string $sourceName): VectorStoreInterface
    {
        return $this;
    }
}
