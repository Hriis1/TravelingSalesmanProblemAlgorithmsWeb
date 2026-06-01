<?php
$timeout = 30 * 60; // 30 minutes

// ————— START SESSION IF NEEDED —————
if (session_status() === PHP_SESSION_NONE) {
    // session_set_cookie_params([
    //     'lifetime' => 0,
    //     'path'     => '/',
    //     'domain'   => '',
    //     'secure'   => true,
    //     'httponly' => true,
    //     'samesite' => 'Lax'
    // ]);
    session_start();
}

// ————— EXPIRE AFTER INACTIVITY —————
if (isset($_SESSION['LAST_ACTIVITY'])) {
    $inactive = time() - $_SESSION['LAST_ACTIVITY'];
    if ($inactive > $timeout) {
        // too long — destroy and start a brand new session
        session_unset();     // clear $_SESSION
        session_destroy();   // kill the session
        session_start();     // start a new one
    }
}

// ————— SLIDE WINDOW —————
// update last‑activity so the timeout is renewed on each request
$_SESSION['LAST_ACTIVITY'] = time();

//periodically rotate session ID for extra security
if (!isset($_SESSION['CREATED']) || time() - $_SESSION['CREATED'] > 1800) {
    session_regenerate_id(true);    // replace the session id
    $_SESSION['CREATED'] = time();  // update creation time
}
