<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crée un compte administrateur avec les informations spécifiées'
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Email de l\'administrateur')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Mot de passe de l\'administrateur')
            ->addOption('nom', null, InputOption::VALUE_REQUIRED, 'Nom de l\'administrateur')
            ->addOption('prenom', null, InputOption::VALUE_REQUIRED, 'Prénom de l\'administrateur')
            ->addOption('telephone', null, InputOption::VALUE_REQUIRED, 'Téléphone de l\'administrateur')
            ->addOption('adresse', null, InputOption::VALUE_REQUIRED, 'Adresse de l\'administrateur');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $admin = new User();
        $admin->setEmail($input->getOption('email'));
        $admin->setPassword($this->passwordHasher->hashPassword(
            $admin,
            $input->getOption('password')
        ));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setNom($input->getOption('nom'));
        $admin->setPrenom($input->getOption('prenom'));
        $admin->setTelephone($input->getOption('telephone'));
        $admin->setAdresse($input->getOption('adresse'));

        try {
            $this->entityManager->persist($admin);
            $this->entityManager->flush();

            $output->writeln('<info>Administrateur créé avec succès !</info>');
            $output->writeln('<comment>Email : ' . $input->getOption('email') . '</comment>');
            $output->writeln('<comment>Mot de passe : ' . $input->getOption('password') . '</comment>');
            $output->writeln('<comment>Rôle : ROLE_ADMIN</comment>');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Erreur lors de la création de l\'administrateur : ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
