<?php

    class UtilEmail
    {

        private function get_email_header ()
        {
            $text = '';

            return $text;
        }

        private function get_email_footer ()
        {
            $text = '<hr size="1" />';
            $text .= 'Echipa concordanta.ro';

            return $text;
        }

        function sendSignUpEmail ($email_address, $bcc, $username, $password)
        {
            $text = UtilEmail::get_email_header();
            $text .= 'Va multumim pentru ca v-ati creat un cont la www.cocordanta.ro: <br />';
            $text .= 'Datele dumneavoastra de autentificare sunt: <br />';
            $text .= 'Nume de utilizator: ' . $username . '<br />';
            $text .= 'Parola: ' . $password . '<br />';
            $text .= UtilEmail::get_email_footer();

            $transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
            $mailer = Swift_Mailer::newInstance($transport);
            $message = Swift_Message::newInstance('Cont nou la www.concordanta.ro')
                ->setFrom(array('no-reply@concordanta.ro'))
                ->setTo($email_address)
                ->setBcc($bcc)
                ->setBody($text, 'text/html');

            $result = $mailer->send($message);
        }

        public static function sendContactEmail ($recipients, $name, $subject, $message, $email_from)
        {
            $text = UtilEmail::get_email_header();
            $text .= 'Subiect: ' . $subject . '<br />';
            $text .= 'Nume: ' . $name . '<br />';
            $text .= 'Adresa email: ' . $email_from . '<br />';
            $text .= 'Mesaj: ' . $message;
            $text .= UtilEmail::get_email_footer();

            $transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
            $mailer = Swift_Mailer::newInstance($transport);
            $message = Swift_Message::newInstance('Mesaj CONTACT - concordanta.ro')
                ->setFrom(array('no-reply@concordanta.ro'))
                ->setTo($recipients)
                ->setBody($text, 'text/html');

            $result = $mailer->send($message);
        }

        /**
         *
         * @param $email - The email of the user.
         * @param $password - The new password.
         */
        public static function sendNewPaswordEmail ($email, $password)
        {
            $text = UtilEmail::get_email_header();
            $text .= 'Noua parola este: ' . $password . '<br />';
            $text .= UtilEmail::get_email_footer();

            $transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
            $mailer = Swift_Mailer::newInstance($transport);
            $message = Swift_Message::newInstance('PAROLA nouă - concordanta.ro')
                ->setFrom(array('no-reply@concordanta.ro'))
                ->setTo($email)
                ->setBody($text, 'text/html');

            $result = $mailer->send($message);
        }
    }