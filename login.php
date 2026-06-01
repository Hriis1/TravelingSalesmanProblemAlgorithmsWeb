<?php
require_once __DIR__ . "/backend/componenets/includes.php";

$userId = (int) ($_SESSION["user_id"] ?? 0);

if ($userId > 0) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in | TSP Algorithms</title>
    <link rel="stylesheet" href="css/auth.css">
</head>

<body>
    <main class="auth-page">
        <section class="auth-card" aria-label="Log in form">
            <div class="auth-brand">
                <div class="auth-brand-mark" aria-hidden="true">TSP</div>
                <div>
                    <h1>Log in</h1>
                </div>
            </div>

            <form class="auth-form" id="loginForm">
                <div class="auth-field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" autocomplete="username" required>
                </div>

                <div class="auth-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" autocomplete="current-password" required>
                </div>

                <p class="auth-message" id="loginMessage" aria-live="polite"></p>

                <button class="auth-button" type="submit" id="loginButton">Log in</button>
            </form>
        </section>
    </main>

    <?php require_once __DIR__ . "/scripts.php"; ?>

    <script>
        $(function () {
            const $loginForm = $('#loginForm');
            const $loginButton = $('#loginButton');
            const $loginMessage = $('#loginMessage');
            const $username = $('#username');
            const $password = $('#password');

            //Show the current form message
            function setLoginMessage(message, type = 'error') {
                $loginMessage
                    .removeClass('success error')
                    .addClass(type)
                    .text(message);
            }

            //Post login data through userRouter
            $loginForm.on('submit', function (event) {
                event.preventDefault();

                const username = $username.val().trim();
                const password = $password.val();

                if (!username || !password) {
                    setLoginMessage('Enter username and password');
                    return;
                }

                $loginButton.prop('disabled', true).text('Logging in...');
                setLoginMessage('');

                $.ajax({
                    url: 'backend/users/userRouter.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'logInUser',
                        username: username,
                        password: password
                    },
                    success: function (result) {
                        if (result.success == true) {
                            setLoginMessage('Login successful', 'success');
                            window.location.href = 'index.php';
                            return;
                        }

                        setLoginMessage(result.error || 'Could not log in');
                    },
                    error: function () {
                        setLoginMessage('Could not log in');
                    },
                    complete: function () {
                        $loginButton.prop('disabled', false).text('Log in');
                    }
                });
            });
        });
    </script>
</body>

</html>
