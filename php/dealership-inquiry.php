<?php

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
require '../vendor/autoload.php';
require_once '../constants.php';

$servername = getServernameForDBConnection();
$username = getUsernameForDBConnection();
$password = getPasswordForDBConnection();
$dbname = getDBNameForDBConnection();

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
    $conn->close();
}

$firstname = mysqli_real_escape_string($conn, $_REQUEST['firstName']);
$lastname = mysqli_real_escape_string($conn, $_REQUEST['lastName']);
$mobile = mysqli_real_escape_string($conn, $_REQUEST['mobile']);
$city = mysqli_real_escape_string($conn, $_REQUEST['city']);
$pincode = mysqli_real_escape_string($conn, $_REQUEST['pincode']);
$state = mysqli_real_escape_string($conn, $_REQUEST['state']);
$country = mysqli_real_escape_string($conn, $_REQUEST['country']);
$inqfor = mysqli_real_escape_string($conn, $_REQUEST['inq-for']);

// for email
if(sendAnEmailToAdmin($firstname, $lastname, $mobile, $city, $pincode, $state, $country, $inqfor)) {
    doEntryOfOTPInDb($firstname, $lastname, $mobile, $city, $pincode, $state, $country, $inqfor, $conn);
}

function sendAnEmailToAdmin(string $firstname, string $lastname, string $mobile, string $city, int $pincode, string $state, string $country, string $inqfor) : bool {
    $emailSent = false;
    
    //Create a new PHPMailer instance
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->CharSet = "utf-8";
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Host = getSMTPHost();
    $mail->Port = getSMTPPort();
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPAuth = true;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->Username = getSMTPUserName();
    $mail->Password = getSMTPPassword();
    $mail->setFrom(getSMTPUserName(), 'Administrator');
    $mail->addAddress(getTyreOShieldInfoMailId(), 'Tusharbhai');
    //Set the subject line
    $mail->Subject = "Dealership inquiry form submission by ".$mobile."";
    $mail->Body = "<h3> Dealership inquiry form </h3><br/>"."<b>Name : </b>".$firstname." ".$lastname."<br/>"."<b>Mobile : </b>".$mobile."<br/>"."<b>City : </b>".$city."<br/>"."<b>Pincode : </b>".strval($pincode)."<br/>"."<b>State : </b>".$state."<br/>"."<b>Country : </b>".$country."<br/>"."<b>Inquiry for : </b>".$inqfor."";
    /*$mail->Body = getBody($firstname, $lastname, $email, $mobile);*/
    $mail->AltBody = 'This is a plain-text message body';
    
    $mail->isHTML(true);// Set email format to HTML

    if (!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Email Sent!!!';
        $emailSent = true;
    }
    return $emailSent;
}

function doEntryOfOTPInDb(string $firstname, string $lastname, string $mobile, string $city, int $pincode, string $state, string $country, string $inqfor, object $conn) {
    $sql = "INSERT INTO dealership_inquiry_form (first_name, last_name, mobile_no, city, pincode, state, country, inquiry_for)
    VALUES ('$firstname', '$lastname', '$mobile', '$city', $pincode, '$state', '$country', '$inqfor')";

    if ($conn->query($sql) === TRUE) {
        echo '<script type="text/javascript"> window.location = "../assets/html/msg/inquiry-submitted.html" </script>';
        //header("Location: ../assets/html/msg/inquiry-submitted.html"); 
        $conn->close();
        exit();
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
        $conn->close();
    }
}

?>