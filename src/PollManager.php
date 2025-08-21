<?php

class PollManager
{
    private $config;
    private $dataPath;
    private $pollFile;
    private $votesFile;
    private $ipLogFile;
    private $logFile;

    public function __construct($config)
    {
        $this->config = $config;
        $this->dataPath = $config['paths']['data'];
        $this->pollFile = $this->dataPath . $config['files']['poll'];
        $this->votesFile = $this->dataPath . $config['files']['votes'];
        $this->ipLogFile = $this->dataPath . $config['files']['ip_log'];
        $this->logFile = $config['paths']['logs'] . $config['files']['system_log'];
    }

    public function getPollData()
    {
        if (!file_exists($this->pollFile)) {
            return null;
        }

        $lines = file($this->pollFile, FILE_IGNORE_NEW_LINES);
        if (empty($lines)) {
            return null;
        }

        $question = array_shift($lines);
        $options = array_filter($lines);

        return [
            'question' => $question,
            'options' => array_values($options)
        ];
    }

    public function getVoteCounts()
    {
        if (!file_exists($this->votesFile)) {
            return [];
        }

        $votes = file($this->votesFile, FILE_IGNORE_NEW_LINES);
        return array_map('intval', array_filter($votes));
    }

    public function hasUserVoted($ip)
    {
        if (!$this->config['voting']['prevent_duplicate_ip']) {
            return false;
        }

        if (!file_exists($this->ipLogFile)) {
            return false;
        }

        $logs = file($this->ipLogFile, FILE_IGNORE_NEW_LINES);
        $currentTime = time();
        $timeoutHours = $this->config['voting']['ip_timeout_hours'];
        $timeoutSeconds = $timeoutHours * 3600;

        foreach ($logs as $log) {
            if (empty($log)) continue;

            list($loggedIp, $voteIndex, $timestamp) = explode('|', $log);

            if ($loggedIp === $ip && ($currentTime - $timestamp) < $timeoutSeconds) {
                return true;
            }
        }

        return false;
    }

    public function castVote($ip, $optionIndex)
    {
        if ($this->hasUserVoted($ip)) {
            return ['success' => false, 'message' => 'Bu IP adresinden daha önce oy kullanılmış.'];
        }

        $pollData = $this->getPollData();
        if (!$pollData || !isset($pollData['options'][$optionIndex])) {
            return ['success' => false, 'message' => 'Geçersiz seçenek.'];
        }

        $votes = $this->getVoteCounts();

        if (!isset($votes[$optionIndex])) {
            for ($i = count($votes); $i <= $optionIndex; $i++) {
                $votes[$i] = 0;
            }
        }

        $votes[$optionIndex]++;

        $this->saveVotes($votes);
        $this->logVote($ip, $optionIndex);

        return ['success' => true, 'message' => 'Oyunuz başarıyla kaydedildi.'];
    }

    private function saveVotes($votes)
    {
        $content = implode("\n", $votes);
        file_put_contents($this->votesFile, $content);
    }

    private function logVote($ip, $optionIndex)
    {
        $timestamp = time();
        $logEntry = $ip . '|' . $optionIndex . '|' . $timestamp . "\n";
        file_put_contents($this->ipLogFile, $logEntry, FILE_APPEND);
    }

    public function getResults()
    {
        $pollData = $this->getPollData();
        $votes = $this->getVoteCounts();

        if (!$pollData) {
            return null;
        }

        $totalVotes = array_sum($votes);
        $results = [];

        foreach ($pollData['options'] as $index => $option) {
            $voteCount = isset($votes[$index]) ? $votes[$index] : 0;
            $percentage = $totalVotes > 0 ? round(($voteCount / $totalVotes) * 100, 2) : 0;

            $results[] = [
                'option' => $option,
                'votes' => $voteCount,
                'percentage' => $percentage
            ];
        }

        return [
            'question' => $pollData['question'],
            'total_votes' => $totalVotes,
            'results' => $results
        ];
    }

    public function getVotingStats()
    {
        if (!file_exists($this->ipLogFile)) {
            return ['total_votes' => 0, 'unique_ips' => 0];
        }

        $logs = file($this->ipLogFile, FILE_IGNORE_NEW_LINES);
        $logs = array_filter($logs);

        $uniqueIps = [];
        foreach ($logs as $log) {
            if (empty($log)) continue;
            list($ip) = explode('|', $log);
            $uniqueIps[$ip] = true;
        }

        return [
            'total_votes' => count($logs),
            'unique_ips' => count($uniqueIps)
        ];
    }

    public function logActivity($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] $message\n";
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }
}
