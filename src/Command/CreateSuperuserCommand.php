<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-superuser',
    description: 'Creates a superuser with admin privileges'
)]
class CreateSuperuserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            // Créer un nouveau superuser
            $superuser = new User();
            $superuser->setEmail('admin@example.com');
            $superuser->setPassword($this->passwordHasher->hashPassword($superuser, 'admin123'));
            $superuser->setRoles(['ROLE_USER', 'ROLE_SUPER_ADMIN']);
            $superuser->setNom('Administrateur');
            $superuser->setPrenom('Système');
            $superuser->setTelephone('0612345678');
            $superuser->setAdresse('123 Rue de l\'Admin');

            // Sauvegarder l'utilisateur
            $this->entityManager->persist($superuser);
            $this->entityManager->flush();

            $output->writeln('<info>Superuser créé avec succès !</info>');
            $output->writeln('Email: admin@example.com');
            $output->writeln('Mot de passe: admin123');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $output->writeln('<error>Erreur lors de la création du superuser: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
