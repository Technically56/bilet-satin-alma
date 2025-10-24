<?php
session_start([
    'cookie_path' => '/',
    'cookie_lifetime' => 3600,
    'cookie_secure' => false,
    'cookie_httponly' => true,
    'cookie_samesite' => 'lax',
]);

echo "<h2>Session Test</h2>";

// Set a test variable
if (!isset($_SESSION['test_counter'])) {
    $_SESSION['test_counter'] = 0;
}
$_SESSION['test_counter']++;
$_SESSION['last_access'] = date('Y-m-d H:i:s');

// Display session info
echo "<b>Session ID:</b> " . session_id() . "<br>";
echo "<b>Session Counter:</b> " . $_SESSION['test_counter'] . "<br>";
echo "<b>Last Access:</b> " . $_SESSION['last_access'] . "<br><br>";

echo "<b>All Session Data:</b><br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<br><a href='test-session.php'>Refresh Page</a> | ";
echo "<a href='test-session2.php'>Go to Page 2</a> | ";
echo "<a href='test-session-destroy.php'>Destroy Session</a>";
?>