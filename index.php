<?php
require_once "db.php";

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare usernames
    $usernamesArr = array_filter(array_map('trim', explode("\n", $_POST['usernames'])));
    $totalUsers = count($usernamesArr);

    if ($totalUsers > 0) {
        // Insert job into DB
        $stmt = $pdo->prepare("
            INSERT INTO bulk_email_jobs 
            (cpanel_user, cpanel_pass, cpanel_host, email_domain, email_pass, quota_mb, usernames, total_users, pending_count, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ");

        $stmt->execute([
            trim($_POST['cpanel_user']),
            trim($_POST['cpanel_pass']),
            trim($_POST['cpanel_host']),
            trim($_POST['email_domain']),
            trim($_POST['email_pass']),
            intval($_POST['quota']),
            trim($_POST['usernames']),
            $totalUsers,
            $totalUsers
        ]);

        $message = "✅ Job saved successfully! $totalUsers email(s) queued for creation.";
    } else {
        $message = "❌ No valid usernames provided.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bulk Email Creator (cPanel)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        header {
            background: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .container {
            max-width: 700px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        h2 {
            margin-bottom: 15px;
            color: #333;
        }
        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
        }
        button {
            background: #007bff;
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
<header>
    <h1>Bulk Email Account Creator</h1>
</header>
<div class="container">
    <?php if ($message): ?>
        <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <form method="post">
        <h2>Enter cPanel Details</h2>
        <input type="text" name="cpanel_user" placeholder="cPanel Username" required>
        <input type="password" name="cpanel_pass" placeholder="cPanel Password" required>
        <input type="text" name="cpanel_host" placeholder="cPanel Host (e.g., yourdomain.com)" required>
        
        <h2>Email Settings</h2>
        <input type="text" name="email_domain" placeholder="Email Domain (e.g., yourdomain.com)" required>
        <input type="text" name="email_pass" placeholder="Password for All Accounts" required>
        <input type="number" name="quota" placeholder="Mailbox Size (MB)" value="500" required>
        <textarea name="usernames" placeholder="Enter usernames (one per line)" rows="6" required></textarea>
        
        <button type="submit">Save Job</button>
    </form>
</div>
</body>
</html>
