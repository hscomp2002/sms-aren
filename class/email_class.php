<?php
//	require 'phpmailer/PHPMailerAutoload.php'; 
include('../class/phpmailer/class.phpmailer.php');
include('../class/phpmailer/class.pop3.php');
include('../class/phpmailer/class.smtp.php');


	class email_class
	{
		public function __construct($to,$subject,$message,$from='info@darma.ir')
		{
			$out = FALSE;
			//$to = "hscomp2002@gamil.com";
			//$subject = "ﻢﻫﺭﺩﺍﺩ";
			//$message = "ﻖﻫﺮﻣﺎﻨﻫ";
			if($to != '' && $subject != '' && $message != '')
			{
/*
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=utf8' . "\r\n";
				$headers .= 'From: '. $from . "\r\n";
				$out =  mail($to,$subject ,'<html><body dir="rtl">'.$message.'</body></html>',$headers);
*/
				$mail = new PHPMailer;

				$mail->isSMTP();                                      // Set mailer to use SMTP
				$mail->Host = 'mail.darma.ir';  // Specify main and backup SMTP servers
				$mail->SMTPAuth = true;                               // Enable SMTP authentication
				$mail->Username = 'info@darma.ir';                 // SMTP username
				$mail->Password = 'darma@159951';                           // SMTP password
				$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

				$mail->From = 'info@darma.ir';
				$mail->CharSet='UTF-8';
				$mail->FromName = 'گروه بازرگانی دارما';
				//$mail->addAddress('mad_moon_lover@yahoo.com', 'مهرداد میرسمیع');     // Add a recipient
				$mail->addAddress($to);               // Name is optional
				//$mail->addReplyTo('info@example.com', 'Information');
				//$mail->addCC('cc@example.com');
				//$mail->addBCC('bcc@example.com');

				$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
				//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
				//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
				$mail->isHTML(true);                                  // Set email format to HTML

				$mail->Subject = $subject;
				$mail->Body    = $message;//'بدنه ایمیل بصورات زیر است <b>بلد شده است</b>';
				$mail->AltBody = $message;//'الت بادی ';
				/*
				if(!$mail->send()) {
				    echo 'Message could not be sent.';
				    echo 'Mailer Error: ' . $mail->ErrorInfo;
				} else {
				    echo 'Message has been sent';
				}
				*/
				$out = $mail->send();
			}
			return($out);
		}
	}
?>
