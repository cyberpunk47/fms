<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $name    = $_POST['student_name'] ?? '';
        $email   = $_POST['student_id'] ?? '';
        $ui      = $_POST['ui'] ?? 0;
        $flow    = $_POST['flow'] ?? 0;
        $feature = $_POST['feature'] ?? 0;
        $support = $_POST['support'] ?? 0;
        $remark  = $_POST['comments'] ?? '';

        $stmt = $pdo->prepare("INSERT INTO rating (Name, email, ui, flow, feature, support, remark) 
                               VALUES (:name, :email, :ui, :flow, :feature, :support, :remark)");

        $stmt->execute([
            ':name'    => $name,
            ':email'   => $email,
            ':ui'      => $ui,
            ':flow'    => $flow,
            ':feature' => $feature,
            ':support' => $support,
            ':remark'  => $remark,
        ]);
        echo "<script>alert('Form Submitted');</script>";

    } catch (PDOException $e) {
        echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Assessment Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gradient-to-t from-orange-300 via-white to-orange-300">
    <aside class="w-64 h-screen bg-orange-100 shadow-lg p-6 rounded-2xl">
        <h2 class="text-xl text-center font-bold text-gray-800 mb-6">Navigation</h2>
        <nav class="space-y-10">
            <a href="intro.html" class="flex items-center p-3 rounded-lg hover:bg-orange-700 text-gray-700 border-b-2 border-y-amber-900">
                <img src="home.png" alt="Dashboard" class="w-6 h-6 mr-3 rounded-full">
                Home
            </a>
            <a href="dashboard.php" class="flex items-center p-3 rounded-lg hover:bg-orange-700 text-gray-700 border-b-2 border-y-amber-900">
                <img src="contact.png" alt="Contact" class="w-6 h-6 mr-3">
                Dashboard
            </a>
            <a href="settings.php" class="flex items-center p-3 rounded-lg hover:bg-orange-700 text-gray-700 border-b-2 border-y-amber-900">
                <img src="help.png" alt="Help" class="w-6 h-6 mr-3">
                Contact Us
            </a>
        </nav>
    </aside>
    <main class="flex-1 p-8">
        <div class="bg-pink-100 shadow-lg border border-gray-300 rounded-2xl p-8 max-w-2xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Ratings</h2>

            <form action="" method="POST" class="space-y-6">
                <div>
                    <label class="block text-gray-800 font-semibold">Your Name</label>
                    <input type="text" name="student_name" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                </div>

                <div>
                    <label class="block text-gray-800 font-semibold">Email</label>
                    <input type="email" name="student_id" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <div class="bg-gray-50 p-6 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Rate this Application:</h3>

                    <div class="space-y-4">
                        <?php
                        $questions = [
                            "User Interface" => "ui",
                            "Content Flow" => "flow",
                            "Features" => "feature",
                            "Approachability & Support" => "support"
                        ];
                        
                        foreach ($questions as $label => $id) {
                            echo "
                                <div>
                                    <label class='block text-gray-800'>$label: <span id='{$id}-value'>3</span></label>
                                    <input type='range' name='{$id}' min='1' max='5' value='3' class='w-full' oninput=\"document.getElementById('{$id}-value').textContent = this.value\">
                                </div>
                            ";
                        }
                        ?>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-800 font-semibold">Additional Comments</label>
                    <textarea name="comments" rows="4" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-900 text-white py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-pink-800 transition">
                    Submit Feedback
                </button>
            </form>
        </div>

    </main>
</body>
</html>
