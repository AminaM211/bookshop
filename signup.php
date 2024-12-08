<?php
    $name = isset($_SESSION['name']) ? $_SESSION['name'] : null;

    include_once './classes/db.php';
    
    // Maak databaseverbinding
    $db = new Database();
    $conn = $db->connect();
    
    // Controleer of er POST-data is verzonden
    if(!empty($_POST)){
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Controleer of het e-mailadres al bestaat
        $checkEmail = $conn->prepare('SELECT COUNT(*) FROM users WHERE email = ?');
        $checkEmail->bind_param('s', $email);
        $checkEmail->execute();
        $checkEmail->bind_result($emailExists);
        $checkEmail->fetch();
        $checkEmail->close();

        if ($emailExists) {
            // Toon een foutmelding als het e-mailadres al bestaat
            $error = "Dit e-mailadres is al in gebruik.";
        } else {
            // Als het e-mailadres niet bestaat, voeg dan de gebruiker toe
            $options = [
                'cost' => 12,
            ];
        
            $hash = password_hash($password, PASSWORD_DEFAULT, $options); // Hash het wachtwoord

            // Voeg de gebruiker toe aan de database
            $statement = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            $statement->bind_param('sss', $name, $email, $hash); // Beveiligd voor SQL injectie
            $statement->execute();

            // Redirect naar de login pagina na succesvolle registratie
            header("Location: login.php");
            exit;
        }
    }

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Pageturners</title>
    <link rel="icon" type="image/x-icon" href="./images/favicon.ico">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/signup.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <img src="./images/logo.png" alt="Logo" />
        </div>
        <div class="nav-right">
            <a href="login.php" class="login-link">Log in</a>
        </div>
    </nav>

    <div class="signup-container">
        <h2>Sign Up</h2>
        <form class="signup-form" action="signup.php" method="POST">
            <?php if(isset($error) ): ?>
				<div class="form__error">
					<p>
						Sorry, we can't log you in with that email address and password. Can you try again?
					</p>
				</div>
			<?php endif; ?>
            <div class="input-field">
                <label for="name">Naam:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="input-field">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-field">
                <label for="password">Wachtwoord:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-field">
                <label for="confirm-password">Bevestig Wachtwoord:</label>
                <input type="password" id="confirm-password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn signup-btn">Maak Account</button>
        </form>
        <p>Heb je al een account? <a href="login.php">Log In</a></p>
    </div>
</body>
</html>
