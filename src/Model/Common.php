<?php

namespace App\Model;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use App\Entity\User;
use App\Entity\Post;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class Common
{
    public function getUserId(string $ip, EntityManagerInterface $em): int
    {
        $userData = $em->getRepository(User::class)->findOneBy(['ip' => $ip]);
        if (empty($userData)) {
            $user = new User();
            $user->setIp($ip);
            $em->persist($user);
            $em->flush();
            return $user->getId();
        }
        return $userData->getId();
    }
    public function getPosts(int $userId, EntityManagerInterface $em): array
    {
        $posts = $em->getConnection()->query('SELECT p.* FROM post as p ' .
            'LEFT JOIN  (SELECT post_id FROM user_posts WHERE user_id = ' . $userId . ') as up ON (p.id = up.post_id) ' .
            'WHERE p.views < ' . $_ENV['VIEWS_HIDE_POSTS'] . ' AND up.post_id IS NULL ORDER BY p.hotness DESC')->fetchAll();
        if (!empty($posts)) {
            return $posts;
        } else {
            return [];
        }
    }

    public function markViewedPosts(int $userId, array $ids, EntityManagerInterface $em): void
    {
        $timestamp = time();
        $sql = "REPLACE INTO user_posts (id, user_id, post_id, timestamp) VALUES ";
        foreach ($ids as $k => $id) {
            $sql .= '(:id' . $k . ', :user_id' . $k . ', :post_id' . $k . ', :timestamp' . $k . '),';
        }
        $sql = rtrim($sql, ',');
        $stmt = $em->getConnection()->prepare($sql);
        foreach ($ids as $k => $id) {
            $stmt->bindValue(':id' . $k, 1, \PDO::PARAM_INT);
            $stmt->bindValue(':user_id' . $k, $userId, \PDO::PARAM_INT);
            $stmt->bindValue(':post_id' . $k, $id, \PDO::PARAM_INT);
            $stmt->bindValue(':timestamp' . $k, $timestamp, \PDO::PARAM_INT);
        }
        $stmt->execute();
    }
}
