<?php
// Get Disk Space Info
$totalDisk = disk_total_space("/");
$freeDisk  = disk_free_space("/");
$usedDisk  = $totalDisk - $freeDisk;

// Get CPU Load
$cpuLoad = sys_getloadavg();

// Get Memory Info (Linux only)
$memInfo = file_get_contents("/proc/meminfo");
preg_match("/MemTotal:\s+(\d+)/", $memInfo, $matchesTotal);
preg_match("/MemAvailable:\s+(\d+)/", $memInfo, $matchesAvailable);

$totalRAM = $matchesTotal[1] * 1024; // bytes
$freeRAM  = $matchesAvailable[1] * 1024;
$usedRAM  = $totalRAM - $freeRAM;

// Function to format sizes
function formatSize($bytes) {
    $sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    while ($bytes >= 1024 && $i < count($sizes) - 1) {
        $bytes /= 1024;
        $i++;
    }
    return round($bytes, 2) . ' ' . $sizes[$i];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Server Monitor</title>
    <meta http-equiv="refresh" content="5"> <!-- auto refresh every 5s -->
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            color: #eee;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: rgba(20, 20, 20, 0.9);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0,255,255,0.3),
                        inset 0 0 20px rgba(0,255,255,0.1);
            max-width: 500px;
            width: 100%;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #0ff;
            text-shadow: 0 0 10px #0ff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        td:first-child {
            color: #aaa;
        }
        td:last-child {
            color: #fff;
            font-weight: bold;
        }
        tr:last-child td {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Server Monitor</h1>
        <table>
            <tr><td>Total Disk</td><td><?= formatSize($totalDisk) ?></td></tr>
            <tr><td>Used Disk</td><td><?= formatSize($usedDisk) ?></td></tr>
            <tr><td>Free Disk</td><td><?= formatSize($freeDisk) ?></td></tr>
            <tr><td>CPU Load (1 min)</td><td><?= $cpuLoad[0] ?></td></tr>
            <tr><td>CPU Load (5 min)</td><td><?= $cpuLoad[1] ?></td></tr>
            <tr><td>CPU Load (15 min)</td><td><?= $cpuLoad[2] ?></td></tr>
            <tr><td>Total RAM</td><td><?= formatSize($totalRAM) ?></td></tr>
            <tr><td>Used RAM</td><td><?= formatSize($usedRAM) ?></td></tr>
            <tr><td>Free RAM</td><td><?= formatSize($freeRAM) ?></td></tr>
        </table>
    </div>
</body>
</html>
