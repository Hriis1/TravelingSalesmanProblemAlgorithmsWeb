<?php
$userIsLoggedIn = !empty($user);
$username = $userIsLoggedIn ? ($user["username"] ?? "User") : "";
?>

<header class="site-header">
    <div></div>

    <nav class="site-header-actions" aria-label="User navigation">
        <?php if ($userIsLoggedIn) { ?>
            <span class="site-header-user"><?= htmlspecialchars($username) ?></span>
            <button class="site-header-button" type="button" id="logOutButton">Log out</button>
        <?php } else { ?>
            <a class="site-header-button" href="login.php">Log in</a>
            <a class="site-header-button site-header-button-primary" href="register.php">Register</a>
        <?php } ?>
    </nav>
</header>