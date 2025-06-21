<?php

namespace App\Controller;

use App\Entity\Zone;
use App\Entity\ProblemeSignale;
use App\Form\ProblemeSignaleType;
use App\Repository\ZoneRepository;
use App\Repository\TourneeRepository;
use App\Repository\PointCollecteRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class CitoyenController extends AbstractController
{
    #[Route('/citoyen', name: 'app_citoyen_dashboard')]
    #[IsGranted('ROLE_CITOYEN')]
    public function dashboard(
        ZoneRepository $zoneRepository,
        TourneeRepository $tourneeRepository,
        Security $security
    ): Response {
        $citoyen = $security->getUser();
        $zone = $citoyen->getZone();
        
        $prochainesTournées = $tourneeRepository->findBy([
            'zone' => $zone,
            'date' => new \DateTime('today')
        ], ['date' => 'ASC']);

        return $this->render('citoyen/dashboard.html.twig', [
            'prochaines_tournées' => $prochainesTournées
        ]);
    }

    #[Route('/citoyen/signalement', name: 'app_citoyen_signalement')]
    #[IsGranted('ROLE_CITOYEN')]
    #[Route('/admin/citoyens', name: 'app_admin_citoyens')]
    #[IsGranted('ROLE_ADMIN')]
    public function listCitoyens(UserRepository $userRepository): Response
    {
        $citoyens = $userRepository->findBy(['roles' => 'ROLE_CITOYEN']);
        return $this->render('citoyen/list.html.twig', [
            'citoyens' => $citoyens
        ]);
    }

    public function signalement(
        Request $request,
        Security $security
    ): Response {
        $signalement = new ProblemeSignale();
        $signalement->setCitoyen($this->getUser());
        $signalement->setDate(new \DateTime());

        $form = $this->createForm(ProblemeSignaleType::class, $signalement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($signalement);
            $entityManager->flush();
            
            $this->addFlash('success', 'Signalement enregistré avec succès');
            return $this->redirectToRoute('app_citoyen_dashboard');
        }

        return $this->render('citoyen/signalement.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // Autres méthodes pour gérer les demandes de collecte et les notifications
}
