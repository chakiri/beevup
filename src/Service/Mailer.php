<?php

namespace App\Service;

use GuzzleHttp\Client;
use SendinBlue\Client\Api\ContactsApi;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\CreateContact;
use SendinBlue\Client\Model\SendSmtpEmail;
use SendinBlue\Client\Model\UpdateContact;
use Twig\Environment;


class Mailer
{
    private $templating;

    public function __construct(Environment $templating)
    {
        $this->templating = $templating;
    }

    //Get config From Api Key
    public function getConfig()
    {
        return Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['SENDINBLUE_API_KEY']);
    }

    //Function to send emails by Sendinblue SMTP with Twig templates
    public function sendEmail($subject, $email, array $content, $template): void
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

    //Function to send emails wth Sendinblue templates
    public function sendEmailWithTemplate($email, array $params, int $templateId): void
    {
        $config = $this->getConfig();

        $apiInstance = new TransactionalEmailsApi(new Client(), $config);

        $sendSmtpEmail = new SendSmtpEmail();

        $sendSmtpEmail['sender'] = ['name' => $_ENV['DEFAULT_EMAIL_NAME'], 'email' => $_ENV['DEFAULT_EMAIL']];
        $sendSmtpEmail['to'] = [['email' => $email]];
        $sendSmtpEmail['templateId'] = $templateId;
        $sendSmtpEmail['params'] = $params;

        try {
            $apiInstance->sendTransacEmail($sendSmtpEmail);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            dd($e);
        }
    }

    //Get list of all contacts on SendinBlue Api
    public function getContacts()
    {
        $config = $this->getConfig();

        $apiInstance = new ContactsApi(new Client(), $config);

        try {
            return $apiInstance->getContacts();
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    //Create a contact on Sendinblue Api
    public function createContact($email, $listId): void
    {
        $config = $this->getConfig();

        $apiInstance = new ContactsApi(new Client(), $config);

        $createContact = new CreateContact();
        $createContact['email'] = $email;
        $createContact['listIds'] = [$listId];

        try {
            $apiInstance->createContact($createContact);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    //Update contact attributes
    public function updateContact($email, array $attributes): void
    {
        $config = $this->getConfig();

        $apiInstance = new ContactsApi(new Client(), $config);

        $updateContact= new UpdateContact();

        $updateContact['attributes'] = $attributes;

        try {
            $apiInstance->updateContact($email, $updateContact);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    //Check if contact exist on Api
    public function isContact($email): bool
    {
        $config = $this->getConfig();

        $apiInstance = new ContactsApi(new Client(), $config);

        $result = $apiInstance->getContactInfo($email);
        if ($result === null) return false;
        else return true;
    }
}