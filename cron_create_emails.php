<?php
// Load database connection
require_once __DIR__ . '/db.php';

// Batch size to avoid timeout (adjust if needed)
$batchSize = 200;

// Fetch jobs that are still pending or processing
$jobs = $pdo->query("SELECT * FROM bulk_email_jobs WHERE status IN ('pending','processing')")->fetchAll(PDO::FETCH_ASSOC);

foreach ($jobs as $job) {
    $jobId = $job['id'];
    $cpanelUser = $job['cpanel_user'];
    $cpanelPass = $job['cpanel_pass'];
    $cpanelHost = $job['cpanel_host'];
    $emailDomain = $job['email_domain'];
    $defaultPassword = $job['email_pass'];
    $quotaMB = intval($job['quota_mb']);
    $usernames = explode("\n", trim($job['usernames']));

    // Clean usernames array
    $usernames = array_map('trim', $usernames);
    $usernames = array_filter($usernames);

    // Determine which users still need to be created
    $alreadyCreated = $job['created_count'];
    $pendingList = array_slice($usernames, $alreadyCreated, $batchSize);

    // If no pending emails left, mark job as done
    if (empty($pendingList)) {
        $stmt = $pdo->prepare("UPDATE bulk_email_jobs SET status = 'done', pending_count = 0, last_updated_at = NOW() WHERE id = ?");
        $stmt->execute([$jobId]);
        continue;
    }

    // Mark job as processing
    $stmt = $pdo->prepare("UPDATE bulk_email_jobs SET status = 'processing' WHERE id = ?");
    $stmt->execute([$jobId]);

    foreach ($pendingList as $user) {
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

        // Log each creation result in a separate table (optional)
        $pdo->prepare("INSERT INTO bulk_email_logs (job_id, email, result, created_at) VALUES (?, ?, ?, NOW())")
            ->execute([$jobId, "{$user}@{$emailDomain}", $result]);

        // Increment created_count
        $alreadyCreated++;
    }

    // Update job counts & last updated time
    $pendingCount = max(0, count($usernames) - $alreadyCreated);
    $status = ($pendingCount === 0) ? 'done' : 'processing';

    $stmt = $pdo->prepare("UPDATE bulk_email_jobs 
                           SET created_count = ?, pending_count = ?, status = ?, last_updated_at = NOW() 
                           WHERE id = ?");
    $stmt->execute([$alreadyCreated, $pendingCount, $status, $jobId]);
}

echo "Cron job completed at " . date("Y-m-d H:i:s") . "\n";
