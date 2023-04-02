<?php
/*
THIS FILE USES PHPMAILER INSTEAD OF THE PHP MAIL() FUNCTION
*/

require 'php/PHPMailerAutoload.php';

/*
*  CONFIGURE EVERYTHING HERE
*/

// an email address that will be in the From field of the email.
$fromEmail = 'contact@squishsupply.com';
$fromName = 'Squish contact form';

// an email address that will receive the email with the output of the form
$sendToEmail = 'info@squishsupply.com';
$sendToName = 'Squish';

// subject of the email
$subject = 'Info Request';

// form field names and their translations.
// array variable name => Text to appear in the email
$fields = array('name' => 'Name', 'surname' => 'Surname', 'phone' => 'Phone', 'email' => 'Email', 'message' => 'Message');

$name = $_POST['name'];
$business = $_POST['business'];
$email = $_POST['email'];
$phone = $_POST['phone'];

// message that will be displayed when everything is OK :)
$okMessage = '
			<div class="form-sent">
				<p class="Lust-Italic">Enquiry Received</p>
				<p class="Nolan-Book">Thanks for your interest. We’ll contact you shortly.</p>
				<p class="Nolan-Book">If your enquiry is urgent give us a call:</p>
				<p class="Nolan-ExtraBold">020 7720 0488</p>
			</div>
			';

// If something goes wrong, we will display this message.
$errorMessage = '
			<div class="form-error">
				<p class="Lust-Italic">Enquiry Issue</p>
				<p class="Nolan-Book">Oops! Something went wrong.</p>
				<p class="Nolan-Book">Please give us a call</p>
				<p class="Nolan-ExtraBold">020 7720 0488</p>
			</div>
			';

/*
*  LET'S DO THE SENDING
*/

// if you are not debugging and don't need error reporting, turn this off by error_reporting(0);
error_reporting(E_ALL & ~E_NOTICE);

try
{
    
    if(count($_POST) == 0) throw new \Exception('Form is empty');
    
    $emailTextHtml = "<style>
	body {margin: 0;font-family: \'Trebuchet MS\', \'Open Sans\', Arial, Helvetica, sans-serif;color: #575f56;}
	#container {margin: auto;max-width: 640px;}
	#header {background: #1d9002;height: 18px;width: 100%;}
	#main {padding: 20px;}
	#footer {background: #1d9002;color: #ffffff;padding: 20px;text-align: center;}
	#footer a {color: white;text-decoration: none;}
	</style>
	<body>
		<div id='container'>
			<div id='header'><br></div>
			<div id='main'><h2>Price List And Info Request</h2>
			Request for price list and information from <strong>".$name."</strong>.<br>
			Information about contactee:
				<ul>
					<li>Name: <strong>".$name."</strong></li>
					<li>Business: <strong>".$business."</strong></li>
					<li>Email: <strong>".$email."</strong></li>
					<li>Phone: <strong>".$phone."</strong></li>
				</ul>
			</div>
			<div id='footer'>Sent ".date('d/m/Y')."<br>
				<a href='http://www.squishsupply.com/' target='_blank'>www.squishsupply.com</a>
			</div>
		</div>
	</body>";
    
    $mail = new PHPMailer;

    $mail->setFrom($fromEmail, $fromName);
    $mail->addAddress($sendToEmail, $sendToName); // you can add more addresses by simply adding another line with $mail->addAddress();
    $mail->addReplyTo($from);
    
    $mail->isHTML(true);

    $mail->Subject = $subject;
    $mail->msgHTML($emailTextHtml); // this will also create a plain-text version of the HTML email, very handy
    
    
    if(!$mail->send()) {
        throw new \Exception('I could not send the email.' . $mail->ErrorInfo);
    }
    
    $responseArray = array('type' => 'success', 'message' => $okMessage);
}
catch (\Exception $e)
{
    // $responseArray = array('type' => 'danger', 'message' => $errorMessage);
    $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
}


// if requested by AJAX request return JSON response
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $encoded = json_encode($responseArray);
    
    header('Content-Type: application/json');
    
    echo $encoded;
}
// else just display the message
else {
    echo $responseArray['message'];
}