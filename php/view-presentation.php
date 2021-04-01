<?php

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
require '../vendor/autoload.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tyreosdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
    $conn->close();
}

$firstname = mysqli_real_escape_string($conn, $_REQUEST['firstName']);
$lastname = mysqli_real_escape_string($conn, $_REQUEST['lastName']);
$email = mysqli_real_escape_string($conn, $_REQUEST['email']);
$mobile = mysqli_real_escape_string($conn, $_REQUEST['mobile']);

$randomOtp = generateRandomOTP();

// for SMS
/*if(sendOtpToMobile($mobile)) {
    doEntryOfOTPInDb($firstname, $lastname, $email, $mobile, $randomOtp, $conn);
}*/

if(sendAnEmailToAdmin($firstname, $lastname, $email, $mobile, $randomOtp, $conn)) {
    doEntryOfOTPInDb($firstname, $lastname, $email, $mobile, $randomOtp, $conn);
}

function sendAnEmailToAdmin(string $firstname, string $lastname, string $email, string $mobile, string $randomOtp, object $conn) : bool {
    $emailSent = false;
    
    //Create a new PHPMailer instance
    $mail = new PHPMailer();
    //Tell PHPMailer to use SMTP
    $mail->isSMTP();
    //Enable SMTP debugging
    //SMTP::DEBUG_OFF = off (for production use)
    //SMTP::DEBUG_CLIENT = client messages
    //SMTP::DEBUG_SERVER = client and server messages
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    //Set the hostname of the mail server
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->SMTPAuth = true;
    $mail->Username = 'tyreoshield.inquiry@gmail.com';
    $mail->Password = 'Admin@123';
    $mail->setFrom('vivek.fullstack.dev@gmail.com', 'Vivek Soni');
    //Set an alternative reply-to address
    //$mail->addReplyTo('replyto@example.com', 'First Last');
    //Set who the message is to be sent to
    $mail->addAddress('viveksoni100@gmail.com', 'Vivekbhai');
    //Set the subject line
    $mail->Subject = 'PHPMailer GMail SMTP test';
    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    //$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
    //Replace the plain text body with one created manually
    $mail->Body = 'This is a plain-text message body';
    $mail->AltBody = 'This is a plain-text message body';
    //Attach an image file
    //$mail->addAttachment('images/phpmailer_mini.png');
    //send the message, check for errors
    if (!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message sent!';
        $emailSent = true;
    }
    return $emailSent;
}

function doEntryOfOTPInDb(string $firstname, string $lastname, string $email, string $mobile, string $randomOtp, object $conn) {
    $sql = "INSERT INTO presentation_view_table (first_name, last_name, email, mobile_no, otp)
    VALUES ('$firstname', '$lastname', '$email', '$mobile', '$randomOtp')";

    if ($conn->query($sql) === TRUE) {
        //for opt SMS
        //header("Location: ../assets/html/otp/index.html?mobile-no=".$mobile);
        header("Location: https://drive.google.com/file/d/1rTRPF4rl4IV29wpaymbAJYVkdwSP908q/view");
        $conn->close();
        exit();
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
        $conn->close();
    }
}

function sendOtpToMobile(string $mobile) : bool {
    $smsSent = true;
    
    $apiKey = urlencode('pA7Ax32bMs4-9BoaWw8BWVDOxcvbY4nFO30iVcyMq5');
    
    $numbers = array((int)$mobile);
    $sender = urlencode('TXTLCL');
    $message = rawurlencode(generateSMS($mobile));
    
    $numbers = implode(',', $numbers);
    
    // Prepare data for POST request
    $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
    
    // Send the POST request with cURL
    $ch = curl_init('https://api.textlocal.in/send/');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    // Process your response here
    echo $response;
    // echo json_decode($response);
    echo "SMS is sent. . .";
    
    return false;

}

function generateSMS(string $mobile) : string {
    $sms = "Your OTP for accessing tyreoshiled presentation is : ".$mobile."";
    return $sms;
}

function generateRandomOTP() : string {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 4; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>
