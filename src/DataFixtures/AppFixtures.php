<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();

        $company = new Company();

        $company->setName('station F')
            ->setIntroduction('Intro')
            ->setDescription('Description')
        ;

        $manager->persist($company);

        $channels = ['general', 'random'];

        $user->setUsername('nihel')
            ->setEmail('nihel@gmail.com')
            ->setPassword('password')
            ->setCompany($company)
            ->setChannels($channels)
        ;

        $manager->persist($user);

        $manager->flush();
    }
}
