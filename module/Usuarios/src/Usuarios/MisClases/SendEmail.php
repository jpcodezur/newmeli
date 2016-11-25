<?php

namespace Usuarios\MisClases;

use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mail\Transport\SmtpOptions;

class SendEmail {

    private $message;

    public function __construct($to, $from, $subject) {

        $this->message = new Message();
        $this->message->addTo($to)
                ->addFrom($from)
                ->setSubject($subject);
    }

    public function sendEmail($message) {

        // Setup SMTP transport using LOGIN authentication
        $transport = new SmtpTransport();
        $options = new SmtpOptions(array(
            'host' => 'smtp.gmail.com',
            'connection_class' => 'login',
            'connection_config' => array(
                'ssl' => 'tls',
                'username' => 'testcodezur@gmail.com',
                'password' => 'q1w2e3r4Q!W"E#R$',
                
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ),
            'port' => 587,
        ));

        $html = new MimePart($message);
        $html->type = "text/html";

        $body = new MimeMessage();
        $body->addPart($html);

        $this->message->setBody($body);

        $transport->setOptions($options);
        $transport->send($this->message);
    }

}
