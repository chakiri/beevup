<?php


namespace App\Service;
use GuzzleHttp\Client;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendEmail;
use SendinBlue\Client\Model\SendSmtpEmail;
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

    //Function to send emails by Sendinblue SMTP
    public function sendEmailSmtp($subject, $email, array $content, $template): void
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['SENDINBLUE_API_KEY']);

        $apiInstance = new TransactionalEmailsApi(new Client(), $config);

        $sendSmtpEmail = new SendSmtpEmail();

        $sendSmtpEmail['subject'] = $subject;
        $sendSmtpEmail['sender'] = ['name' => $_ENV['DEFAULT_EMAIL_NAME'], 'email' => $_ENV['DEFAULT_EMAIL']];
        $sendSmtpEmail['to'] = [['email' => $email]];
        $sendSmtpEmail['htmlContent'] = $this->templating->render('emails/'.$template, $content);

        try {
            $apiInstance->sendTransacEmail($sendSmtpEmail);
        } catch (\Exception $e) {
            echo 'Exception when calling TransactionalEmailsApi->sendEmailSmtp: ', $e->getMessage();
        }
    }

    public function sendEmailForTemplate($dest, $bcc, $template, $attributes)
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $_ENV['SENDINBLUE_API_KEY']);

        $apiInstance = new TransactionalEmailsApi(new Client(), $config);

        $sendEmail = new SendEmail();
        $sendEmail
            ->setEmailTo($dest)
            ->setReplyTo($_ENV['DEFAULT_EMAIL'])
            ->setAttributes($attributes)
        ;
        if($bcc) { $sendEmail->setEmailBcc([$bcc]); }

        try {
            return $apiInstance->sendTemplate($template, $sendEmail);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }
}