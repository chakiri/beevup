<?php

namespace App\Controller;

use App\Entity\ExpertBooking;
use App\Entity\ExpertMeeting;
use App\Form\ExpertBookingType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/expert/booking")
 */
class ExpertBookingController extends AbstractController
{
    /**
     * @Route("/{expertMeeting}/new", name="expert_booking_new", methods={"GET","POST"})
     * @Route("/{expertBooking}/edit", name="expert_booking_edit", methods={"GET","POST"})
     */
    public function form(Request $request, ?ExpertBooking $expertBooking, ?ExpertMeeting $expertMeeting): Response
    {
        //If creation get expertMeeting
        if (!$expertBooking){
            $expertBooking = new ExpertBooking();
        }else{
            $expertMeeting = $expertBooking->getExpertMeeting();
        }

        $form = $this->createForm(ExpertBookingType::class, $expertBooking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expertBooking->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($expertBooking);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('expert_booking/form.html.twig', [
            'expertBooking' => $expertBooking,
            'expertMeeting' => $expertMeeting,
            'form' => $form->createView(),
            'edit' => $expertBooking->getId() !== null,
            'dates' => $this->getUniqueDates($expertMeeting->getTimeSlots())
        ]);
    }

    private function getUniqueDates($timesSlot)
    {
        $dates = [];
        foreach($timesSlot as $timeSlot){
            if (!in_array($timeSlot->getDate()->format('m/d/Y'), $dates))
                $dates [] = $timeSlot->getDate()->format('m/d/Y');
        }

        return $dates;
    }
}
