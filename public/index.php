<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/PollManager.php';

$config = require __DIR__ . '/../config/config.php';
$pollManager = new PollManager($config);

$ip = $_SERVER['REMOTE_ADDR'];
$message = '';
$showResults = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote'])) {
    $optionIndex = (int)$_POST['vote'];
    $result = $pollManager->castVote($ip, $optionIndex);
    $message = $result['message'];
    $showResults = $result['success'];
}

$pollData = $pollManager->getPollData();
$results = $pollManager->getResults();

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($config['app']['name']); ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
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

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
            font-size: 2em;
        }

        .poll-question {
            font-size: 1.5em;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
            background: #ecf0f1;
            padding: 15px;
            border-radius: 5px;
        }

        .poll-options {
            list-style: none;
            padding: 0;
        }

        .poll-option {
            margin: 10px 0;
        }

        .poll-option label {
            display: block;
            padding: 15px;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .poll-option label:hover {
            background: #e9ecef;
            border-color: #007bff;
        }

        .poll-option input[type="radio"] {
            margin-right: 10px;
            transform: scale(1.2);
        }

        .vote-button {
            display: block;
            width: 100%;
            padding: 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s ease;
        }

        .vote-button:hover {
            background: #0056b3;
        }

        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .results {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .result-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .result-bar {
            height: 20px;
            background: #007bff;
            border-radius: 3px;
            margin: 5px 0;
            transition: width 0.5s ease;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
            padding: 20px;
            background: #e9ecef;
            border-radius: 5px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9em;
        }

        .admin-link {
            text-align: center;
            margin-top: 30px;
        }

        .admin-link a {
            color: #6c757d;
            text-decoration: none;
        }

        .admin-link a:hover {
            text-decoration: underline;
        }

        .already-voted {
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($config['app']['name']); ?></h1>

        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'başarıyla') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($pollData): ?>
            <div class="poll-question">
                <?php echo htmlspecialchars($pollData['question']); ?>
            </div>

            <?php if (!$showResults && !$pollManager->hasUserVoted($ip)): ?>
                <form method="POST" action="">
                    <ul class="poll-options">
                        <?php foreach ($pollData['options'] as $index => $option): ?>
                            <li class="poll-option">
                                <label>
                                    <input type="radio" name="vote" value="<?php echo $index; ?>" required>
                                    <?php echo htmlspecialchars($option); ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="submit" class="vote-button">Oy Ver</button>
                </form>
            <?php else: ?>
                <?php if ($pollManager->hasUserVoted($ip)): ?>
                    <div class="already-voted">
                        Bu IP adresinden daha önce oy kullanılmış.
                    </div>
                <?php endif; ?>

                <?php if ($results && ($showResults || $config['display']['show_results_after_vote'])): ?>
                    <div class="results">
                        <h3>Sonuçlar:</h3>
                        <?php foreach ($results['results'] as $result): ?>
                            <div class="result-item">
                                <div>
                                    <strong><?php echo htmlspecialchars($result['option']); ?></strong>
                                    <?php if ($config['display']['show_vote_count']): ?>
                                        <br><small><?php echo $result['votes']; ?> oy</small>
                                    <?php endif; ?>
                                </div>
                                <?php if ($config['display']['show_percentage']): ?>
                                    <div style="text-align: right;">
                                        <div><?php echo $result['percentage']; ?>%</div>
                                        <div class="result-bar" style="width: <?php echo $result['percentage']; ?>%;"></div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($results['total_votes'] > 0): ?>
                        <div class="stats">
                            <?php $stats = $pollManager->getVotingStats(); ?>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $results['total_votes']; ?></div>
                                <div class="stat-label">Toplam Oy</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-number"><?php echo $stats['unique_ips']; ?></div>
                                <div class="stat-label">Benzersiz IP</div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>

        <?php else: ?>
            <div class="message error">
                Henüz aktif bir anket bulunmuyor.
            </div>
        <?php endif; ?>

        <div class="admin-link">
            <a href="admin.php">Admin Paneli</a>
        </div>
    </div>
</body>
</html>
