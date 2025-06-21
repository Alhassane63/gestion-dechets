<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoadAdminData extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // Créer l'administrateur
        $admin = new User();
        $admin->setEmail('symfony@example.com');
        $admin->setPassword($this->passwordHasher->hashPassword(
            $admin,
            'Kounta@629'
        ));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setNom('Admin');
        $admin->setPrenom('Symfony');
        $admin->setTelephone('1234567890');
        $admin->setAdresse('123 Rue de Symfony');

        $manager->persist($admin);
        $manager->flush();
    }
}
