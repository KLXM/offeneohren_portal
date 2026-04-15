<?php

class rex_offeneohren_portal_notification_service
{
    public static function getHtmlWrapper(string $content, string $title = ''): string
    {
        return <<<HTML
            <!DOCTYPE html>
            <html lang="de">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>{$title}</title>
                <style>
                    body {
                        margin: 0;
                        padding: 0;
                        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                        font-size: 14px;
                        line-height: 1.6;
                        color: #333;
                        background-color: #f6f8fa;
                    }
                    .email-container {
                        max-width: 600px;
                        margin: 20px auto;
                        background-color: #ffffff;
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    }
                    .email-header {
                        background-color: #ea33f7; /* Offene Ohren Branding */
                        color: #ffffff;
                        padding: 20px;
                        text-align: center;
                    }
                    .email-header h1 {
                        margin: 0;
                        font-size: 20px;
                        font-weight: 600;
                    }
                    .email-body {
                        padding: 20px;
                    }
                    .email-footer {
                        background-color: #f6f8fa;
                        color: #6a737d;
                        padding: 20px;
                        text-align: center;
                        font-size: 12px;
                        border-top: 1px solid #e1e4e8;
                    }
                    .item-card {
                        background: #fbfbfb;
                        border: 1px solid #eaeaea;
                        padding: 15px;
                        margin-bottom: 15px;
                        border-radius: 5px;
                    }
                    .item-card h3 {
                        margin-top: 0;
                        margin-bottom: 5px;
                        color: #2933F0;
                    }
                    .badge {
                        display: inline-block;
                        padding: 3px 8px;
                        font-size: 12px;
                        font-weight: bold;
                        border-radius: 12px;
                        color: white;
                    }
                    .badge-new { background-color: #1e87f0; }
                    .badge-change { background-color: #faa05a; }
                    .badge-approved { background-color: #32d296; }
                    .badge-rejected { background-color: #f0506e; }
                    hr {
                        border: none;
                        border-top: 1px solid #eee;
                        margin: 20px 0;
                    }
                </style>
            </head>
            <body>
                <div class="email-container">
                    <div class="email-header">
                        <h1>{$title}</h1>
                    </div>
                    <div class="email-body">
                        {$content}
                    </div>
                    <div class="email-footer">
                        <p>Diese E-Mail wurde automatisch vom Offene Ohren Portal versendet.</p>
                        <p>Ihre Benachrichtigungs-Einstellungen können Sie im REDAXO Backend anpassen.</p>
                    </div>
                </div>
            </body>
            </html>
        HTML;
    }

    public static function sendDigest(string $email, string $content, string $subject)
    {
        if (trim($content) === '') {
            return false;
        }

        $html = self::getHtmlWrapper($content, $subject);
        
        $mail = new rex_mailer();
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->Body = $html;
        $mail->isHTML(true);
        
        return $mail->send();
    }
}
