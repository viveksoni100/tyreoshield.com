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
$typeofvehicle = mysqli_real_escape_string($conn, $_REQUEST['vehicletype']);
$details = mysqli_real_escape_string($conn, $_REQUEST['details']);
$city = mysqli_real_escape_string($conn, $_REQUEST['city']);
$pincode = mysqli_real_escape_string($conn, $_REQUEST['pincode']);
$state = mysqli_real_escape_string($conn, $_REQUEST['state']);
$country = mysqli_real_escape_string($conn, $_REQUEST['country']);

// for email
if(sendAnEmailToAdmin($firstname, $lastname, $mobile, $typeofvehicle, $details, $city, $pincode, $state, $country)) {
    doEntryOfOTPInDb($firstname, $lastname, $mobile, $typeofvehicle, $details, $city, $pincode, $state, $country, $conn);
}

function sendAnEmailToAdmin(string $firstname, string $lastname, string $mobile, string $typeofvehicle, string $details, string $city, int $pincode, string $state, string $country) : bool {
    $emailSent = false;
    
    //Create a new PHPMailer instance
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->CharSet = "utf-8";
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
    
    $mail->Host = 'smtp.gmail.com';
    //$mail->Host = 'smtp.mailtrap.io';
    $mail->Port = 587;
    //$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;
    
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    
    $mail->Username = getTyreOShieldInfoMailId();
    //$mail->Username = '50281ecd935f3f';
    $mail->Password = 'TUREOSHIELD@1985';
    //$mail->Password = '24637539b050bd';
    
    $mail->setFrom('vivek.fullstack.dev@gmail.com', 'Administrator');
    
    $mail->addAddress(getTyreOShieldInfoMailId(), 'Tusharbhai');
    //Set the subject line
    $mail->Subject = "Retail inquiry form submission by ".$mobile."";
    $mail->Body = "<h3> Retail inquiry form </h3><br/>"."<b>Name : </b>".$firstname." ".$lastname."<br/>"."<b>Mobile : </b>".$mobile."<br/>"."<b>Type of Vehicle : </b>".$typeofvehicle."<br/>"."<b>Details : </b>".$details."<br/>"."<b>City : </b>".$city."<br/>"."<b>Pincode : </b>".strval($pincode)."<br/>"."<b>State : </b>".$state."<br/>"."<b>Country : </b>".$country."";
    $mail->AltBody = 'This is a plain-text message body';
    
    $mail->isHTML(true);// Set email format to HTML


    if (!$mail->send()) {
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        $emailSent = true;
    }
    return $emailSent;
}

function doEntryOfOTPInDb(string $firstname, string $lastname, string $mobile, string $typeofvehicle, string $details, string $city, int $pincode, string $state, string $country, object $conn) {
    $sql = "INSERT INTO retail_inquiry_form (first_name, last_name, mobile_no, type_of_vehicle, vehicle_tyre_detail, city, pincode, state, 	country)
    VALUES ('$firstname', '$lastname', '$mobile', '$typeofvehicle', '$details', '$city', $pincode, '$state', '$country')";

    if ($conn->query($sql) === TRUE) {
        $conn->close();
        //header("Location: ../assets/html/msg/inquiry-submitted.html");
        echo '<script type="text/javascript"> window.location = "../assets/html/msg/inquiry-submitted.html" </script>';
        exit();
    } else {
        //header("Location: ../assets/html/error/index.html");
        echo '<script type="text/javascript"> window.location = "../assets/html/error/index.html" </script>';
        $conn->close();
    }
}

?>