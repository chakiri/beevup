<?php


namespace App\Controller;

use App\Entity\Sponsorship;
use App\Form\PostType;
use App\Repository\ScorePointRepository;
use App\Repository\SponsorshipRepository;
use App\Repository\UserRepository;
use App\Service\Email;
use App\Service\ScoreHandler;
use App\Service\Utility;
use App\Form\SponsorshipType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class SponsorshipController  extends AbstractController
{
    /**
     * @Route("/sponsorship", name="sponsorship")
     */
    public function form(Request $request, Utility $utility, EntityManagerInterface $manager, SponsorshipRepository $sponsorshipRepository,  ScoreHandler $scoreHandler, UserRepository $userRepository, ScorePointRepository $scorePointRepository, Email $emailService)
    {
        $sponsorship = new Sponsorship();
        $emailsExist = [];
        $emailsNotCorrect = [];
        $sponsor ='';
        $message ='';
        $optionsToast = [];
        $newEmail = false;
        $points = 0;
        $url = $this->generateUrl('security_registration');
        $pointsSender = $scorePointRepository->findOneBy(['id' => 6])->getPoint();
        if($this->getUser()->getProfile() !=null)
        {
            $sponsor = $this->getUser()->getProfile()->getFirstname() .' '. $this->getUser()->getProfile()->getLastname().' ';
        }
        $form = $this->createForm(SponsorshipType::class, $sponsorship);
        $form->handleRequest($request);
         if ($form->isSubmitted() && $form->isValid()) {
            $emails =  $utility->getEmailsList($form['emailsList']->getData());
            $customMessage = str_replace("\r\n", "<br>", $form['message']->getData());
            foreach ($emails as $email) {
                $email = trim($email);
                if($email != ''){
                    if ($sponsorshipRepository->findOneBy(['email' => $email]) == null && $userRepository->findOneBy(['email' => $email]) == null) {
                        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $newEmail = true;
                            $sponsorship->setEmail($email);
                            $sponsorship->setMessage($customMessage);
                            $sponsorship->setUser($this->getUser());
                            $manager->persist($sponsorship);
                            $emailService->sendEmail($sponsor.' vous propose de le rejoindre sur Beevup.fr', $email, ['message' => nl2br($customMessage)], 'spnsorship.html.twig');
                            $scoreHandler->add($this->getUser(), $pointsSender);
                            $points += $pointsSender;
                        } else {
                            array_push($emailsNotCorrect, $email);
                        }
                    } else {
                        array_push($emailsExist, $email);
                    }
                }
            }

            $manager->flush();

            $points_msg = '<p><strong> Vous venez d\'obtenir '.$points. ' points </strong></p>';

            if($newEmail == true ) {
             $message ='Vos contacts vont recevoir un e-mail d’invitation. Nous vous remercions pour votre action.';
            }
            $emailsExistCount = count($emailsExist);
            $emailsNotCorrectCount = count($emailsNotCorrect);

            if($emailsExistCount > 0 || $emailsNotCorrectCount > 0 ){
                if($emailsExistCount > 0 ){
                    if(  $newEmail == true ) {
                        $message = ($emailsExistCount == 1) ? $message.'<p><strong> l\'email suivant est déjà référencé sur notre plateforme: </strong></p>' : $message.'Vos contacts vont recevoir un e-mail d’invitation. Nous vous remercions pour votre action.<p><strong>  les emails suivants  sont déjà référencés sur notre plateforme:</strong></p> ';
                    } else {
                        $message = ($emailsExistCount == 1) ? $message.'L\'email suivant est déjà référencé sur notre plateforme: ' : $message.'Les emails suivants  sont déjà référencés sur notre plateforme: ';
                    }
                    $message = $message . '<ul>';
                    foreach ($emailsExist as $emailExist) {
                        $message = $message . '<li> ' . $emailExist . '</li>';
                    }
                    $message = $message . '</ul>';
                }
                if($emailsNotCorrectCount > 0) {
                    $message = $message . '<br/> les email(s) suivant(s) sont non valides: ';
                    $message = $message . '<ul>';
                    foreach ($emailsNotCorrect as $emailNotCorrect) {
                        $message = $message . '<li> ' . $emailNotCorrect . '</li>';
                    }
                    $message = $message . '</ul>';
                }
                if($points > 0){
                    $message = $message.' '.$points_msg ;
                }
               $this->addFlash('danger', nl2br($message));
            } else {
                $this->addFlash('success', 'Vos contacts vont recevoir un e-mail d’invitation. Nous vous remercions pour votre action.'.$points_msg);
                $optionsToast = ['toastScore' => $points];

                return $this->redirectToRoute('dashboard', $optionsToast);
            }
        }

        return $this->render('sponsorship/form.html.twig', [
            'sponsorshipForm' => $form->createView(),
            'sponsorship' => $sponsorship,

        ]);
    }


}