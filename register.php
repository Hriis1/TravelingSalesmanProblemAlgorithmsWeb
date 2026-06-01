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
    <title>Register | TSP Algorithms</title>
    <link rel="stylesheet" href="css/auth.css">
</head>

<body>
    <main class="auth-page">
        <section class="auth-card" aria-label="Register form">
            <div class="auth-brand">
                <div class="auth-brand-mark" aria-hidden="true">TSP</div>
                <div>
                    <h1>Register</h1>
                </div>
            </div>

            <form class="auth-form" id="registerForm">
                <div class="auth-field">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" autocomplete="username" required>
                </div>

                <div class="auth-field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" autocomplete="new-password" required>
                </div>

                <div class="auth-field">
                    <label for="passwordConfirm">Confirm password</label>
                    <input type="password" id="passwordConfirm" name="password_confirm" autocomplete="new-password" required>
                </div>

                <p class="auth-message" id="registerMessage" aria-live="polite"></p>

                <button class="auth-button" type="submit" id="registerButton">Register</button>
            </form>

            <div class="auth-switch">
                <span>Already have an account?</span>
                <a class="auth-link-button" href="login.php">Log in</a>
            </div>
        </section>
    </main>

    <?php require_once __DIR__ . "/scripts.php"; ?>

    <script>
        $(function () {
            const $registerForm = $('#registerForm');
            const $registerButton = $('#registerButton');
            const $registerMessage = $('#registerMessage');
            const $username = $('#username');
            const $password = $('#password');
            const $passwordConfirm = $('#passwordConfirm');

            //Show the current form message
            function setRegisterMessage(message, type = 'error') {
                $registerMessage
                    .removeClass('success error')
                    .addClass(type)
                    .text(message);
            }

            //Post register data through userRouter
            $registerForm.on('submit', function (event) {
                event.preventDefault();

                const username = $username.val().trim();
                const password = $password.val();
                const passwordConfirm = $passwordConfirm.val();

                if (!username || !password || !passwordConfirm) {
                    setRegisterMessage('Enter username and passwords');
                    return;
                }

                $registerButton.prop('disabled', true).text('Registering...');
                setRegisterMessage('');

                $.ajax({
                    url: 'backend/users/userRouter.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'registerUser',
                        username: username,
                        password: password,
                        password_confirm: passwordConfirm
                    },
                    success: function (result) {
                        if (result.success == true) {
                            setRegisterMessage('Registration successful', 'success');
                            window.location.href = 'index.php';
                            return;
                        }

                        console.log(result);

                        setRegisterMessage(result.error || 'Could not register');
                    },
                    error: function () {
                        setRegisterMessage('Could not register');
                    },
                    complete: function () {
                        $registerButton.prop('disabled', false).text('Register');
                    }
                });
            });
        });
    </script>
</body>

</html>
