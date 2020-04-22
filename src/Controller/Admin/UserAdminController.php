<?php
namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Repository\UserTypeRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use App\Entity\User;
use App\Entity\Profile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\BarCode;
class UserAdminController extends EasyAdminController
{
    /**
     * @var UserPasswordEncoderInterface
     */

    private $passwordEncoder;
    private $userTypeRepo;
    private $barCode;
    private $userRepo;


   
    

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserTypeRepository $userTypeRepo, UserRepository $userRepo,  BarCode $barCode)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userTypeRepo = $userTypeRepo;
        $this->barCode = $barCode;
        $this->userRepo = $userRepo;
    }
    
    public function persistUserEntity($user)
    {
        $currentUser = $this->getUser();
        $this->updatePassword($user);
        $userRoles = $user->getRoles();


        if(in_array('ROLE_ADMIN_STORE', $userRoles))
        {
            $type = $this->userTypeRepo->findOneBy(['id'=> 4], []);
            $user->setType($type);
        }
        if(in_array('ROLE_ADMIN_COMPANY', $userRoles)){
            $type = $this->userTypeRepo->findOneBy(['id'=> 3], []);
            $user->setType($type);

        }
        if(in_array('ROLE_ADMIN_PLATEFORM', $userRoles)){
            $type = $this->userTypeRepo->findOneBy(['id'=> 5], []);
            $user->setType($type);

        }

        array_push($userRoles, 'ROLE_USER');
        /*** generate bar code*/
        $userId =  $this->userRepo->findOneBy([],['id' => 'desc'])->getId() + 1;
        $code = $this->barCode->generate( $userId);
        $user->setBarCode($code);
        /**end ******/

        $user->setIsValid(1);
        $user->setIsDeleted(0);
        $user->setRoles($userRoles);
        $this->updateRoles($user);
        parent::persistEntity($user);
        $profile = new Profile();
        $profile->setUser($user);
        parent::persistEntity($profile);
   }

    public function updateUserEntity($user)
    {
        $currentUser = $this->getUser();
        $userRoles = $user->getRoles();
        if(in_array('ROLE_ADMIN_STORE', $userRoles))
        {
            $type = $this->userTypeRepo->findOneBy(['id'=> 4], []);
            $user->setType($type);
        }
         if(in_array('ROLE_ADMIN_COMPANY', $userRoles)) {
            $type = $this->userTypeRepo->findOneBy(['id' => 3], []);
            $user->setType($type);
         }

         if(in_array('ROLE_ADMIN_PLATEFORM', $userRoles)){
                $type = $this->userTypeRepo->findOneBy(['id'=> 5], []);
                $user->setType($type);
         }


        array_push($userRoles, 'ROLE_USER');
        $user->setRoles($userRoles);
        $code = $this->barCode->generate($user->getId());
        $user->setBarCode($code);
        $this->updatePassword($user);
        parent::updateEntity($user);
        
    }

    public function updatePassword(User $user)
    {   
        if (!empty($user->getPassword()) && strlen($user->getPassword())< 50) {
             $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
        }
    }
    public function updateRoles(User $user)
    {   
        if (!empty($user->getRoles())) {
             $user->setRoles($user->getRoles());
        }
    }
}