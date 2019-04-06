<?php
header('Content-type: application/json');
require_once('php-mailer/PHPMailerAutoload.php'); // Include PHPMailer

$mail = new PHPMailer();
$emailTO = $emailBCC =  $emailCC = array(); $formEmail = '';

### Enter Your Sitename 
$sitename = 'nmaq';

### Enter your email addresses: @required
$emailTO[] = array( 'email' => 'nour3_@hotmail.com', 'name' => 'nmaq website' ); 

### Enable bellow parameters & update your BCC email if require.
$emailBCC[] = array( 'email' => 'abxotb@gmail.com', 'name' => 'Website Devolper' );

### Enable bellow parameters & update your CC email if require.
//$emailCC[] = array( 'email' => 'email@yoursite.com', 'name' => 'Your Name' );

### Enter Email Subject
$subject = "Contact Us " . ' - ' . $sitename; 

$formEmail = 'nour3_@hotmail.com';

### Success Messages
$msg_success = "تم استقبال <strong>طلب التواصل</strong> منكم بنجاح، سيتم التواصل معكم بأسرع وقت ممكن";

if( $_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST["contact-email"]) && $_POST["contact-email"] != '' && isset($_POST["contact-name"]) && $_POST["contact-name"] != '') {
		### Form Fields
		$cf_email = $_POST["contact-email"];
		$cf_name = $_POST["contact-name"];
		$cf_service = isset($_POST["contact-service"]) ? $_POST["contact-service"] : '';
		$cf_message = isset($_POST["contact-message"]) ? $_POST["contact-message"] : '';

		$honeypot 	= isset($_POST["form-anti-honeypot"]) ? $_POST["form-anti-honeypot"] : 'bot';
		$bodymsg = '';
		
		if ($honeypot == '' && !(empty($emailTO))) {
			### If you want use SMTP 
			// $mail->isSMTP();
			// $mail->SMTPDebug = 0;
			// $mail->Host = 'smtp.gmail.com';
			// $mail->Port = 587;
			// $mail->SMTPAuth = true;
			// $mail->Username = 'info.nmq.sa@gmail.com';
			// $mail->Password = 'nmq@1234';

			### Regular email configure
			$mail->IsHTML(true);
			$mail->CharSet = 'UTF-8';

			$mail->From = ($formEmail !='') ? $formEmail : $cf_email;
			$mail->FromName = $cf_name . ' - ' . $sitename;
			$mail->AddReplyTo($cf_email, $cf_name);
			$mail->Subject = $subject;
			
			foreach( $emailTO as $to ) {
				$mail->AddAddress( $to['email'] , $to['name'] );
			}
			
			### if CC found
			if (!empty($emailCC)) {
				foreach( $emailCC as $cc ) {
					$mail->AddCC( $cc['email'] , $cc['name'] );
				}
			}
			
			### if BCC found
			if (!empty($emailBCC)) {
				foreach( $emailBCC as $bcc ) {
					$mail->AddBCC( $bcc['email'] , $bcc['name'] );
				}				
			}

			### Include Form Fields into Body Message
			$bodymsg .= isset($cf_name) ? "Contact Name: $cf_name<br><br>" : '';
			$bodymsg .= isset($cf_email) ? "Contact Email: $cf_email<br><br>" : '';
			$bodymsg .= isset($cf_service) ? "Selected Service: $cf_service<br><br>" : '';
			$bodymsg .= isset($cf_message) ? "Message: $cf_message<br><br>" : '';
			$bodymsg .= $_SERVER['HTTP_REFERER'] ? '<br>---<br><br>This email was sent from [ICO]: ' . $_SERVER['HTTP_REFERER'] : '';
			
			// Mailing
			$mail->MsgHTML( $bodymsg );
			$is_emailed = $mail->Send();

			if( $is_emailed === true ) {
				$response = array ('result' => "success", 'message' => $msg_success);
			} else {
				$response = array ('result' => "error", 'message' => $mail->ErrorInfo);
			}
			echo json_encode($response);
			
		} else {
			echo json_encode(array ('result' => "error", 'message' => "Bot <strong>Detected</strong>.! Clean yourself Botster.!"));
		}
	} else {
		echo json_encode(array ('result' => "error", 'message' => "من فضلك <strong>قم بتعبئة</strong> جميع الحقول المطلوبة وأعد الإرسال"));
	}
}