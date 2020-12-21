<?php


namespace App\Controller;

use App\Entity\Sponsorship;
use App\Form\PostType;
use App\Repository\SponsorshipRepository;
use App\Repository\UserRepository;
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
     * @Route("/sponorship", name="Parrainez un membre de votre réseau​")
     */
    public function form(Request $request, Utility $utility, EntityManagerInterface $manager, SponsorshipRepository $sponsorshipRepository, \Swift_Mailer $mailer, ScoreHandler $scoreHandler, UserRepository $userRepository)
    {
        $sponsorship = new Sponsorship();
        $emailsExist = [];
        $emailsNotCorrect = [];
        $sponsor ='';
        $message ='';
        $newEmail = false;
        $points = 0;
        $points_msg = '';
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
                        $this->sendEmail($sponsor, $email, $utility->addLink($customMessage), $mailer);
                        $scoreHandler->add($this->getUser(), 50);
                        $points +=50;

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
             $emailsExistCount = count($emailsExist);
             $emailsNotCorrectCount = count($emailsNotCorrect);
            if($emailsExistCount > 0 || $emailsNotCorrectCount > 0 ){
                if($emailsExistCount > 0 ){
                    if(  $newEmail == true ) {
                        $message = ($emailsExistCount == 1) ? 'Vos contacts vont recevoir un e-mail d’invitation. Nous vous remercions pour votre action.<p><strong> l\'email suivant est déjà référencé sur notre plateforme: </strong></p>' : 'Vos contacts vont recevoir un e-mail d’invitation. Nous vous remercions pour votre action.<p><strong>  les emails suivants  sont déjà référencés sur notre plateforme:</strong></p> ';
                    } else {
                        $message = ($emailsExistCount == 1) ? 'L\'email suivant est déjà référencé sur notre plateforme: ' : 'Les emails suivants  sont déjà référencés sur notre plateforme: ';
                    }
                    $message = $message . '<ul>';
                    foreach ($emailsExist as $emailExist) {
                        $message = $message . '<li> ' . $emailExist . '</li>';
                }
                    $message = $message . '</ul>';
                }
                if($emailsNotCorrectCount > 0) {
                    $message = $message. '<br/> les email(s) suivant(s) sont non valides: ';
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

            }
        }

        return $this->render('sponsorship/form.html.twig', [
            'sponsorshipForm' => $form->createView(),
            'sponsorship' => $sponsorship

          ]);
    }

    public function sendEmail($sponsor, $email, $customMessage, $mailer){
        $message = (new \Swift_Message())
            ->setSubject($sponsor.' vous propose de le rejoindre sur Beevup.fr')
            ->setFrom($_ENV['DEFAULT_EMAIL'])
            ->setTo($email)
            ->setBody(
                $this->renderView(
                    'emails/spnsorship.html.twig',
                    ['message' => nl2br($customMessage)]
                ),
                'text/html'
            )
        ;
        $result = $mailer->send($message);
    }

}