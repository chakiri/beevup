<?php

namespace App\Controller;

use App\Entity\ExpertBooking;
use App\Entity\ExpertMeeting;
use App\Entity\Slot;
use App\Form\ExpertBookingType;
use App\Repository\ExpertBookingRepository;
use App\Service\Chat\AutomaticMessage;
use App\Service\ExpertMeeting\HandleMeeting;
use App\Service\ExpertMeeting\videoConference;
use App\Service\Mail\Mailer;
use App\Service\TimeSlot\HandleDatetime;
use App\Service\TimeSlot\SlotInstantiator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/app/expert/booking")
 */
class ExpertBookingController extends AbstractController
{
    /**
     * @Route("/{expertMeeting}/new", name="expert_booking_new", methods={"GET","POST"})
     */
    public function new(Request $request, ExpertMeeting $expertMeeting, ExpertBookingRepository $expertBookingRepository, HandleDatetime $handleDatetime, Mailer $mailer, AutomaticMessage $automaticMessage, HandleMeeting $handleMeeting, SlotInstantiator $slotInstantiator): Response
    {
        //If Rebooking expertBooking already exist
        $expertBooking = $expertBookingRepository->findOneBy(['user' => $this->getUser(), 'expertMeeting' => $expertMeeting, 'status' => 'canceled']);

        if (!$expertBooking){
            $expertBooking = new ExpertBooking();
            $expertBooking->setExpertMeeting($expertMeeting);
            $expertBooking->setUser($this->getUser());
        }

        $form = $this->createForm(ExpertBookingType::class, $expertBooking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expertBooking->setStatus('waiting');

            $slot = $expertBooking->getslot();
            $slot->setStatus(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($expertBooking);
            $entityManager->persist($slot);
            $entityManager->flush();

            //Get info expert booking
            $booking = $handleMeeting->getInfoBooking($expertBooking);

            //Send email to expert user
            $params = [
                'name' => $expertBooking->getExpertMeeting()->getUser()->getProfile()->getFullName(),
                'booking' => $booking,
                'url' => $this->generateUrl('expert_booking_list', ['status' => 'toConfirm'], UrlGeneratorInterface::ABSOLUTE_URL)
            ];
            $mailer->sendEmailWithTemplate($expertBooking->getExpertMeeting()->getUser()->getEmail(), $params, 'expert_booking_request');

            //Send message to user
            $automaticMessage->fromAdvisorToUser($expertBooking->getExpertMeeting()->getUser(), 'Bonne nouvelle !<br> Une demande de RDV expert est en attente de confirmation. <a href="' . $this->generateUrl('expert_booking_list', ['status' => 'toConfirm']) . '">Cliquez ici</a>');

            return $this->redirectToRoute('dashboard');
        }

        //Remove passed slots
        foreach ($expertMeeting->getTimeSlots() as $timeSlot){
            $slotInstantiator->clearPassedSlots($timeSlot);
        }

        //Get array dates and array startTimes corresponding to dates
        $dates = $handleDatetime->getUniqueDates($expertMeeting->getTimeSlots());
        $startTimes = $handleDatetime->getTimesById($expertMeeting->getTimeSlots(), $dates, $expertBooking->getSlot());

        return $this->render('expert_booking/form.html.twig', [
            'expertBooking' => $expertBooking,
            'expertMeeting' => $expertMeeting,
            'form' => $form->createView(),
            'edit' => $expertBooking->getId() !== null,
            'dates' => $dates,
            'startTimes' => $startTimes
        ]);
    }

    /**
     * @Route("/{expertBooking}/edit", name="expert_booking_edit", methods={"GET","POST"})
     */
    /*public function edit(Request $request, ExpertBooking $expertBooking, HandleDatetime $handleDatetime, Mailer $mailer, AutomaticMessage $automaticMessage, HandleMeeting $handleMeeting): Response
    {
        //Denied access
        if($expertBooking->getUser() !== $this->getUser()) return $this->render('bundles/TwigBundle/Exception/error403.html.twig');

        $expertMeeting = $expertBooking->getExpertMeeting();

        return $this->form($request, $expertMeeting, $expertBooking, $handleDatetime, $mailer, $automaticMessage, $handleMeeting);
    }*/

    /**
     * @Route("/confirm/submit/{slot}", name="expert_booking_confirm_submit", options={"expose"=true})
     */
    public function confirmSubmitModal(Slot $slot): response
    {
        return $this->render('expert_booking/modal/confirmSubmit.html.twig', [
            'timeSlotDate' => $slot->getTimeSlot()->getDate()->format('d/m/Y'),
            'timeSlotTimeStart' => $slot->getStartTime()->format('H:i')
        ]);
    }

    /**
     * @Route("/list/{status}", name="expert_booking_list")
     */
    public function list($status, ExpertBookingRepository $expertBookingRepository, HandleMeeting $handleMeeting): Response
    {
        //Get the first one because user is allowed to have only one
        $expertMeeting = $this->getUser()->getExpertMeetings()[0];

        //Archived all passed booking of expert meeting
        $handleMeeting->archive($expertBookingRepository->findByMeeting($expertMeeting));

        if ($status == 'toConfirm')
            $list = $expertBookingRepository->findByStatus($expertMeeting, 'waiting');
        elseif ($status == 'confirmed')
            $list = $expertBookingRepository->findByStatus($expertMeeting, 'confirmed');
        elseif ($status == 'canceled')
            $list = $expertBookingRepository->findByStatus($expertMeeting, 'canceled');
        elseif ($status == 'archived')
            $list = $expertBookingRepository->findByStatus($expertMeeting, 'archived');

        return $this->render('dashboard/expertBooking/list.html.twig', [
            'profile' => $this->getUser()->getProfile(),
            'list' => $list,
            'status' => $status
        ]);
    }

    /**
     * @Route("/confirm/{id}", name="expert_booking_confirm", options={"expose"=true})
     */
    public function confirm(ExpertBooking $expertBooking, EntityManagerInterface  $manager, AutomaticMessage $automaticMessage, videoConference $videoConference, Mailer $mailer, HandleMeeting $handleMeeting): Response
    {
        if ($expertBooking->getStatus() === 'waiting'){

            if ($expertBooking->getWay() === 'visio') {
                $link = $videoConference->generateLink();
                $expertBooking->setVideoLink($link);
            }

            $expertBooking->setStatus('confirmed');

            $manager->persist($expertBooking);
            $manager->flush();
        }

        //Send email to expert user
        $params = [
            'booking' => $handleMeeting->getInfoBooking($expertBooking),
            'meeting' => $handleMeeting->getInfoMeeting($expertBooking),
            'action' => $expertBooking->getWay() === 'visio' ? '<a href="' . $expertBooking->getVideoLink() . '">Lancer la visio</a>' : 'A l\'adresse : ' . $expertBooking->getExpertMeeting()->getAddress(),
            'url' => $this->generateUrl('expert_booking_list', ['status' => 'confirmed'], UrlGeneratorInterface::ABSOLUTE_URL)
        ];
        //Send confirmation to booking user
        $mailer->sendEmailWithTemplate($expertBooking->getUser()->getEmail(), $params, 'expert_booking_confirm_user');
        //Send confirmation to meeting user
        $mailer->sendEmailWithTemplate($expertBooking->getExpertMeeting()->getUser()->getEmail(), $params, 'expert_booking_confirm_expert');

        //Send message to user
        $automaticMessage->fromAdvisorToUser($expertBooking->getUser(), 'Bonne nouvelle !<br> Votre rendez-vous vient d\'être confirmé.');

        return new JsonResponse(['message' => 'is confirmed'],200);
    }

    /**
     * @Route("/cancel/{id}", name="expert_booking_cancel")
     */
    public function cancel(ExpertBooking $expertBooking, EntityManagerInterface  $manager, Mailer $mailer): Response
    {
        $expertBooking->setStatus('canceled');

        //Free slot
        $slot = $expertBooking->getSlot();
        $slot->setStatus(false);

        $manager->persist($expertBooking);
        $manager->persist($slot);
        $manager->flush();

        //Send email to expert user
        $params = [
            'booking' => [
                'name' => $expertBooking->getUser()->getProfile()->getFullName(),
                'date' => $expertBooking->getSlot()->getTimeSlot()->getDate()->format('d/m/Y'),
                'time' => $expertBooking->getSlot()->getStartTime()->format('H:i'),
            ],
            'meeting' => [
                'name' => $expertBooking->getExpertMeeting()->getUser()->getProfile()->getFullName(),
                'company' => $expertBooking->getExpertMeeting()->getUser()->getCompany()->getName(),
            ],
            'url' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL)
        ];
        //Send email to booking user
        $mailer->sendEmailWithTemplate($expertBooking->getUser()->getEmail(), $params, 'expert_booking_cancel_user');

        return new JsonResponse(['message' => 'is canceled'],200);
    }

    /**
     * @Route("/archive/{id}", name="expert_booking_archive")
     */
    public function archive(ExpertBooking $expertBooking, EntityManagerInterface  $manager): Response
    {
        $expertBooking->setStatus('archived');

        $manager->persist($expertBooking);
        $manager->flush();

        return new JsonResponse(['message' => 'is archived'],200);
    }
}
