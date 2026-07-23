<?php
/**
 * Database connection for MealConnect.
 * Default XAMPP MySQL credentials: user root, empty password.
 * Change these if your XAMPP setup is different.
 */
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'mealconnect';

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (!$conn) {
    die('<div style="font-family:sans-serif;padding:40px;max-width:700px;margin:60px auto;background:#fff5f0;border:1px solid #ffb199;border-radius:12px">
        <h2 style="color:#c1401f">MealConnect can\'t connect to the database</h2>
        <p>Please make sure:</p>
        <ol>
            <li>XAMPP\'s <b>Apache</b> and <b>MySQL</b> modules are running.</li>
            <li>You have imported <code>database/schema.sql</code> in phpMyAdmin.</li>
            <li>The database name is <code>mealconnect</code>.</li>
        </ol>
        <p style="color:#888">Technical detail: ' . mysqli_connect_error() . '</p>
    </div>');
}

mysqli_set_charset($conn, 'utf8mb4');

// Session must start before any output on every page that includes this file
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
