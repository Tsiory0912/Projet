<?php

namespace App\Tests\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ArticleControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $articleRepository;
    private string $path = '/article/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->articleRepository = $this->manager->getRepository(Article::class);

        foreach ($this->articleRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Article index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'article[code]' => 'Testing',
            'article[nom]' => 'Testing',
            'article[stock_alerte]' => 'Testing',
            'article[categorie]' => 'Testing',
            'article[unite]' => 'Testing',
            'article[stock]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->articleRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Article();
        $fixture->setCode('My Title');
        $fixture->setNom('My Title');
        $fixture->setStock_alerte('My Title');
        $fixture->setCategorie('My Title');
        $fixture->setUnite('My Title');
        $fixture->setStock('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Article');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Article();
        $fixture->setCode('Value');
        $fixture->setNom('Value');
        $fixture->setStock_alerte('Value');
        $fixture->setCategorie('Value');
        $fixture->setUnite('Value');
        $fixture->setStock('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'article[code]' => 'Something New',
            'article[nom]' => 'Something New',
            'article[stock_alerte]' => 'Something New',
            'article[categorie]' => 'Something New',
            'article[unite]' => 'Something New',
            'article[stock]' => 'Something New',
        ]);

        self::assertResponseRedirects('/article/');

        $fixture = $this->articleRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getCode());
        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getStock_alerte());
        self::assertSame('Something New', $fixture[0]->getCategorie());
        self::assertSame('Something New', $fixture[0]->getUnite());
        self::assertSame('Something New', $fixture[0]->getStock());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Article();
        $fixture->setCode('Value');
        $fixture->setNom('Value');
        $fixture->setStock_alerte('Value');
        $fixture->setCategorie('Value');
        $fixture->setUnite('Value');
        $fixture->setStock('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/article/');
        self::assertSame(0, $this->articleRepository->count([]));
    }
}
