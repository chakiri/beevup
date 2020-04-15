<?php
namespace App\Controller\Admin;

use App\Repository\UserTypeRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use App\Entity\User;
use App\Entity\Profile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAdminController extends EasyAdminController
{
    /**
     * @var UserPasswordEncoderInterface
     */

    private $passwordEncoder;
    private $userTypeRepo;

   
    

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserTypeRepository $userTypeRepo)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userTypeRepo = $userTypeRepo;
    }
    
    public function persistUserEntity($user)
    {
        $currentUser = $this->getUser();
        $this->updatePassword($user);
        $userRoles = $user->getRoles();


        if(in_array('ROLE_PATRON', $userRoles))
        {
            $type = $this->userTypeRepo->findOneBy(['id'=> 4], []);
            $user->setType($type);
        }
        else if(in_array('ROLE_ADMIN_COMPANY', $userRoles)){
            $type = $this->userTypeRepo->findOneBy(['id'=> 3], []);
            $user->setType($type);

        } else {
            $type = $this->userTypeRepo->findOneBy(['id'=> 5], []);
            $user->setType($type);

        }

        array_push($userRoles, 'ROLE_USER');
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
        if(in_array('ROLE_PATRON', $userRoles))
        {
            $type = $this->userTypeRepo->findOneBy(['id'=> 4], []);
            $user->setType($type);
        }
        else if(in_array('ROLE_ADMIN_COMPANY', $userRoles)){
            $type = $this->userTypeRepo->findOneBy(['id'=> 3], []);
            $user->setType($type);

        } else {
            $type = $this->userTypeRepo->findOneBy(['id'=> 5], []);
            $user->setType($type);

        }
        array_push($userRoles, 'ROLE_USER');
        $user->setRoles($userRoles);
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