<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\CompanyCategory;
use App\Entity\Profile;
use App\Entity\ServiceCategory;
use App\Entity\Store;
use App\Entity\Topic;
use App\Entity\TopicType;
use App\Entity\TypeService;
use App\Entity\User;
use App\Entity\UserFunction;
use App\Entity\UserType;
use App\Repository\CategoryRepository;
use App\Repository\TopicTypeRepository;
use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $userRepository;

    private $companyCategoryRepository;

    private $userTypeRepository;

    private $passwordEncoder;

    private $topicTypeRepository;

    public function __construct(UserRepository $userRepository, CategoryRepository $companyCategoryRepository, UserTypeRepository $userTypeRepository, UserPasswordEncoderInterface $passwordEncoder, TopicTypeRepository $topicTypeRepository)
    {
        $this->userRepository = $userRepository;
        $this->companyCategoryRepository = $companyCategoryRepository;
        $this->userTypeRepository = $userTypeRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->topicTypeRepository = $topicTypeRepository;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr-FR');

        //company category
        $categories = ['distribution', 'vente'];
        foreach ($categories as $category){
            $companyCategory = new CompanyCategory();
            $companyCategory->setName($category);
            $manager->persist($companyCategory);
        }

        //service category
        $categories = ['fourniture', 'papeterie'];
        foreach ($categories as $category){
            $serviceCategory = new ServiceCategory();
            $serviceCategory->setName($category);
            $manager->persist($serviceCategory);
        }

        //service type
        $types = ['plateform', 'store', 'company', 'foreign'];
        foreach ($types as $type){
            $serviceType = new TypeService();
            $serviceType->setName($type);
            $manager->persist($serviceType);
        }

        //user Type
        $types = ['admin magasin', 'conseiller magasin', 'admin entreprise', 'patron magasin', 'admin plateform', 'collaborateur entreprise'];
        foreach ($types as $type){
            $userType = new UserType();
            $userType->setName($type);
            $manager->persist($userType);
        }

        //functions user
        $functions = ['chef de projet', 'responsable digital', 'responsable marketing', 'developpeur', 'gerant'];
        foreach ($functions as $function){
            $userFunction = new UserFunction();
            $userFunction->setName($function);
            $manager->persist($userFunction);
        }

        //store
        $store = new Store();
        $store->setName('magasin bv les clayes')
            ->setReference('12345')
            ->setEmail('bvlesclayes@beevup.com')
            ->setPhone('0112345678')
            ->setAddressNumber(25)
            ->setAdresseRue('Rue du Gros Caillou')
            ->setAddressPostCode('78340')
            ->setCity('Les Clayes sous Bois')
            ->setCountry('France')
            ;
        $manager->persist($store);

        $manager->flush();

        //company
        $company = new Company();
        $company->setStore($store)
            ->setName('entreprise des clayes')
            ->setSiret('123456789BVNS')
            ->setCategory($this->companyCategoryRepository->findOneBy(['name' => 'distribution']))
            ->setEmail('bvlesclayes@beevup.com')
            ->setPhone('0112345678')
            ->setAddressNumber(25)
            ->setAddresseStreet('Rue du Gros Caillou')
            ->setAddressPostCode('78340')
            ->setCity('Les Clayes sous Bois')
            ->setCountry('France')
            ->setBarCode('')
        ;
        $manager->persist($company);

        $manager->flush();

        //Topic Type
        $topicType = new TopicType();
        $topicType->setName('admin');

        $manager->persist($topicType);

        $manager->flush();


        //Topic admin
        $topic = new Topic();
        $topic->setName('general')
            ->setType($this->topicTypeRepository->findOneBy(['name' => 'admin']))
        ;

        $manager->persist($topic);

        $manager->flush();

        //user
        $user = new User();
        $user->setCompany($company)
            ->setStore($store)
            ->setType($this->userTypeRepository->findOneBy(['name' => 'admin plateform']))
            ->setEmail('admin.plateforme@beevup.com');
        $password = $this->passwordEncoder->encodePassword($user, 'password');
        $user->setPassword($password)
            ->setRoles(array('ROLE_ADMIN_PLATEFORM'))
            ->setIsValid(true)
            ->addTopic($topic)
            ;
        $manager->persist($user);

        $manager->flush();

        //profile
        $profile = new Profile();
        $profile->setUser($user);
        $profile->setGender(1);
        $profile->setFirstname('nicolas');
        $profile->setLastName('strugarek');
        $profile->setMobileNumber('0112345678');
        $profile->setPhoneNumber('0112345678');
            ;
        $manager->persist($profile);

        $manager->flush();

    }
}
