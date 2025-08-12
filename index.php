<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpanelUser = trim($_POST['cpanel_user']);
    $cpanelPass = trim($_POST['cpanel_pass']);
    $cpanelHost = trim($_POST['cpanel_host']);
    $emailDomain = trim($_POST['email_domain']);
    $defaultPassword = trim($_POST['email_pass']);
    $quotaMB = intval($_POST['quota']);
    $usernames = explode("\n", trim($_POST['usernames']));

    $results = [];

    foreach ($usernames as $user) {
        $user = trim($user);
        if (empty($user)) continue;

        $query = "https://{$cpanelHost}:2083/execute/Email/add_pop?"
            . "email={$user}&password={$defaultPassword}&quota={$quotaMB}&domain={$emailDomain}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "{$cpanelUser}:{$cpanelPass}");
        curl_setopt($ch, CURLOPT_URL, $query);

        $result = curl_exec($ch);
        curl_close($ch);

        $results[] = "{$user}@{$emailDomain} → {$result}";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bulk Email Account Creator</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; padding: 20px; }
        h2 { text-align: center; }
        form { background: white; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto; }
        input, textarea, button { width: 100%; padding: 10px; margin: 6px 0; border-radius: 5px; border: 1px solid #ccc; }
        button { background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        pre { background: #eee; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
<h2>Bulk Email Account Creator (cPanel)</h2>
<form method="post">
    <input type="text" name="cpanel_user" placeholder="cPanel Username" required>
    <input type="password" name="cpanel_pass" placeholder="cPanel Password" required>
    <input type="text" name="cpanel_host" placeholder="cPanel Host (e.g., yourdomain.com)" required>
    <input type="text" name="email_domain" placeholder="Email Domain (e.g., yourdomain.com)" required>
    <input type="text" name="email_pass" placeholder="Password for All Accounts" required>
    <input type="number" name="quota" placeholder="Mailbox Size (MB)" value="500" required>
    <textarea name="usernames" placeholder="Enter usernames (one per line)" rows="6" required></textarea>
    <button type="submit">Create Accounts</button>
</form>

<?php if (!empty($results)): ?>
    <h3>Results:</h3>
    <pre><?php echo implode("\n", $results); ?></pre>
<?php endif; ?>
</body>
</html>
