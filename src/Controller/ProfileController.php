<?php

namespace App\Controller;

use App\Entity\Topic;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Profile;
use App\Form\ProfileType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProfileController extends AbstractController
{
    /**
     * @Route("/myaccount/{id}", name="profile_show")
     */
    public function show(Profile $profile)
    {
        return $this->render('profile/show.html.twig', [
            'profile' => $profile,
        ]);
    }
    /**
     * @Route("/myaccount/{id}/edit", name="profile_edit")
     */
    public function edit(Profile $profile, EntityManagerInterface $manager, Request $request, TopicRepository $topicRepository)
    {
        $form = $this->createForm(ProfileType::class, $profile);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['imageFile']->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                //$safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $safeFilename =  $originalFilename;
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                try {
                    $file->move(
                        $this->getParameter('profil_photo'),
                        $newFilename
                    );
                    
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                   
                }
                $profile->setPhoto($newFilename);
            }
            $profile->setIsCompleted(true);

            $manager->persist($profile);
            $manager->flush();

            //Add function topic to the list of topics
            $this->initTopicFunction($topicRepository, $profile, $manager);

            return $this->redirectToRoute('profile_show', [
               'id' => $profile->getId()
            ]);
        }

        return $this->render('profile/edit.html.twig', [
            'EditProfileForm' => $form->createView(),
        ]);
    }

    private function initTopicFunction(TopicRepository $topicRepository, Profile $profile, EntityManagerInterface $manager)
    {
        //Verif if topic function exist already
        $topic = $topicRepository->findOneBy(['name' => $profile->getSlugFunction(), 'type' => 'function']);
        if (!$topic){
            $topic = new Topic();
            $topic
                ->setName($profile->getSlugFunction())
                ->setType('function')
            ;
            $manager->persist($topic);
        }

        //Add this topic to user and delete the other
        $user = $profile->getUser();

        //Delete the other function topic
        $userTopics = $user->getTopics();

        foreach ($userTopics as $userTopic){
            if ($userTopic->getType() == 'function'){
                $user->removeTopic($userTopic);
            }
        }

        $user->addTopic($topic);

        $manager->persist($user);

        $manager->flush();
    }
}
