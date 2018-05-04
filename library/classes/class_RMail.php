<?php

use Zend\Mime;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

class RMail
{
    /**
     * @param string $subject
     * @param string $htmlMessage
     * @param array $toEmails array('9220rules@hotmail.com'=>'Bjarne Thomsen', 'kaj@yahoo.com'=>'Kaj Ikast')
     * @param array $ccEmails
     * @param array $bccEmails
     * @param string $fromEmail
     * @param string $fromName
     * @param string $replyToEmail
     * @param string $replyToName
     * @return Message
     */
    public static function createMessage(string $subject, string $htmlMessage = "", array $toEmails = array(), array $ccEmails = array(), array $bccEmails = array(),
                                string $fromEmail = "it@jutlander.dk",
                                string $fromName = "Jutlander Portalen",
                                ?string $replyToEmail = null,
                                ?string $replyToName = null
                                ):Message
    {
        $mail = new Message();

        $mail->setFrom($fromEmail, Mime\Mime::encodeQuotedPrintableHeader($fromName, 'UTF-8'));
        if ($replyToEmail){
            $mail->setReplyTo($replyToEmail, Mime\Mime::encodeQuotedPrintableHeader($replyToName, 'UTF-8'));
        }

        foreach ($toEmails as $address => $name) {
            $mail->addTo($address, Mime\Mime::encodeQuotedPrintableHeader($name, 'UTF-8'));
        }

        foreach ($ccEmails as $address => $name) {
            $mail->addCc($address, Mime\Mime::encodeQuotedPrintableHeader($name, 'UTF-8'));
        }

        foreach ($bccEmails as $address => $name) {
            $mail->addBcc($address, Mime\Mime::encodeQuotedPrintableHeader($name, 'UTF-8'));
        }


        // The multipart body
        $mimeMessage = new Mime\Message();

        // The text part
        $html = new Mime\Part(self::emailLayout(nl2br($htmlMessage)));
        $html->setType(Mime\Mime::TYPE_HTML);
        $html->setCharset('UTF-8');
        $mimeMessage->addPart($html);

        $mail->setBody($mimeMessage);
        $mail->setSubject($subject);

        return $mail;
    }

    public static function createSmtpTransport():SmtpTransport{
        $transport = new SmtpTransport();
        $options = new SmtpOptions(array(
            'host' => '10.31.130.237'
        ));
        $transport->setOptions($options);

        return $transport;
    }

    public static function send(Message $message, ?TransportInterface $transport = null){
        if (!$transport){
            $transport = self::createSmtpTransport();
        }
        $transport->send($message);
    }

    public static function sendSimple($toEmail, $subject, $htmlBody ="", $fromName = "Jutlander Portalen", $fromEmail = "it@jutlander.dk"){
        self::send(self::createMessage($subject, $htmlBody, array($toEmail=>$toEmail), array(), array(), $fromEmail, $fromName));
    }


    private static function emailLayout(string $body): string
    {
        return "<html><head><style>
            body {
                font-family: verdana,helvetica,arial,sans-serif;
                font-size: 12px;
            }
        </style></head><body>$body</body></html>";
    }


}
