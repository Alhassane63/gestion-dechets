<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ImageDownloader
{
    private $httpClient;
    private $targetDirectory;

    public function __construct(HttpClientInterface $httpClient, string $targetDirectory)
    {
        $this->httpClient = $httpClient;
        $this->targetDirectory = $targetDirectory;
    }

    public function downloadImage(string $url, string $filename): bool
    {
        try {
            $response = $this->httpClient->request('GET', $url);
            $content = $response->getContent();

            // Create directory if it doesn't exist
            if (!file_exists($this->targetDirectory)) {
                mkdir($this->targetDirectory, 0777, true);
            }

            // Save the image
            $filepath = $this->targetDirectory . '/' . $filename;
            file_put_contents($filepath, $content);

            return true;
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }
}
