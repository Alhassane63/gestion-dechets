<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-password',
    description: 'Crée un mot de passe hashé pour un utilisateur'
)]
class CreatePasswordCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $question = new Question('Veuillez saisir le mot de passe à hasher : ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);

        $password = $helper->ask($input, $output, $question);

        if (!$password) {
            $output->writeln('<error>Le mot de passe ne peut pas être vide.</error>');
            return Command::FAILURE;
        }

        $user = new User();

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);

        $output->writeln('<info>Mot de passe hashé généré avec succès !</info>');
        $output->writeln('<comment>Hash : </comment>' . $hashedPassword);

        return Command::SUCCESS;
    }
}
