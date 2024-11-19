<?php

	$name = isset($_SESSION['name']) ? $_SESSION['name'] : null;

	function canLogin($p_email, $p_password){ 
		$conn = new PDO('mysql:host=localhost;dbname=bookstore', 'root', '');
		$statement = $conn->prepare('SELECT * FROM users WHERE email = :email');
		$statement->bindValue(':email', $p_email);
		$statement->execute();

		$user = $statement->fetch(PDO::FETCH_ASSOC);

		if (!$user) {
			return false; // No user found with that email
		}
		
		if($user){
			$hash = $user['password'];
			if(password_verify($p_password, $hash)){
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	if(!empty($_POST)){
		$email = $_POST['email'];
		$password = $_POST['password'];
		$result = canLogin($email, $password);

		if($result){
			session_start();
			$_SESSION['loggedin'] = true;
			$_SESSION['email'] = $email;
			$_SESSION['name'] = $name;

			header('Location: index.php');
			exit();
		} else {
			$error = true;
		}
	}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Online Bookstore</title>
	<link rel="stylesheet" href="./css/navbar.css">
    <link rel="stylesheet" href="./css/login.css">
</head>
<body>
	<nav class="navbar">
		<div class="logo">
			<img src="./images/logo.png" alt="Logo" />
		</div>
		<div class="nav-right">
			<?php if ($name): ?>
				<span class="name"><?php echo htmlspecialchars($username); ?></span>
			<?php else: ?>
				<a href="signup.php" class="login-link">create an account</a>
			<?php endif; ?>
		</div>
	</nav>

    <div class="login-container">
        <h2>Login</h2>
        <form class="login-form" action="" method="POST">
            <?php if(isset($error)): ?>
				<div class="form__error">
					<p>
						Sorry, we can't log you in with that email and password. Can you try again?
					</p>
				</div>
			<?php endif; ?>
            <div class="input-field">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-field">
                <label for="password">Wachtwoord:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn login-btn">Login</button>
        </form>
        <p>Nog geen account? <a href="signup.php">Sign Up</a></p>
    </div>

	//test
</body>
</html>
