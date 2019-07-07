<?php

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

class SMTP
{
    /**
     * 给定用户的 uid，查询用户的邮件地址，并给该地址发一封邮件。<br/>
     * 当 uid 不存在对应用户时，抛出异常。<br/>
     * 当发送失败时抛出异常。注意，异常中不包含具体的失败原因。
     * @param int $target_uid 收件人的 uid。
     * @param string $subject 邮件主题。
     * @param string $body 邮件正文的 HTML 形式。
     * @param string $alt_body 邮件正文的纯文本形式。
     * @throws \Exception
     */
    public static function send_html_to_user(int $target_uid, string $subject, string $body, string $alt_body): void
    {
        self::send_html_to_address((new User($target_uid))->get_email(), $subject, $body, $alt_body);
    }

    /**
     * 给指定的邮箱发一封邮件。<br/>
     * 当发送失败时抛出异常。注意，异常中不包含具体的失败原因。
     * @param string $target_address 收件人邮箱地址。
     * @param string $subject 邮件主题。
     * @param string $body 邮件正文的 HTML 形式。
     * @param string $alt_body 邮件正文的纯文本形式。
     * @throws \Exception
     */
    public static function send_html_to_address(string $target_address, string $subject, string $body, string $alt_body): void
    {
        (new Field\EmailField())->check_and_throw($target_address);

        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = SMTP_AUTH;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';
            $mail->SMTPAutoTLS = false;

            $mail->setFrom(SMTP_MAIL_FROM, SMTP_MAIL_DISPLAY_NAME);
            $mail->addAddress($target_address);
            $mail->addReplyTo(SMTP_MAIL_REPLY_TO, 'No-reply');


            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $alt_body;
            $mail->send();
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            throw new \Exception('邮件发送失败: ' . $mail->ErrorInfo);
        }
    }
}
