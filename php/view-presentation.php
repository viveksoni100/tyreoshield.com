<?php
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

$sql = "INSERT INTO presentation_view_table (first_name, last_name, email, mobile_no, otp)
VALUES ('$firstname', '$lastname', '$email', '$mobile', '$randomOtp')";

if ($conn->query($sql) === TRUE) {
    header("Location: ../assets/html/otp/index.html?mobile-no=".$mobile);
    $conn->close();
    exit();
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
    $conn->close();
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
