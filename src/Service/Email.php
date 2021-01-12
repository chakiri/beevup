<?php

namespace App\Service;

use GuzzleHttp\Client;
use SendinBlue\Client\Api\ContactsApi;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\CreateContact;
use SendinBlue\Client\Model\SendEmail;
use SendinBlue\Client\Model\SendSmtpEmail;
use SendinBlue\Client\Model\UpdateAttribute;
use Twig\Environment;


class Email
{
    private $mailer;
    private $templating;

    public function __construct(\Swift_Mailer $mailer, Environment $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function send($token,$url, $user,$storePatron, $template, $subject='Mail de confirmation')
    {
        $message = (new \Swift_Message($subject))
            ->setFrom($_ENV['DEFAULT_EMAIL'])
            ->setTo($user->getEmail())
            ->setBody(
                $this->templating->render(
                    'emails/'.$template,
                    [    'url' => $url,
                        'user'=> $user,
                        'storePatron'=>$storePatron
                    ]
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }

    //Function for all sending email
    public function sendEmail($subject, $email, array $content, $template): void
    {
        $message = (new \Swift_Message())
            ->setSubject($subject)
            ->setFrom($_ENV['DEFAULT_EMAIL'])
            ->setTo($email)
            ->setBody(
                $this->templating->render(
                    'emails/'.$template, $content
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }

    //Get config From Api Key
    public function getConfig()
    {
        return Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['SENDINBLUE_API_KEY']);
    }

    //Function to send emails by Sendinblue SMTP
    public function sendEmailSmtp($subject, $email, array $content, $template): void
    {
        $config = $this->getConfig();

        $apiInstance = new TransactionalEmailsApi(new Client(), $config);

        $sendSmtpEmail = new SendSmtpEmail();

        $sendSmtpEmail['subject'] = $subject;
        $sendSmtpEmail['sender'] = ['name' => $_ENV['DEFAULT_EMAIL_NAME'], 'email' => $_ENV['DEFAULT_EMAIL']];
        $sendSmtpEmail['to'] = [['email' => $email]];
        $sendSmtpEmail['htmlContent'] = $this->templating->render('emails/'.$template, $content);

        try {
            $apiInstance->sendTransacEmail($sendSmtpEmail);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    //Function to send emails by Sendinblue Template
    public function sendEmailForTemplate(array $emails, $templateId)
    {
        $config = $this->getConfig();

        $apiInstance = new TransactionalEmailsApi(new Client(), $config);

        $sendEmail = new SendEmail();
        $sendEmail['emailTo'] = $emails;

        try {
            $apiInstance->sendTemplate($templateId, $sendEmail);
        } catch (\Exception $e) {
            return error_log($e->getMessage());
        }
    }

    //Check if contact exist on Api
    protected function checkIfExist($email)
    {
        $config = $this->getConfig();

        $apiInstance = new ContactsApi(new Client(), $config);

        $result = $apiInstance->getContactInfo($email);
        if ($result === null) return false;
        else return true;
    }

    //Check if contact is prospect
    protected function checkifProspect(array $contact)
    {

    }

    //Get list of all contacts on SendinBlue Api
    protected function getContactsApi()
    {
        $config = $this->getConfig();

        $apiInstance = new ContactsApi(new Client(), $config);

        try {
            return $apiInstance->getContacts();
        } catch (\Exception $e) {
            return error_log($e->getMessage());
        }
    }

    //Create a contact on Sendinblue Api
    protected function createContactApi($email)
    {
        $config = $this->getConfig();

        $apiInstance = new ContactsApi(new Client(), $config);

        $createContact = new CreateContact();
        $createContact['email'] = $email;

        try {
            return $apiInstance->createContact($createContact);
        } catch (\Exception $e) {
            return error_log($e->getMessage());
        }
    }

    //Edit contact from prospect to client
    protected function editContactApi($contact)
    {
        $config = $this->getConfig();

        $apiInstance = new ContactsApi(new Client(), $config);

        $updateAttribute = new UpdateAttribute();

        $apiInstance->updateAttribute();
    }
}