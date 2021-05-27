<?php

namespace App\Controller;

use App\Entity\ExpertBooking;
use App\Entity\ExpertMeeting;
use App\Entity\Slot;
use App\Entity\TimeSlot;
use App\Entity\User;
use App\Form\ExpertBookingType;
use App\Repository\ExpertBookingRepository;
use App\Service\Chat\AutomaticMessage;
use App\Service\TimeSlot\handleDatetime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app/expert/booking")
 */
class ExpertBookingController extends AbstractController
{
    /**
     * @Route("/{expertMeeting}/new", name="expert_booking_new", methods={"GET","POST"})
     */
    public function new(Request $request, ExpertMeeting $expertMeeting, handleDatetime $handleDatetime): Response
    {
        $expertBooking = new ExpertBooking();
        $expertBooking->setExpertMeeting($expertMeeting);

        return $this->form($request, $expertMeeting, $expertBooking, $handleDatetime);
    }

    /**
     * @Route("/{expertBooking}/edit", name="expert_booking_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, ExpertBooking $expertBooking, handleDatetime $handleDatetime): Response
    {
        //Denied access
        if($expertBooking->getUser() !== $this->getUser()) return $this->render('bundles/TwigBundle/Exception/error403.html.twig');

        $expertMeeting = $expertBooking->getExpertMeeting();

        return $this->form($request, $expertMeeting, $expertBooking, $handleDatetime);
    }

    private function form (Request $request, ExpertMeeting $expertMeeting, ExpertBooking $expertBooking, $handleDatetime)
    {
        $form = $this->createForm(ExpertBookingType::class, $expertBooking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expertBooking->setUser($this->getUser());
            $expertBooking->setStatus('waiting');

            $slot = $expertBooking->getslot();
            $slot->setStatus(true);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($expertBooking);
            $entityManager->persist($slot);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard');
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
     * @Route("/confirm/submit/{expertUser}/{slot}", name="expert_booking_confirm_submit", options={"expose"=true})
     */
    public function confirmSubmitModal(User $expertUser, Slot $slot, AutomaticMessage $automaticMessage): response
    {
        //Send message to user
        $automaticMessage->fromAdvisorToUser($expertUser, 'Bonne nouvelle !<br> Une demande de RDV expert est en attente de confirmation. <a href="' . $this->generateUrl('expert_booking_list', ['status' => 'toConfirm']) . '">Cliquez ici</a>');

        return $this->render('expert_booking/modal/confirmSubmit.html.twig', [
            'timeSlotDate' => $slot->getTimeSlot()->getDate()->format('d/m/Y'),
            'timeSlotTimeStart' => $slot->getStartTime()->format('H:i')
        ]);
    }

    /**
     * @Route("/list/{status}", name="expert_booking_list")
     */
    public function list($status, ExpertBookingRepository $expertBookingRepository): Response
    {
        //Get the first one because user is allowed to have only one
        $expertMeeting = $this->getUser()->getExpertMeetings()[0];

        if ($status == 'toConfirm')
            $list = $expertBookingRepository->findByStatus($expertMeeting, 'waiting');
        elseif ($status == 'toCome')
            $list = $expertBookingRepository->findByStatus($expertMeeting, 'confirmed');
        elseif ($status == 'passed')
            $list = $expertBookingRepository->findByStatus($expertMeeting, 'confirmed');

        return $this->render('dashboard/expertBooking/list.html.twig', [
            'profile' => $this->getUser()->getProfile(),
            'list' => $list,
            'status' => $status
        ]);
    }

    /**
     * @Route("/confirm/{id}", name="expert_booking_confirm", options={"expose"=true})
     */
    public function confirm(ExpertBooking $expertBooking, EntityManagerInterface  $manager, AutomaticMessage $automaticMessage): Response
    {
        if ($expertBooking->getStatus() === 'waiting'){
            $expertBooking->setStatus('confirmed');

            $manager->persist($expertBooking);
            $manager->flush();
        }

        //Send message to user
        $automaticMessage->fromAdvisorToUser($expertBooking->getUser(), 'Bonne nouvelle !<br> Votre rendez-vous vient d\'être confirmé.');


        return new JsonResponse(['message' => 'is confirmed'],200);
    }

    /**
     * @Route("/cancel/{id}", name="expert_booking_cancel")
     */
    public function cancel(ExpertBooking $expertBooking, EntityManagerInterface  $manager): Response
    {
        $expertBooking->setStatus('canceled');

        $manager->persist($expertBooking);
        $manager->flush();

        return new JsonResponse(['message' => 'is canceled'],200);
    }
}
