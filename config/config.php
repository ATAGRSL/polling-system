<?php

return [
    'app' => [
        'name' => 'Akıllı Anket Sistemi',
        'version' => '1.0.0',
        'timezone' => 'Europe/Istanbul',
        'charset' => 'UTF-8'
    ],

    'paths' => [
        'data' => __DIR__ . '/../data/',
        'logs' => __DIR__ . '/../logs/',
        'public' => __DIR__ . '/../public/',
        'src' => __DIR__ . '/../src/'
    ],

    'files' => [
        'poll' => 'anket.txt',
        'votes' => 'oylar.txt',
        'ip_log' => 'ip_log.txt',
        'system_log' => 'system.log'
    ],

    'voting' => [
        'prevent_duplicate_ip' => true,
        'ip_timeout_hours' => 24,
        'max_votes_per_ip' => 1
    ],

    'display' => [
        'show_results_after_vote' => true,
        'show_vote_count' => true,
        'show_percentage' => true
    ],

    'security' => [
        'enable_csrf_protection' => true,
        'enable_xss_filter' => true,
        'log_suspicious_activity' => true
    ],

    'admin' => [
        'password' => 'admin123',
        'enable_admin_panel' => true
    ]
];
