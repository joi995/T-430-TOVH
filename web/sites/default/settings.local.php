<?php
// Set error logging to log every error
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
$config['system.logging']['error_level'] = 'verbose';

// Disable CSS and JS preprocessing
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;

// Config sync directory outside web root for Git safety
$settings['config_sync_directory'] = $app_root . '/../config/sync';

// Trusted host patterns for local environment
$settings['trusted_host_patterns'] = [
    '^.+\.ddev\.site$'
];

// Environment indicator for local development
$config['environment_indicator.indicator']['name'] = 'local';
$config['environment_indicator.indicator']['fg_color'] = '#ffffff';
$config['environment_indicator.indicator']['bg_color'] = '#000000';
