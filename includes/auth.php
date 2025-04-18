<?php

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function login($email, $password)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT user_id, first_name, last_name, email, password_hash, salt, role, account_status 
                               FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['account_status'] == 'active') {
            $hashed_input = hash('sha256', $password . $user['salt']);
            
            if (password_verify($hashed_input, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name']; 
                $_SESSION['user_email'] = $email;

                $update = $pdo->prepare("UPDATE users SET last_login = NOW(), failed_login_attempts = 0 WHERE user_id = ?");
                $update->execute([$user['user_id']]);
                
                return true;
            }
        }
        return false;
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return false;
    }
}

function logout()
{
    $_SESSION = array();
    session_destroy();
    header("Location: index.php");
    exit();
}