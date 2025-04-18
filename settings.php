<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
$name=$email=$mess=$sub='';
$res=NULL;
if($_SERVER['REQUEST_METHOD']=="POST"){
  if(!empty($_POST['name'])&&!empty($_POST['email'])&&!empty($_POST['subject'])&&!empty($_POST['message'])){
    $name=$_POST['name'];
    $email=$_POST['email'];
    $mess=$_POST['message'];
    $sub=$_POST['subject'];
    $mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'testing442user@gmail.com'; 
    $mail->Password   = 'kkhb jwni kldb xwml'; 
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

   
    $mail->setFrom($email,$name);
    $mail->addAddress('sniperking5681@gmail.com');


    $mail->isHTML(true);
    $mail->Subject =$sub;
    $mail->Body=$mess;

    $mail->send();
    $res='Email Sent Successfully';
} catch (Exception $e) {
    $res='Failed to Send Email,Try Again Later!';
}
  }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;400;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    nav {
      background-color: rgb(30, 30, 144);
      padding: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    nav a {
      color: #A9A9A9;
      text-decoration: none;
      margin: 0 15px;
      font-weight: bold;
    }

    nav a:hover {
      color: #3498db;
    }

    .header {
      background-color:rgb(30, 30, 144);
      text-align: center;
      padding: 30px 10px;
    }

    .header h1 {
      font-size: 80px;
      color: #A9A9A9;
      margin: 10px 0;
    }

    .container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      padding: 40px 20px;
      flex: 1;
    }

    .contcol {
      max-width: 400px;
      color: #777;
      margin: 20px;
    }

    .form-container {
      max-width: 500px;
      width: 100%;
      margin: 20px;
      color: #777;
    }

    .form-container label h2 {
      margin: 10px 0 5px;
      font-size: 16px;
    }

    .form-container input,
    .form-container textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 16px;
    }

    .form-container textarea {
      resize: vertical;
    }

    .contbtn {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .contbtn:hover {
      background-color: #2980b9;
    }

    footer {
      background-color: #0C0A33;
      color: white;
      padding: 20px;
      text-align: center;
      font-size: 18px;
      margin-top: auto;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        align-items: center;
      }

      .header h1 {
        font-size: 48px;
      }
    }
  </style>
</head>
<body>
  <nav>
    <div>
      <a href="intro.html">Home</a>
      <a href="dashboard.php">Dashboard</a>
      <a href="contact.html">Rate Us</a>
    </div>
    <a href="login2.html" style="background-color: #e74c3c; padding: 10px 20px; border-radius: 5px; color: #fff; text-decoration: none; font-weight: bold;">Logout</a>
  </nav>

  <div class="header">
    <h1>Contact</h1>
  </div>

  <div class="container">
    <div class="contcol">
      <h2>Lovely Professional University, Phagwara</h2>
      <p>Pincode: 444000, Punjab, INDIA</p>
      <h2>+91 7007471499</h2>
      <p>Monday - Saturday, 10AM - 6PM</p>
      <h2>sniperking5681@gmail.com</h2>
    </div>

    <div class="form-container">
      <form method="post">
        <label><h2>Name</h2></label>
        <input type="text" placeholder="Enter your name" name="name" required />

        <label><h2>Email ID</h2></label>
        <input type="email" placeholder="Enter your email id" name="email" required />

        <label><h2>Subject</h2></label>
        <input type="text" placeholder="Enter your subject" name="subject" required />

        <label><h2>Message</h2></label>
        <textarea rows="4" placeholder="Type your message here" name="message" required></textarea>

        <button type="submit" class="contbtn">Email Us</button>
      </form>
      <div style="font-family: Arial, Helvetica, sans-serif;color:green;margin-top:5px;">
      <?php if(isset($res)){
        echo $res.'!';
      }?>
      </div>
      
    </div>
  </div>

  <footer>
    &copy; 2025 Faculty Management System
  </footer>
</body>
</html>
