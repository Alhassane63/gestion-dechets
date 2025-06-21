<?php

namespace App\Command;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:check-users',
    description: 'Vérifie les utilisateurs existants dans la base de données'
)]
class CheckUsersCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            // Vérifier si un utilisateur existe déjà
            $users = $this->userRepository->findAll();
            
            if (empty($users)) {
                $output->writeln('<error>Aucun utilisateur trouvé dans la base de données.</error>');
                return Command::SUCCESS;
            }

            $output->writeln('<info>Utilisateurs existants :</info>');
            foreach ($users as $user) {
                $output->writeln('');
                $output->writeln('Email: ' . $user->getEmail());
                $output->writeln('Roles: ' . implode(', ', $user->getRoles()));
                $output->writeln('Nom: ' . $user->getNom());
                $output->writeln('Prénom: ' . $user->getPrenom());
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $output->writeln('<error>Erreur lors de la vérification des utilisateurs: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
