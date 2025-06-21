<?php

namespace App\Controller;

use App\Entity\Tournee;
use App\Repository\TourneeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class TourneeController extends AbstractController
{
    #[Route('/admin/tournées', name: 'app_admin_tournées')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(TourneeRepository $tourneeRepository): Response
    {
        $tournées = $tourneeRepository->findAll();
        
        return $this->render('admin/tournées/index.html.twig', [
            'tournées' => $tournées
        ]);
    }
}
