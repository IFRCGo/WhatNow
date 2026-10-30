#!/bin/sh
set -eu
php -r '
    if (PHP_VERSION_ID < 80400 || PHP_VERSION_ID >= 80500) {
        fwrite(STDERR, "Expected PHP 8.4.x\n"); exit(1);
    }
    foreach (["curl", "mbstring", "openssl", "pdo_mysql", "pdo_sqlite", "session", "sodium", "zip", "gd", "bcmath"] as $extension) {
        if (!extension_loaded($extension)) {
            fwrite(STDERR, "Missing extension: " . $extension . "\n"); exit(1);
        }
    }
'
apache2ctl -t
printf 'Verified PHP 8.4 runtime and required extensions.\n'
