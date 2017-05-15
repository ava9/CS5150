<?php
// Server side validation to check that forms have required fields filled
if (empty($_POST['name'])) {
  $nameError = 'Missing';
}
else {
  $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
}
if (empty($_POST['email'])) {
  $emailError = 'Missing';
}
else {
  $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format"; 
      }
}
if (empty($_POST['mobile'])) {
  $mobileError = 'Missing';
}
else {
  $mobile = filter_var($_POST['mobile'], FILTER_SANITIZE_STRING);
}
if (empty($_POST['password'])) {
  $passwordError = 'Missing';
}
else {
  $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
}
if (empty($_POST['confirmPassword'])) {
  $confirmPasswordError = 'Missing';
}
else {
  $confirmPassword = filter_var($_POST['confirmPassword'], FILTER_SANITIZE_STRING);
}
?>