<?php


namespace App\EventListener;


use App\Entity\Label;
use App\Service\Chat\AutomaticMessage;
use App\Service\Mail\Mailer;
use Doctrine\ORM\Event\OnFlushEventArgs;

class LabelListener
{
    private AutomaticMessage $automaticMessage;
    private Mailer $mailer;

    public function __construct(AutomaticMessage $automaticMessage, Mailer $mailer)
    {
        $this->automaticMessage = $automaticMessage;
        $this->mailer = $mailer;
    }

    /**
     * Detect editing store appointment to prevent user
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();
        $updatedEntities = $unitOfWork->getScheduledEntityUpdates();

        foreach ($updatedEntities as $entity) {
            if ($entity instanceof Label) {
                $changeset = $unitOfWork->getEntityChangeSet($entity);

                if (array_key_exists('storeAppointment', $changeset)) {
                    //Send email to user recap of appointment
                    $params = ['appointment' => $entity->getStoreAppointment()->format('d/m/Y à H:i')];
                    $this->mailer->sendEmailWithTemplate($entity->getCompany()->getEmailAdministrator(), $params, 'store_appointment_client');
                }
            }
        }

    }
}