<?php
try {
    $pdo = new PDO(
        'mysql:host=gateway01.ap-southeast-1.prod.alicloud.tidbcloud.com;port=4000',
        '4LoKbmvXKW4TnhY.root',
        'PT56SDscmOnwxjdK',
        [
            PDO::MYSQL_ATTR_SSL_CA => __DIR__ . '/cacert.pem',
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
        ]
    );
    $pdo->exec('CREATE DATABASE IF NOT EXISTS reoda');
    echo "Success\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
