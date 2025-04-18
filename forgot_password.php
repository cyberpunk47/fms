<?php
session_start();
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once 'includes/config.php';
global $pdo;

function send_otp($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'viciousflagbearer@gmail.com'; 
        $mail->Password = 'etzg ailh biai nski';    
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        
        $mail->setFrom('ayushsingh123102@gmail.com', 'FARS Support');
        $mail->addAddress($email);

        
        $mail->isHTML(true);
        $mail->Subject = 'FARS Password Reset OTP';
        $mail->Body    = "<h2>Your OTP is: <strong>$otp</strong></h2><p>Use this to reset your password. It expires in 5 minutes.</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

$message = '';
$step = 'email';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with($email, '@gmail.com')) {
            $message = "Enter a valid Gmail ID.";
        } else {
        
            $stmt = $pdo->prepare("SELECT * FROM facultydata WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() === 1) {
                $otp = rand(100000, 999999);
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_otp'] = $otp;
                $_SESSION['otp_expiry'] = time() + 300;

                if (send_otp($email, $otp)) {
                    $step = 'otp';
                } else {
                    $message = "Failed to send OTP. Try again later.";
                }
            } else {
                $message = "Email not found in records.";
            }
        }
    } elseif (isset($_POST['otp'])) {
        if ($_POST['otp'] == $_SESSION['reset_otp'] && time() <= $_SESSION['otp_expiry']) {
            $step = 'reset';
        } else {
            $message = "Invalid or expired OTP.";
            $step = 'otp';
        }
    } elseif (isset($_POST['new_password'])) {
        $email = $_SESSION['reset_email'];
        $newPassword = $_POST['new_password'];
        $stmt = $pdo->prepare("UPDATE facultydata SET password = ? WHERE email = ?");
        $stmt->execute([$newPassword, $email]);
        header("Location: faculty_login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Forget Password - FARS</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 to-gray-700 min-h-screen flex items-center justify-center text-white">
  <div class="bg-white/10 backdrop-blur-md shadow-lg rounded-lg p-8 w-full max-w-md">
    <h2 class="text-2xl font-bold text-center mb-6 text-yellow-300">Reset Your Password</h2>

    <?php if (!empty($message)): ?>
      <p class="text-red-400 text-sm text-center mb-4"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($step === 'email'): ?>
      <form method="POST" class="space-y-4">
        <label class="block">
          <span class="text-sm">Enter your Gmail ID</span>
          <input type="email" name="email" required class="w-full mt-1 px-3 py-2 bg-white/20 border border-gray-300 rounded text-white focus:outline-none focus:ring-2 focus:ring-yellow-400" placeholder="you@gmail.com" />
        </label>
        <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-2 px-4 rounded">Send OTP</button>
      </form>

    <?php elseif ($step === 'otp'): ?>
      <form method="POST" class="space-y-4">
        <label class="block">
          <span class="text-sm">Enter OTP sent to your Gmail</span>
          <input type="text" name="otp" required class="w-full mt-1 px-3 py-2 bg-white/20 border border-gray-300 rounded text-white focus:outline-none focus:ring-2 focus:ring-yellow-400" placeholder="6-digit code" />
        </label>
        <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-2 px-4 rounded">Verify OTP</button>
      </form>

    <?php elseif ($step === 'reset'): ?>
      <form method="POST" class="space-y-4">
        <label class="block">
          <span class="text-sm">Enter New Password</span>
          <input type="password" name="new_password" required class="w-full mt-1 px-3 py-2 bg-white/20 border border-gray-300 rounded text-white focus:outline-none focus:ring-2 focus:ring-yellow-400" />
        </label>
        <button type="submit" class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-2 px-4 rounded">Reset Password</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
