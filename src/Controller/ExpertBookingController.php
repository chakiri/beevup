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

        //Get array dates and array startTimes corresponding to dates
        $dates = $this->getUniqueDates($expertMeeting->getTimeSlots());
        $startTimes = $this->getTimesById($expertMeeting->getTimeSlots(), $dates);

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
     * Return unique date format in array
     * @param $timesSlot
     * @return array
     */
    private function getUniqueDates($timesSlot): array
    {
        $dates = [];
        foreach($timesSlot as $timeSlot){
            if (!in_array($timeSlot->getDate()->format('d/m/Y'), $dates))
                $dates [$timeSlot->getId()] = $timeSlot->getDate()->format('d/m/Y');
        }

        return $dates;
    }

    /**
     * Return 2 dimensions array corresponding to times of each date
     */
    private function getTimesById($timesSlot, $dates): array
    {
        $startsTimes = [];
        foreach($dates as $date){
            //Create array containing date key and value times
            $startsTimes [$date] = [];
            foreach($timesSlot as $timeSlot){
                if ($timeSlot->getDate()->format('d/m/Y') === $date){
                    array_push($startsTimes [$date], $timeSlot->getStartTime()->format('H:i'));
                }
            }
        }

        return $startsTimes;
    }
}
