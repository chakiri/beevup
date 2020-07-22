<?php
/**
 * Created by PhpStorm.
 * User: mohamedchakiri
 * Date: 22/07/2020
 * Time: 17:02
 */

namespace App\Service;


use App\Repository\PostRepository;
use App\Repository\UserHistoricRepository;
use Symfony\Component\Security\Core\Security;

class LastOpportunities
{
    private $security;

    private $postRepository;

    private $userHistoricRepository;

    public function __construct(Security $security, PostRepository $postRepository, UserHistoricRepository $userHistoricRepository)
    {
        $this->security = $security;
        $this->postRepository = $postRepository;
        $this->userHistoricRepository = $userHistoricRepository;
    }

    public function get()
    {
        $historic = $this->userHistoricRepository->findOneBy(['user' => $this->security->getUser()]);
        $opportunities = $this->postRepository->findOpportunitiesByDate($this->security->getUser(), $historic->getLastLogout());

        return $opportunities;
    }
}