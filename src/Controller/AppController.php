<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AppController extends AbstractController
{
    #[Route('/app', name: 'app_app')]
    public function app(): Response
    {
//        $number = random_int(0, 100);

        return $this->render('base.html.twig', [
            //'number' => $number,
        ]);
    }
}
