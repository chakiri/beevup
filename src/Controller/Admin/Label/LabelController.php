<?php

namespace App\Controller\Admin\Label;

use App\Entity\Label;
use App\Service\Chat\AutomaticMessage;
use App\Service\Mail\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LabelController extends EasyAdminController
{
    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
    /**
     * Get result to display in list
     */
    protected function createListQueryBuilder($entityClass, $sortDirection, $sortField = null, $dqlFilter = null)
    {
        //Get query builder from controller esay admin
        $result = parent::createListQueryBuilder($entityClass, $sortDirection, $sortField, $dqlFilter);

        //Custom result of query builder
        $result
            ->leftJoin('entity.company', 'c')
            ->leftJoin('c.store', 's')
            ->andWhere('s.id = :storeId')
            ->andWhere('entity.charter = true')
            ->andWhere('entity.kbisStatus is not null')
            ->andWhere('entity.isLabeled = false')
            ->orderBy('entity.createdAt', 'DESC')
            ->setParameter('storeId', $this->getUser()->getStore()->getId())
        ;

        return $result;
    }

    /**
     * Route to display kbis in modal
     * @Route("/modal/kbis/{label}", name="modal_kbis", options={"expose"=true})
     */
    public function modalKbis(Label $label)
    {
        return $this->render('admin/file/kbis_preview.html.twig', [
            'item' => $label
        ]);
    }

    /**
     * Action to validate label
     */
    public function validateLabelAction()
    {
        $id = $this->request->query->get('id');
        $entity = $this->em->getRepository(Label::class)->find($id);

        $entity->setIsLabeled(true);

        $this->em->persist($entity);

        $this->em->flush();

        //Send email to admin company
        $user = $entity->getCompany()->getCompanyAdministrator();
        $params = ['name' => $user->getProfile()->getFullName()];
        $this->mailer->sendEmailWithTemplate($user->getEmail(), $params, 'label_labeled');

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'Label',
            'action' => 'list'
        ]);
    }

    /**
     * Action to validate kbis
     *
     * @Route("/valid/kbis/{label}", name="valid_kbis")
     */
    public function validateKbis(Label $label, EntityManagerInterface $manager, Mailer $mailer)
    {
        $label->setKbisStatus('isValid');

        $manager->persist($label);

        $manager->flush();

        //Send email to admin company
        $user = $label->getCompany()->getCompanyAdministrator();
        $params = ['name' => $user->getProfile()->getFullName()];
        $mailer->sendEmailWithTemplate($user->getEmail(), $params, 'label_kbis_validated');

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'Label',
            'action' => 'list'
        ]);
    }

    /**
     * Action to rejetc kbis
     *
     * @Route("/reject/kbis/{label}", name="reject_kbis")
     */
    public function rejectKbis(Label $label, EntityManagerInterface $manager, AutomaticMessage $automaticMessage, Mailer $mailer)
    {
        $label->setKbisStatus(null);

        $manager->persist($label);

        $manager->flush();

        //Send email to user
        $user = $label->getCompany()->getCompanyAdministrator();
        $params = [
            'url' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'name' => $user->getProfile()->getFullName()
        ];
        $mailer->sendEmailWithTemplate($user->getEmail(), $params, 'label_kbis_rejected');

        //Send message to user
        $automaticMessage->fromAdvisorToUser($label->getCompany()->getCompanyAdministrator(), "Votre document Kbis n'a pas été accepté. Veuillez charger un document valide. ");

        return $this->redirectToRoute('easyadmin', [
            'entity' => 'Label',
            'action' => 'list'
        ]);
    }

}