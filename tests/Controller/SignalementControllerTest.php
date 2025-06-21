<?php

namespace App\Tests\Controller;

use App\Entity\Signalement;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SignalementControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $signalementRepository;
    private string $path = '/yes/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->signalementRepository = $this->manager->getRepository(Signalement::class);

        foreach ($this->signalementRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Signalement index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'signalement[description]' => 'Testing',
            'signalement[lieu]' => 'Testing',
            'signalement[dateSignalement]' => 'Testing',
            'signalement[etat]' => 'Testing',
            'signalement[zone]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->signalementRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Signalement();
        $fixture->setDescription('My Title');
        $fixture->setLieu('My Title');
        $fixture->setDateSignalement('My Title');
        $fixture->setEtat('My Title');
        $fixture->setZone('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Signalement');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Signalement();
        $fixture->setDescription('Value');
        $fixture->setLieu('Value');
        $fixture->setDateSignalement('Value');
        $fixture->setEtat('Value');
        $fixture->setZone('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'signalement[description]' => 'Something New',
            'signalement[lieu]' => 'Something New',
            'signalement[dateSignalement]' => 'Something New',
            'signalement[etat]' => 'Something New',
            'signalement[zone]' => 'Something New',
        ]);

        self::assertResponseRedirects('/yes/');

        $fixture = $this->signalementRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getLieu());
        self::assertSame('Something New', $fixture[0]->getDateSignalement());
        self::assertSame('Something New', $fixture[0]->getEtat());
        self::assertSame('Something New', $fixture[0]->getZone());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Signalement();
        $fixture->setDescription('Value');
        $fixture->setLieu('Value');
        $fixture->setDateSignalement('Value');
        $fixture->setEtat('Value');
        $fixture->setZone('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/yes/');
        self::assertSame(0, $this->signalementRepository->count([]));
    }
}
