<?php
$conn=mysqli_connect("localhost","root","root","student_registration");
if($conn){
    echo "connected";
}else{
    echo "Failed";
}
?>