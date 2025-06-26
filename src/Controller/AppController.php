<?php

namespace App\Controller;

use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Model\Common;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class AppController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/get_posts', name: 'get_posts', methods: ['GET'])]
    public function get_posts(Request $request, Common $common): Response
    {
        $userId = $common->getUserId($_SERVER['HTTP_X_FORWARDED_FOR'], $this->em);
        $posts = $common->getPosts($userId, $this->em);
        return $this->render('base.html.twig', [
            'userIp' => $_SERVER['HTTP_X_FORWARDED_FOR'],
            'userId' => $userId,
            'posts' => $posts
        ]);
    }

    #[Route('/mark_viewed_posts', name: 'mark_viewed_posts', methods: ['POST'])]
    public function mark_viewed_posts(Request $request, Common $common): JsonResponse
    {
        $postIds = (array)json_decode($request->getContent(), true);
        $userId = $common->getUserId($_SERVER['HTTP_X_FORWARDED_FOR'], $this->em);
        $common->markViewedPosts($userId, $postIds, $this->em);
        return new JsonResponse('ok');
    }
}
