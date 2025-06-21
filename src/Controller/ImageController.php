<?php

namespace App\Controller;

use App\Utils\ImageDownloader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    #[Route('/download-images', name: 'app_download_images')]
    public function downloadImages(): Response
    {
        $imageDownloader = new ImageDownloader(
            HttpClient::create(),
            $this->getParameter('kernel.project_dir') . '/public/images/dashboard'
        );

        // Images libres de droits de Pexels
        $images = [
            'tri-dechets.jpg' => 'https://images.pexels.com/photos/100582/pexels-photo-100582.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2',
            'poubelles.jpg' => 'https://images.pexels.com/photos/100581/pexels-photo-100581.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2',
            'collecte.jpg' => 'https://images.pexels.com/photos/100580/pexels-photo-100580.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=2'
        ];

        foreach ($images as $filename => $url) {
            $imageDownloader->downloadImage($url, $filename);
        }

        return $this->json(['message' => 'Images téléchargées avec succès']);
    }
}
