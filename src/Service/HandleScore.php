<?php
/**
 * Created by PhpStorm.
 * User: mohamedchakiri
 * Date: 29/04/2020
 * Time: 16:28
 */

namespace App\Service;


use App\Entity\Score;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class HandleScore
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function handle(User $user, Int $pts)
    {
        $score = $user->getScore() ?: new Score();

        $score->setUser($user);
        $points = ($user->getScore()) ? $user->getScore()->getPoints() : 0;
        $points += $pts;
        $score->setPoints($points);

        $user->setScore($score);

        $this->manager->persist($score);
        $this->manager->persist($user);
    }
}