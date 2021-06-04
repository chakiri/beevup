<?php

namespace App\Controller;

use App\Entity\ExpertMeeting;
use App\Form\ExpertMeetingType;
use App\Repository\ExpertBookingRepository;
use App\Repository\ExpertMeetingRepository;
use App\Service\GetCompanies;
use App\Service\TimeSlot\SlotInstantiator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/app/expert/meeting")
 */
class ExpertMeetingController extends AbstractController
{
    /**
     * @Route("/", name="expert_meeting_index", methods={"GET"})
     */
    public function index(ExpertMeetingRepository $expertMeetingRepository, GetCompanies $getCompanies, ExpertBookingRepository $expertBookingRepository): Response
    {
        $allCompanies = $getCompanies->getAllCompanies( $this->getUser()->getStore());

        //Find expert meeting proposed by current user
        $expertMeeting = $expertMeetingRepository->findOneBy(['user' => $this->getUser()]);

        $expertsMeetings = $expertMeetingRepository->findLocal($allCompanies);

        //Add expertMeeting of current user on the beginning of array
        array_unshift($expertsMeetings, $expertMeeting);

        //Get expert meetings booked by current user
        $expertsBooking = $expertBookingRepository->findBy(['user' => $this->getUser()]);
        $expertsMeetingsBookedByUser = [];
        foreach($expertsBooking as $expertBooking){
            $expertsMeetingsBookedByUser [] = $expertBooking->getExpertMeeting();
        }

        return $this->render('expert_meeting/index.html.twig', [
            'expertsMeetings' => $expertsMeetings,
            'expertMeeting' => $expertMeeting,
            'profile' => $this->getUser()->getProfile(),
            'expertsMeetingsBookedByUser' => $expertsMeetingsBookedByUser,
        ]);
    }

    /**
     * @Route("/new", name="expert_meeting_new", methods={"GET","POST"})
     * @Route("/{id}/edit", name="expert_meeting_edit", methods={"GET","POST"})
     */
    public function form(Request $request, ?ExpertMeeting $expertMeeting, SlotInstantiator $slotInstantiator): Response
    {
        if (!$expertMeeting){
            $expertMeeting = new ExpertMeeting();
        }

        $form = $this->createForm(ExpertMeetingType::class, $expertMeeting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expertMeeting->setUser($this->getUser());

            //Instantiate slots
            $slotInstantiator->instantiate($expertMeeting->getBreakTime(), $expertMeeting->getTimeSlots());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($expertMeeting);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('expert_meeting/form.html.twig', [
            'expert_meeting' => $expertMeeting,
            'form' => $form->createView(),
            'edit' => $expertMeeting->getId() !== null
        ]);
    }

    /**
     * @Route("/{id}", name="expert_meeting_show", methods={"GET"})
     */
    public function show(ExpertMeeting $expertMeeting): Response
    {
        return $this->render('expert_meeting/show.html.twig', [
            'expert_meeting' => $expertMeeting,
        ]);
    }

    /**
     * @Route("/{id}", name="expert_meeting_delete", methods={"DELETE"})
     */
    public function delete(Request $request, ExpertMeeting $expertMeeting): Response
    {
        if ($this->isCsrfTokenValid('delete'.$expertMeeting->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($expertMeeting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('expert_meeting_index');
    }
}
