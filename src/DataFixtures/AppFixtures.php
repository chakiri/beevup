<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\CompanyCategory;
use App\Entity\Offer;
use App\Entity\PostCategory;
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
use Doctrine\Persistence\ObjectManager;
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
        $categories = ['Commerçant', 'Artisan', 'Freelance', 'Profession Libérale', 'Administration', 'TPE', 'Autre'];
        foreach ($categories as $category){
            $companyCategory = new CompanyCategory();
            $companyCategory->setName($category);
            $manager->persist($companyCategory);
        }

        //service category
        $categories = ['Aide à la personne', 'Animation', 'Assistance', 'Audit', 'Certification', 'Coaching', 'Commerce de proximité', 'Comptabilité', 'Conseil', 'Construction', 'Consultation', 'Cours', 'Dépannage', 'Edition', 'Entretien', 'Fabrication', 'Immobilier', 'Livraison', 'Maintenance', 'Prestation', 'Réparation', 'Restauration', 'Serrurerie', 'Services aux entreprises', 'Soin', 'Transport de personnes', 'Travaux Gros Oeuvre', 'Travaux Moyen', 'Impression'];
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
        $functions = ['Gérant', 'Directeur de Magasin', 'Responsable de Magasin', 'Conseiller Services', 'Conseiller Papeterie', 'Conseiller Bureautique/Informatique', 'Conseiller Services généraux', 'Conseiller Mobilier', 'Conseiller tous domaines'];
        foreach ($functions as $function){
            $userFunction = new UserFunction();
            $userFunction->setName($function);
            $userFunction->setRelatedTo('Store');
            $manager->persist($userFunction);
        }
        $functions = ['Direction d’entreprise', 'Accueil', 'Achats', 'Administratif', 'Autre', 'Clients', 'Finance', 'Informatique', 'Innovation', 'Juridique', 'Logistique', 'Maîtrise d\'œuvre', 'Maitrise d’Ouvrage', 'Marketing', 'Production', 'R&D', 'Secrétariat', 'Support', 'Technique'];
        foreach ($functions as $function){
            $userFunction = new UserFunction();
            $userFunction->setName($function);
            $userFunction->setRelatedTo('Company');
            $manager->persist($userFunction);
        }

        //Topic Type
        $types = ['admin', 'company', 'categoryCompany', 'function', 'admin_store'];
        foreach ($types as $type){
            $topicType = new TopicType();
            $topicType->setName($type);

            $manager->persist($topicType);
        }
        $manager->flush();


        //Topic admin
        $topic = new Topic();
        $topic->setName('general')
            ->setType($this->topicTypeRepository->findOneBy(['name' => 'admin']))
        ;
        $manager->persist($topic);
        $manager->flush();

        //PostCategory
        $categories = ['Informations', 'Opportunité commerciale', 'Question à la communauté', 'Emploi', 'Evénement', 'Autre', 'Derniers arrivés'];
        foreach ($categories as $category){
            $postCategory = new PostCategory();
            $postCategory->setName($category);
            $manager->persist($postCategory);
        }

        //Offer
        $offer1 = new Offer();
        $offer1->setName('premium one')
            ->setKm(200)
            ->setNbServices(3)
            ->setNbUsers(3)
            ->setPrice(10.00)
        ;

        $manager->persist($offer1);

        $offer2 = new Offer();
        $offer2->setName('premium one plus')
            ->setKm(500)
            ->setNbServices(5)
            ->setNbUsers(5)
            ->setPrice(30.00)
        ;

        $manager->persist($offer2);

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
            ->setSiret('12312312312312')
            ->setCategory($this->companyCategoryRepository->findOneBy(['name' => 'distribution']))
            ->setEmail('bvlesclayes@beevup.com')
            ->setPhone('0112345678')
            ->setAddressNumber(25)
            ->setAddressStreet('Rue du Gros Caillou')
            ->setAddressPostCode('78340')
            ->setCity('Les Clayes sous Bois')
            ->setCountry('France')
            ->setBarCode('')
        ;
        $manager->persist($company);

        $manager->flush();

        //user
        $user = new User();
        $user->setCompany($company)
            ->setStore($store)
            ->setType($this->userTypeRepository->findOneBy(['name' => 'admin plateform']))
            ->setEmail('admin.bv@bureau-vallee.com');
        $password = $this->passwordEncoder->encodePassword($user, 'p62MQk@;');
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
        $profile->setFirstname('super');
        $profile->setLastName('admin');
        $profile->setMobileNumber('0112345678');
        $profile->setPhoneNumber('0112345678');
            ;
        $manager->persist($profile);

        $manager->flush();

    }
}
