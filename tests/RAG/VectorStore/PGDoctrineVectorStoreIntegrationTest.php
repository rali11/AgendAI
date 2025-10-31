<?php

use Doctrine\ORM\EntityManagerInterface;
use RAG\VectorStore\PGDoctrineVectorStore;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use NeuronAI\RAG\Document;
use NeuronAI\RAG\Embeddings\OllamaEmbeddingsProvider;

class PGDoctrineVectorStoreIntegrationTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private PGDoctrineVectorStore $vectorStore;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->vectorStore = new PGDoctrineVectorStore($this->entityManager);

        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('DELETE FROM document');
    }

    public function testAddDocumentAndSimilaritySearch(): void
    {
        $ollamaEmbedding = new OllamaEmbeddingsProvider('nomic-embed-text');
        $contents = [
            ['text' => 'Raúl Eduardo Correa nació el 10 de Septiembre de 1995.', 'metadata' => ['birthdate' => '10 de Septiembre de 1995']],
            ['text' => 'Raúl Eduardo Correa vive en la ciudad de Temperley, provincia de Buenos Aires, país de Argentina.', 'metadata' => ['location' => 'Temperley, Buenos Aires, Argentina']],
            ['text' => 'Raúl Eduardo Correa trabaja como programador en Mercado Libre.', 'metadata' => ['job' => 'programador en Mercado Libre']]
        ];
        $documents = [];

        foreach ($contents as $content) {
            $doc = new Document();
            $doc->embedding = $ollamaEmbedding->embedText($content['text']);
            $doc->content = $content['text'];
            foreach ($content['metadata'] as $key => $value) {
                $doc->addMetadata($key, $value);
            }
            $documents[] = $doc;
        }

        $this->vectorStore->addDocuments($documents);

        $embeddingQuestion = $ollamaEmbedding->embedText('¿Dónde trabaja Raúl?');
        $results = $this->vectorStore->similaritySearch($embeddingQuestion);
        $docsResults = iterator_to_array($results);

        $this->assertEquals('programador en Mercado Libre', $docsResults[0]->metadata['job']);
        $this->assertEquals($documents[2]->embedding, $docsResults[0]->embedding);
    }
}
