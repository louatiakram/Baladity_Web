<?php

namespace App\Controller;

use Exception;
use Mailtrap\Config;
use Mailtrap\EmailHeader\CategoryHeader;
use Mailtrap\EmailHeader\CustomVariableHeader;
use Mailtrap\Helper\ResponseHelper;
use Mailtrap\MailtrapClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Header\UnstructuredHeader;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/mailer', name: 'app_mailer')]
    public function index(MailerInterface $mailer): Response
    {

        // your API token from here https://mailtrap.io/api-tokens
        $apiKey = getenv('11930452df1d18cece7a72f4ebe06837');
        $mailtrap = new MailtrapClient(new Config($apiKey));

        $email = (new Email())
    ->from(new Address('example@your-domain-here.com', 'Mailtrap Test'))
    ->replyTo(new Address('reply@your-domain-here.com'))
    ->to(new Address('email@example.com', 'Jon'))
    ->priority(Email::PRIORITY_HIGH)
    ->cc('mailtrapqa@example.com')
    ->addCc('staging@example.com')
    ->bcc('mailtrapdev@example.com')
    ->subject('Best practices of building HTML emails')
    ->text('Hey! Learn the best practices of building HTML emails and play with ready-to-go templates. Mailtrap’s Guide on How to Build HTML Email is live on our blog')
    ->html(
        '<html>
        <body>
        <p><br>Hey</br>
        Learn the best practices of building HTML emails and play with ready-to-go templates.</p>
        <p><a href="https://mailtrap.io/blog/build-html-email/">Mailtrap’s Guide on How to Build HTML Email</a> is live on our blog</p>
        <img src="cid:logo">
        </body>
    </html>'
    )
    ->embed(fopen('https://mailtrap.io/wp-content/uploads/2021/04/mailtrap-new-logo.svg', 'r'), 'logo', 'image/svg+xml')
    ;

    // Headers
    $email->getHeaders()
    ->addTextHeader('X-Message-Source', 'domain.com')
    ->add(new UnstructuredHeader('X-Mailer', 'Mailtrap PHP Client')) // the same as addTextHeader
    ;
    
    // Custom Variables
    $email->getHeaders()
    ->add(new CustomVariableHeader('user_id', '45982'))
    ->add(new CustomVariableHeader('batch_id', 'PSJ-12'))
    ;
    
    // Category (should be only one)
    $email->getHeaders()
    ->add(new CategoryHeader('Integration Test'))
    ;

    try {
        $response = $mailtrap->sending()->emails()->send($email); // Email sending API (real)
        
        var_dump(ResponseHelper::toArray($response)); // body (array)
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
    
    // OR send email to the Mailtrap SANDBOX
    
    try {
        $response = $mailtrap->sandbox()->emails()->send($email, 1000001); // Required second param -> inbox_id
    
        var_dump(ResponseHelper::toArray($response)); // body (array)
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

        $mailer->send($email);
        return new Response(
            'Email was sent'
        );
    }
}
