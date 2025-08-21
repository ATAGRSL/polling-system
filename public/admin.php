<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/PollManager.php';

$config = require __DIR__ . '/../config/config.php';
$pollManager = new PollManager($config);

$authenticated = false;
$message = '';

if (!$config['admin']['enable_admin_panel']) {
    die('Admin paneli devre dışı.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['password']) && $_POST['password'] === $config['admin']['password']) {
        $authenticated = true;
        $_SESSION['admin_auth'] = true;
    } elseif (isset($_POST['logout'])) {
        session_destroy();
        header('Location: admin.php');
        exit;
    } else {
        $message = 'Hatalı şifre!';
    }
}

if (isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true) {
    $authenticated = true;
}

$results = $pollManager->getResults();
$stats = $pollManager->getVotingStats();
$ipLogs = [];

if (file_exists($config['paths']['data'] . $config['files']['ip_log'])) {
    $logs = file($config['paths']['data'] . $config['files']['ip_log'], FILE_IGNORE_NEW_LINES);
    foreach ($logs as $log) {
        if (empty($log)) continue;
        list($ip, $voteIndex, $timestamp) = explode('|', $log);
        $ipLogs[] = [
            'ip' => $ip,
            'vote_index' => $voteIndex,
            'timestamp' => date('Y-m-d H:i:s', $timestamp)
        ];
    }
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli - <?php echo htmlspecialchars($config['app']['name']); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
            line-height: 1.6;
        }

        .container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h1, h2, h3 {
            color: #333;
            margin-bottom: 20px;
        }

        .login-form {
            max-width: 400px;
            margin: 50px auto;
            text-align: center;
        }

        .login-form input[type="password"] {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 2px solid #e9ecef;
            border-radius: 5px;
            font-size: 16px;
        }

        .login-form button {
            width: 100%;
            padding: 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        .login-form button:hover {
            background: #0056b3;
        }

        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            border: 1px solid #e9ecef;
        }

        .stat-number {
            font-size: 3em;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #6c757d;
            font-size: 1.1em;
        }

        .results-section {
            margin-bottom: 30px;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .results-table th, .results-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .results-table th {
            background: #f8f9fa;
            font-weight: bold;
        }

        .result-bar {
            height: 20px;
            background: linear-gradient(90deg, #007bff, #0056b3);
            border-radius: 3px;
        }

        .logs-section {
            margin-bottom: 30px;
        }

        .logs-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .logs-table th, .logs-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        .logs-table th {
            background: #f8f9fa;
            font-weight: bold;
        }

        .action-buttons {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .action-button {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .action-button.primary {
            background: #007bff;
            color: white;
        }

        .action-button.danger {
            background: #dc3545;
            color: white;
        }

        .action-button:hover {
            opacity: 0.8;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #007bff;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!$authenticated): ?>
            <div class="login-form">
                <h2>Admin Paneli Girişi</h2>
                <?php if ($message): ?>
                    <div class="message"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <input type="password" name="password" placeholder="Şifre" required>
                    <button type="submit">Giriş Yap</button>
                </form>
            </div>
        <?php else: ?>

            <h1>Admin Paneli</h1>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['total_votes']; ?></div>
                    <div class="stat-label">Toplam Oy</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats['unique_ips']; ?></div>
                    <div class="stat-label">Benzersiz IP</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $results ? count($results['results']) : 0; ?></div>
                    <div class="stat-label">Seçenek Sayısı</div>
                </div>
            </div>

            <?php if ($results): ?>
                <div class="results-section">
                    <h2>Anket Sonuçları</h2>
                    <h3><?php echo htmlspecialchars($results['question']); ?></h3>

                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>Seçenek</th>
                                <th>Oy Sayısı</th>
                                <th>Yüzde</th>
                                <th>Grafik</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results['results'] as $result): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($result['option']); ?></td>
                                    <td><?php echo $result['votes']; ?></td>
                                    <td><?php echo $result['percentage']; ?>%</td>
                                    <td>
                                        <div class="result-bar" style="width: <?php echo min($result['percentage'], 100); ?>%;"></div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

            <div class="logs-section">
                <h2>IP Logları</h2>
                <table class="logs-table">
                    <thead>
                        <tr>
                            <th>IP Adresi</th>
                            <th>Oy Verilen Seçenek</th>
                            <th>Tarih/Saat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($ipLogs) as $log): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($log['ip']); ?></td>
                                <td><?php echo isset($results['results'][$log['vote_index']]) ? htmlspecialchars($results['results'][$log['vote_index']]['option']) : 'Bilinmiyor'; ?></td>
                                <td><?php echo htmlspecialchars($log['timestamp']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="action-buttons">
                <h3>Hızlı İşlemler</h3>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="logout" class="action-button danger">Çıkış Yap</button>
                </form>
                <a href="index.php" class="action-button primary">Ana Sayfaya Dön</a>
            </div>

        <?php endif; ?>
    </div>
</body>
</html>
