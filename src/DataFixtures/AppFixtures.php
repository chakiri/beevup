<?php

namespace App\DataFixtures;

use App\Entity\Service;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr-FR');

        $users = [1, 2, 3, 4];

        $types = ['generic', 'local-bv', 'local_company', 'national'];

        for ($i = 1; $i < 20; $i ++){
            $service = new Service();

            //$userId = $users[mt_rand(0, count($users) - 1)];
            $userId = 1;
            $user = $this->userRepository->findOneBy(['id' => $userId]);
            $service
                ->setTitle($faker->sentence($nbWords = 5, $variableNbWords = true))
                ->setIntroduction($faker->sentence($nbWords = 10, $variableNbWords = true))
                ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>' )
                ->setCreatedAt(new \DateTime('now'))
                ->setUser($user)
                ->setType($types[mt_rand(0, count($types) - 1)])
                ->setPrice($faker->randomNumber(2))
            ;

            $manager->persist($service);
        }


        $manager->flush();
    }
}
