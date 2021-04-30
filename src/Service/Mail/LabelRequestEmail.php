<?php


namespace App\Service\Mail;


use App\Entity\User;

class LabelRequestEmail
{

    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send labels request email to admin store
     */
    public function send(User $user, $labels): void
    {
        $list = $this->getListText($labels);

        $params = ['name' => $user->getProfile()->getFirstname(), 'list' => $list];
        $this->mailer->sendEmailWithTemplate($user->getEmail(), $params, 'store_labels_requests');
    }

    /**
     * Get Text list to set in core of the mail
     */
    private function getListText($labels)
    {
        $kbisWaiting = [];
        $appointmentWaiting = [];
        $labeledWaiting = [];

        $now = new \DateTime();

        foreach($labels as $label){
            if ($label->getKbisStatus() === 'isWaiting'){
                $kbisWaiting[] = $label;
            }elseif ($label->getKbisStatus() === 'isValid' && is_null($label->getStoreAppointment())){
                $appointmentWaiting[] = $label;
            }elseif ($label->getKbisStatus() === 'isValid' && $label->getStoreAppointment() && $label->isLabeled() == false && $label->getStoreAppointment() < $now){
                $labeledWaiting[] = $label;
            }
        }

        if ($kbisWaiting){
            $listKbis = "Vérifier le kbis : \n";
            foreach ($kbisWaiting as $label){
                $listKbis .= $label->getCompany()->getName() . "\n";
            }
            $listKbis .= "\n";
        }

        if ($appointmentWaiting){
            $listAppointment = "Prendre un RDV : \n";
            foreach ($appointmentWaiting as $label){
                $listAppointment .= $label->getCompany()->getName() . "\n";
            }
            $listAppointment .= "\n";
        }

        if ($labeledWaiting){
            $listLabeled= "Remettre le Label Beev\'Up : \n";
            foreach ($labeledWaiting as $label){
                $listLabeled .= $label->getCompany()->getName() . "\n";
            }
            $listLabeled .= "\n";
        }

         return nl2br(($listKbis ?? null) . ($listAppointment ?? null) . ($listLabeled ?? null) );
    }
}