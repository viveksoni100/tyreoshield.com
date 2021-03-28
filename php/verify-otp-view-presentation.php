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

$digitOne = mysqli_real_escape_string($conn, $_REQUEST['digit-one']);
$digitTwo = mysqli_real_escape_string($conn, $_REQUEST['digit-two']);
$digitThree = mysqli_real_escape_string($conn, $_REQUEST['digit-three']);
$digitFour = mysqli_real_escape_string($conn, $_REQUEST['digit-four']);
$mobile = mysqli_real_escape_string($conn, $_REQUEST['mobile']);

$inputOtp = $digitOne.$digitTwo.$digitThree.$digitFour;
$sentOtp;

$sql = "SELECT * FROM presentation_view_table WHERE mobile_no='".$mobile."' ORDER BY created_at DESC LIMIT 1;";


if ($result = mysqli_query($conn, $sql)) {
    $sentOtp = retrieveAndAssignOtp($result);
    if(verifyOtp($inputOtp, $sentOtp)) {
        header("Location: https://drive.google.com/file/d/1rTRPF4rl4IV29wpaymbAJYVkdwSP908q/view");
    } else { 
        header("Location: ./assets/html/otp/index.html?mobile-no=".$mobile."&otp-input-flag=wrong");
    }
    $conn->close();
    exit();
} else {
    echo "Failure!";
  echo "Error: " . $sql . "<br>" . $conn->error;
    $conn->close();
}

function retrieveAndAssignOtp(mysqli_result $result) : string {
    while($obj = $result->fetch_object()){
            $sentOtp = $obj->otp;
        }
    return $sentOtp;
}

function verifyOtp(string $inputOtp, string $sentOtp) : int {
    if(strcmp($inputOtp, $sentOtp)) {
        return 0;
    }
    return 1;
}

?>
