<?php
	session_start();

	$name = isset($_SESSION['name']) ? $_SESSION['name'] : null;

	function canLogin($p_email, $p_password){ 
		$conn = new mysqli('junction.proxy.rlwy.net', 'root', 'JoTRKOPYmfOIxHylrywjlCkBrYGpOWvB', 'railway', 11795);
		$statement = $conn->prepare('SELECT * FROM users WHERE email = ?');
		$statement->bind_param('s', $p_email);
		$statement->execute();
		$result = $statement->get_result();

		$user = $result->fetch_assoc();

		if (!$user) {
			return false; // No user found with that email
		}
		
		$hash = $user['password'];
		if(password_verify($p_password, $hash)){
			return $user['id']; // Return user ID on successful login
		} else {
			return false;
		}
	}

	if(!empty($_POST)){
		$email = $_POST['email'];
		$password = $_POST['password'];
		$result = canLogin($email, $password);

		if($result){
			$_SESSION['loggedin'] = true;
			$_SESSION['email'] = $email;
			$_SESSION['name'] = $name;
			$_SESSION['user_id'] = $result; // Store user ID in session

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
	<title>Login - Pageturners</title>
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
				<span class="name"><?php echo htmlspecialchars($name); ?></span>
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
</body>
</html>
