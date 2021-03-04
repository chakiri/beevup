<?php


namespace App\Service\User;


use App\Entity\User;
use App\Repository\FavoritRepository;

class favorites
{

    private $favoritRepository;

    public function __construct(FavoritRepository $favoritRepository)
    {
        $this->favoritRepository = $favoritRepository;
    }

    /**
     * Get favorites users for user
     * @param $user
     */
    public function getFavoritesUsers(User $user)
    {
        $favorites = $this->favoritRepository->findBy(['user'=> $user]);
        $favoritesUsers = [];
        foreach ($favorites as $favorit)
        {
            $favoritesUsers[] = $favorit->getFavoritUser();
        }

        return $favoritesUsers;
    }

    /**
     * Get favorites companies for user
     * @param $user
     */
    public function getFavoritesCompanies(User $user)
    {
        $favorites = $this->favoritRepository->findBy(['user'=> $user]);
        $favoritesCompanies = [];
        foreach ($favorites as $favorit)
        {
            if(!$favorit->getCompany()) {
                $favoritesCompanies[] = $favorit->getCompany();
            }
        }

        return $favoritesCompanies;
    }
}