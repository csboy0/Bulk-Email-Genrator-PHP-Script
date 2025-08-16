<?php
// ====== CPanel Credentials ======
$cpanelUser = "codersma";
$cpanelToken = "1UKGNFRPOCP2TDAMUZBWB3CF2YOVT1VQ";
$cpanelHost = "codersmail.com"; // Change to your actual domain or cPanel hostname

$result = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email_list'])) {
    // Convert textarea input into an array
    $emailsToCheck = array_filter(array_map('trim', explode("\n", $_POST['email_list'])));
    
    // ====== cPanel API URL ======
    $apiURL = "https://{$cpanelHost}:2083/execute/Email/list_pops";

    // ====== Make API Request ======
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiURL);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: cpanel {$cpanelUser}:{$cpanelToken}"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die("cURL Error: " . curl_error($ch));
    }
    curl_close($ch);

    // ====== Parse Response ======
    $data = json_decode($response, true);

    if (!isset($data['data'])) {
        die("Failed to retrieve email list. Check credentials or API token.");
    }

    $existingEmails = [];
    foreach ($data['data'] as $email) {
        $existingEmails[] = strtolower($email['email']);
    }

    // ====== Compare Lists ======
    $missingEmails = array_diff(
        array_map('strtolower', $emailsToCheck),
        $existingEmails
    );

    // ====== Build Result HTML ======
    $result .= "<h3>Results</h3>";
    if (empty($missingEmails)) {
        $result .= "<p style='color:green;'>✅ All emails already exist.</p>";
    } else {
        $result .= "<p style='color:red;'>❌ Missing Emails:</p><ul>";
        foreach ($missingEmails as $missing) {
            $result .= "<li>" . htmlspecialchars($missing) . "</li>";
        }
        $result .= "</ul>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Email Verifier</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; }
        textarea { width: 100%; height: 200px; }
        button { padding: 10px 20px; background: #0073aa; color: white; border: none; cursor: pointer; }
        button:hover { background: #005f8a; }
        .container { max-width: 600px; margin: auto; background: white; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h2>Bulk Email Verification (cPanel)</h2>
        <form method="POST">
            <label>Enter email list (one per line):</label><br>
            <textarea name="email_list" placeholder="example1@domain.com&#10;example2@domain.com"></textarea><br><br>
            <button type="submit">Verify Emails</button>
        </form>
        <hr>
        <?= $result ?>
    </div>
</body>
</html>
