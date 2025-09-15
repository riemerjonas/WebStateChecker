<?php

namespace Webchecker\Handlers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Webchecker\Classes\TestResult;
use Webchecker\Utilities\ConfigLoader;
class NotifyHandler
{

    public static function sendNotification(string $url, TestResult $result)
    {
        $configLoader = new ConfigLoader("config.json");
        $websites = $configLoader->get("websites", null);
        if ($websites === null || !is_array($websites)) {
            echo "Fehler: 'websites' ist nicht korrekt in der Konfigurationsdatei definiert.";
            throw new \Exception("Invalid configuration: 'websites' is missing or not an array.");
        }

        $websiteConfig = $websites[$url] ?? null;
        if ($websiteConfig === null) {
            echo "Fehler: Keine Konfiguration für die Website '$url' gefunden.";
            throw new \Exception("Invalid configuration: No configuration found for website '$url'.");
        }

        $notifyAlways = $websiteConfig['notify_always'] ?? false;
        if($result->hasError() || $notifyAlways)
        {
            $recipients = $websiteConfig['notify'];
            $resultJson = $result->toArray();

            $template = $websiteConfig['template_error'];
            if(!$result->hasError()) {
                $template = $websiteConfig['template_success'];
            }

            self::sendEmail($recipients, "Testergebnisse von WebStateCheckService", $template, $resultJson);
        }

    }


    private static function sendEmail(array $to, string $subject, string $template, array $result): void
    {
        $configLoader = new ConfigLoader("config.json");
        $mailSettings = $configLoader->get("mail_settings", []);
    
        $mail = new PHPMailer(true);
        try
        {
            $mail->isSMTP();
            $mail->CharSet = 'UTF-8';
            $mail->Host       = $mailSettings['smtp_server'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $mailSettings['username'];
            $mail->Password   = $mailSettings['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $mailSettings['port'];

            //Recipients
            $mail->setFrom($mailSettings['username'], $mailSettings['display_name']);
            foreach ($to as $recipient) 
            {
                $mail->addAddress($recipient);
            }

            // Load template content
            $templateUrl = dirname(__DIR__, 2) . "/templates/" . $template;
            $templateContent = file_get_contents($templateUrl);
            foreach ($result as $key => $value) 
            {
                $templateContent = str_replace("{{ " . strtoupper($key) . " }}", htmlspecialchars($value), $templateContent);
            }

            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $templateContent;

            $mail->send();
        }
        catch (\Exception $e)
        {
            echo "Fehler beim Senden der E-Mail: " . $e->getMessage();
        }
    }
}
?>