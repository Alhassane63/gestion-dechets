<?php

namespace App\Controller;

use App\Form\ExportFilterType;
use App\Service\StatisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use DateTime;

class ExportController extends AbstractController
{
    private FormFactoryInterface $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    private function getDatesFromFilter($periode, ?DateTime $dateDebut = null, ?DateTime $dateFin = null): array
    {
        $today = new DateTime();
        $yesterday = (new DateTime())->modify('-1 day');
        $startOfWeek = (new DateTime())->modify('last monday')->modify('-1 day');
        $startOfMonth = (new DateTime())->modify('first day of this month');

        switch ($periode) {
            case 'today':
                return [
                    'startDate' => $today,
                    'endDate' => $today
                ];
            case 'yesterday':
                return [
                    'startDate' => $yesterday,
                    'endDate' => $yesterday
                ];
            case 'this_week':
                return [
                    'startDate' => $startOfWeek,
                    'endDate' => $today
                ];
            case 'last_week':
                return [
                    'startDate' => clone $startOfWeek,
                    'endDate' => clone $startOfWeek
                ];
            case 'this_month':
                return [
                    'startDate' => $startOfMonth,
                    'endDate' => $today
                ];
            case 'last_month':
                $lastMonthStart = clone $startOfMonth;
                $lastMonthStart->modify('-1 month');
                return [
                    'startDate' => $lastMonthStart,
                    'endDate' => clone $startOfMonth->modify('-1 day')
                ];
            case 'custom':
                if ($dateDebut && $dateFin) {
                    return [
                        'startDate' => $dateDebut,
                        'endDate' => $dateFin
                    ];
                }
                break;
        }

        // Valeurs par défaut si la période n'est pas reconnue
        return [
            'startDate' => $startOfMonth,
            'endDate' => $today
        ];
    }

    #[Route('/admin/export/filters', name: 'app_export_filters')]
    public function exportFilters(): Response
    {
        $form = $this->formFactory->create(ExportFilterType::class);
        return $this->render('admin/export/filters.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/export/general-pdf', name: 'app_export_general_pdf')]
    public function exportGeneralPdf(StatisticsService $statisticsService, Request $request): Response
    {
        $form = $this->formFactory->create(ExportFilterType::class);

        $stats = $statisticsService->getGeneralStats($startDate, $endDate);

        $html = $this->renderView('admin/reports/stats-general.pdf.twig', [
            'periodeDebut' => $startDate,
            'periodeFin' => $endDate,
            'stats' => $stats
        ]);

        $snappy = new Pdf();
        $snappy->setOption('encoding', 'UTF-8');
        $snappy->setOption('margin-top', 20);
        $snappy->setOption('margin-right', 20);
        $snappy->setOption('margin-bottom', 20);
        $snappy->setOption('margin-left', 20);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="stats-generales.pdf"');
        $response->setContent($snappy->getOutputFromHtml($html));

        return $response;
    }

    #[Route('/admin/export/agents-pdf', name: 'app_export_agents_pdf')]
    #[IsGranted('ROLE_ADMIN')]
    public function exportAgentsPdf(StatisticsService $statisticsService, Request $request): Response
    {
        $form = $this->formFactory->create(ExportFilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $dates = $this->getDatesFromFilter(
                $data['periode'],
                $data['dateDebut'] ? new DateTime($data['dateDebut']) : null,
                $data['dateFin'] ? new DateTime($data['dateFin']) : null
            );
        } else {
            $dates = $this->getDatesFromFilter('this_month');
        }

        $agentsStats = $statisticsService->getAgentStats($dates['startDate'], $dates['endDate']);

        $html = $this->renderView('admin/reports/stats-agent.pdf.twig', [
            'periodeDebut' => $dates['startDate'],
            'periodeFin' => $dates['endDate'],
            'agentsStats' => $agentsStats
        ]);

        $snappy = new Pdf();
        $snappy->setOption('encoding', 'UTF-8');
        $snappy->setOption('margin-top', 20);
        $snappy->setOption('margin-right', 20);
        $snappy->setOption('margin-bottom', 20);
        $snappy->setOption('margin-left', 20);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="stats-agents.pdf"');
        $response->setContent($snappy->getOutputFromHtml($html));

        return $response;
    }

    #[Route('/admin/export/zones-pdf', name: 'app_export_zones_pdf')]
    #[IsGranted('ROLE_ADMIN')]
    public function exportZonesPdf(StatisticsService $statisticsService, Request $request): Response
    {
        $form = $this->formFactory->create(ExportFilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $dates = $this->getDatesFromFilter(
                $data['periode'],
                $data['dateDebut'] ? new DateTime($data['dateDebut']) : null,
                $data['dateFin'] ? new DateTime($data['dateFin']) : null
            );
        } else {
            $dates = $this->getDatesFromFilter('this_month');
        }

        $zonesStats = $statisticsService->getZoneStats($dates['startDate'], $dates['endDate']);

        $html = $this->renderView('admin/reports/stats-zone.pdf.twig', [
            'periodeDebut' => $dates['startDate'],
            'periodeFin' => $dates['endDate'],
            'zonesStats' => $zonesStats
        ]);

        $snappy = new Pdf();
        $snappy->setOption('encoding', 'UTF-8');
        $snappy->setOption('margin-top', 20);
        $snappy->setOption('margin-right', 20);
        $snappy->setOption('margin-bottom', 20);
        $snappy->setOption('margin-left', 20);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="stats-zones.pdf"');
        $response->setContent($snappy->getOutputFromHtml($html));

        return $response;
    }

    #[Route('/admin/export/collectes-excel', name: 'app_export_collectes_excel')]
    #[IsGranted('ROLE_ADMIN')]
    public function exportCollectesExcel(Request $request): StreamedResponse
    {
        $form = $this->formFactory->create(ExportFilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $dates = $this->getDatesFromFilter(
                $data['periode'],
                $data['dateDebut'] ? new DateTime($data['dateDebut']) : null,
                $data['dateFin'] ? new DateTime($data['dateFin']) : null
            );
        } else {
            $dates = $this->getDatesFromFilter('this_month');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // En-têtes
        $headers = [
            'Agent', 'Zone', 'Point de Collecte', 'Type de Déchets', 'Date', 'Heure', 'Collecte Effectuée', 'Commentaire'
        ];

        $row = 1;
        foreach ($headers as $col => $header) {
            $cell = $sheet->getCellByColumnAndRow($col + 1, $row);
            $cell->setValue($header);
            $cell->getStyle()->getFont()->setBold(true);
            $cell->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $cell->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        // Données
        $collectes = $this->getDoctrine()->getRepository(Collecte::class)
            ->createQueryBuilder('c')
            ->where('c.dateCollecte BETWEEN :start AND :end')
            ->setParameter('start', $dates['startDate'])
            ->setParameter('end', $dates['endDate'])
            ->getQuery()
            ->getResult();
        $row = 2;

        foreach ($collectes as $collecte) {
            $sheet->setCellValueByColumnAndRow(1, $row, $collecte->getAgent()->getPrenom() . ' ' . $collecte->getAgent()->getNom());
            $sheet->setCellValueByColumnAndRow(2, $row, $collecte->getPointCollecte()->getZone()->getNom());
            $sheet->setCellValueByColumnAndRow(3, $row, $collecte->getPointCollecte()->getAdresse());
            $sheet->setCellValueByColumnAndRow(4, $row, $collecte->getPointCollecte()->getTypeDechets());
            $sheet->setCellValueByColumnAndRow(5, $row, $collecte->getDateCollecte()->format('d/m/Y'));
            $sheet->setCellValueByColumnAndRow(6, $row, $collecte->getHeureCollecte()->format('H:i'));
            $sheet->setCellValueByColumnAndRow(7, $row, $collecte->getCollecteEffectuee() ? 'Oui' : 'Non');
            $sheet->setCellValueByColumnAndRow(8, $row, $collecte->getCommentaire() ?: '');

            $row++;
        }

        // Auto-adjust column widths
        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        // Style
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A1:H' . ($row - 1))->applyFromArray($styleArray);

        // Response
        $response = new StreamedResponse(function() use ($spreadsheet) {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="collectes.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    #[Route('/admin/export/signalements-excel', name: 'app_export_signalements_excel')]
    #[IsGranted('ROLE_ADMIN')]
    public function exportSignalementsExcel(Request $request): StreamedResponse
    {
        $form = $this->formFactory->create(ExportFilterType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $dates = $this->getDatesFromFilter(
                $data['periode'],
                $data['dateDebut'] ? new DateTime($data['dateDebut']) : null,
                $data['dateFin'] ? new DateTime($data['dateFin']) : null
            );
        } else {
            $dates = $this->getDatesFromFilter('this_month');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // En-têtes
        $headers = [
            'Citoyen', 'Zone', 'Type de Problème', 'Lieu', 'Description', 'Date', 'État', 'Statut', 'Date de Traitement', 'Commentaire de Traitement'
        ];

        $row = 1;
        foreach ($headers as $col => $header) {
            $cell = $sheet->getCellByColumnAndRow($col + 1, $row);
            $cell->setValue($header);
            $cell->getStyle()->getFont()->setBold(true);
            $cell->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $cell->getStyle()->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        // Données
        $signalements = $this->getDoctrine()->getRepository(Signalement::class)
            ->createQueryBuilder('s')
            ->where('s.dateSignalement BETWEEN :start AND :end')
            ->setParameter('start', $dates['startDate'])
            ->setParameter('end', $dates['endDate'])
            ->getQuery()
            ->getResult();
        $row = 2;

        foreach ($signalements as $signalement) {
            $sheet->setCellValueByColumnAndRow(1, $row, $signalement->getCitoyen()->getPrenom() . ' ' . $signalement->getCitoyen()->getNom());
            $sheet->setCellValueByColumnAndRow(2, $row, $signalement->getZone()->getNom());
            $sheet->setCellValueByColumnAndRow(3, $row, $signalement->getType());
            $sheet->setCellValueByColumnAndRow(4, $row, $signalement->getLieu());
            $sheet->setCellValueByColumnAndRow(5, $row, $signalement->getDescription());
            $sheet->setCellValueByColumnAndRow(6, $row, $signalement->getDateSignalement()->format('d/m/Y H:i'));
            $sheet->setCellValueByColumnAndRow(7, $row, $signalement->getEtat());
            $sheet->setCellValueByColumnAndRow(8, $row, $signalement->getStatut());
            $sheet->setCellValueByColumnAndRow(9, $row, $signalement->getDateTraitement() ? $signalement->getDateTraitement()->format('d/m/Y H:i') : '');
            $sheet->setCellValueByColumnAndRow(10, $row, $signalement->getCommentaireTraitement() ?: '');

            $row++;
        }

        // Auto-adjust column widths
        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        // Style
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A1:J' . ($row - 1))->applyFromArray($styleArray);

        // Response
        $response = new StreamedResponse(function() use ($spreadsheet) {
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="signalements.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
