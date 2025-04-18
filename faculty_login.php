<?php
require_once 'includes/config.php';
if($_SERVER['REQUEST_METHOD']=="POST"){
    if(!empty($_POST['uid'])&&isset($_POST['pass'])){
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT f_id,Name,email,password FROM facultydata WHERE email = ?");
            $stmt->execute([$_POST['uid']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if($user['password']==$_POST['pass']){
                session_start();
                $_SESSION['faculty_id'] = $user['f_id'];
                header("Location:faculty_profile.php?id=".$user['f_id']);
                exit;
            }else {
                $error = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return false;
        }
        
    }else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        .butn:hover{
            background:linear-gradient(
    to right,
    #9d174d 30%,
    #ec4899, 
    #9d174d 70% );
}
.butn{
            transition: all 0.5s ease;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="flex items-center justify-center w-screen h-screen bg-gradient-to-br from-blue-900 from-10% to-gray-800">

    
    <div class="flex w-3/4 h-4/5 border-2 shadow-lg bg-white rounded-lg overflow-hidden">

      
        <div class="w-1/2 flex flex-col items-center justify-center bg-gray-200">
            <img src="login.png" class="w-1/2 h-auto object-cover" alt="Login Image">
            <h2 class="text-blue-600"><a href="intro.html"><-Home-></a></h2>
        </div>

        <div class="w-1/2 flex flex-col justify-center px-10 bg-gradient-to-tr from-purple-700 from-50% to-pink-600">
            
          
            <div class="flex justify-center mb-6">
                <img src="icon2.png" class="w-30 h-30 rounded-full " alt="Company Logo">
            </div>

            <h1 class="text-3xl font-bold text-center text-gray-200 mb-6">Login</h1>
            <?php if (!empty($error)): ?>
    <div class="text-red-500 text-sm text-center mb-2"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
            <form action="#" method="POST" class="space-y-4">
                
               
                <div>
                    <label for="role" class="block text-gray-300 font-medium">Role</label>
                    <select name="role" id="role" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black text-white bg-gray-900/45">
                        <option value="Faculty">Faculty</option>
                    </select>
                </div>

              
                <div>
                    <label for="uid" class="block text-gray-300 font-medium">Email ID</label>
                    <input type="text" id="uid" name="uid" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black text-white bg-gray-900/45" placeholder="Enter your User ID">
                </div>

                
                <div>
                    <label for="pass" class="block text-gray-300 font-medium">Password</label>
                    <input type="password" id="pass" name="pass" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-black text-white bg-gray-900/45" placeholder="Enter your password">
                </div>

                <button type="submit" class="w-full mt-4 bg-gradient-to-r from-pink-500 from-30% to-pink-500 to-70% via-pink-800   text-pink-100 font-bold py-2 px-4 rounded-md transition butn">
                    Login
                </button>

           
                <div class="flex justify-between text-sm text-gray-500 mt-2">
                    <a href="forgot_password.php" class="hover:underline text-orange-500">Forgot Password?</a>
                </div>

            </form>
        </div>

    </div>

</body>
</html>
