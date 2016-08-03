<?php
/**
 * function.php
 *
 * User: 670554666@qq.com
 * Date: 2016/7/19 9:52
 */

/**
 * 导入第三方文件
 *
 * @param string $fileName
 * @return bool
 * @throws Exception
 */
function vendor($fileName)
{
    static $_vendorArray = [];

    if (!isset($_vendorArray[$fileName])) {
        $file = ROOT . '/library/vendor/' . $fileName;
        if (!is_file($file)) {
            throw new \Exception($file, 10016);
        } else {
            require $file;
        }
    }

    return ($_vendorArray[$fileName] = true);
}

/**
 * 发送邮件
 *
 * @param string $address
 * @param string $subject
 * @param string $body
 * @return bool
 * @throws Exception
 */
function mailer($address, $subject, $body)
{
    vendor('PHPMailer-master/PHPMailerAutoload.php');
    $mail = new \PHPMailer();

    $mailConfig = Application::getInstance()->getConfigInstance()->get('mail');

    // $mail->SMTPDebug = 3; // Enable verbose debug output

    $mail->isSMTP();
    $mail->Host = $mailConfig['host'];
    $mail->SMTPAuth = true;
    $mail->Username = $mailConfig['username'];
    $mail->Password = $mailConfig['password'];
    $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
    $mail->Port = $mailConfig['port'];

    $mail->setFrom($mailConfig['fromAddress'], $mailConfig['fromName']);
    $mail->addAddress($address);
    // $mail->addReplyTo('info@example.com', 'Information');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    // $mail->addAttachment('/var/tmp/file.tar.gz');
    $mail->isHTML(true);

    $mail->Subject = $subject;
    $mail->Body = $body;
    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    if(!$mail->send()) {
        throw new \Exception($mail->ErrorInfo, 10017);
    }

    return true;
}