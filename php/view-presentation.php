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

$sql = "INSERT INTO presentation_view_table (first_name, last_name, email, mobile_no)
VALUES ('$firstname', '$lastname', '$email', '$mobile')";

if ($conn->query($sql) === TRUE) {
    header("Location: https://drive.google.com/file/d/1rTRPF4rl4IV29wpaymbAJYVkdwSP908q/view");
    $conn->close();
    exit();
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
    $conn->close();
}
?>
