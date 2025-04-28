<?php

namespace App\Tests\Controller;

use App\Entity\Sortie;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SortieControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $sortieRepository;
    private string $path = '/sortie/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->sortieRepository = $this->manager->getRepository(Sortie::class);

        foreach ($this->sortieRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Sortie index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'sortie[date_sortie]' => 'Testing',
            'sortie[quantite]' => 'Testing',
            'sortie[observation]' => 'Testing',
            'sortie[article]' => 'Testing',
            'sortie[agent]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->sortieRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Sortie();
        $fixture->setDate_sortie('My Title');
        $fixture->setQuantite('My Title');
        $fixture->setObservation('My Title');
        $fixture->setArticle('My Title');
        $fixture->setAgent('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Sortie');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Sortie();
        $fixture->setDate_sortie('Value');
        $fixture->setQuantite('Value');
        $fixture->setObservation('Value');
        $fixture->setArticle('Value');
        $fixture->setAgent('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'sortie[date_sortie]' => 'Something New',
            'sortie[quantite]' => 'Something New',
            'sortie[observation]' => 'Something New',
            'sortie[article]' => 'Something New',
            'sortie[agent]' => 'Something New',
        ]);

        self::assertResponseRedirects('/sortie/');

        $fixture = $this->sortieRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getDate_sortie());
        self::assertSame('Something New', $fixture[0]->getQuantite());
        self::assertSame('Something New', $fixture[0]->getObservation());
        self::assertSame('Something New', $fixture[0]->getArticle());
        self::assertSame('Something New', $fixture[0]->getAgent());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Sortie();
        $fixture->setDate_sortie('Value');
        $fixture->setQuantite('Value');
        $fixture->setObservation('Value');
        $fixture->setArticle('Value');
        $fixture->setAgent('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/sortie/');
        self::assertSame(0, $this->sortieRepository->count([]));
    }
}
