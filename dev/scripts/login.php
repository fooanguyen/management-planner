<?php
	/**
     * Project Planner
     * login.php
     * Authenticates username and password, and establishes session
     * 
     * Team Hailstorm
     * ===============
     * Hemang Bhatt (hb6@umbc.edu)
     * Aidan Gray (graidan1@umbc.edu)
     * Cameron Hensel (chensel1@umbc.edu)
     * Jacob Lutz (jlutz1@umbc.edu)
     * Phuoc Nguyen (ej77536@umbc.edu)
     * Nirav Pancholi (nirav3@umbc.edu)
     * 
     */
	require_once(realpath(dirname(__FILE__)) . "/../classes/Session.class.php");
	require_once(realpath(dirname(__FILE__)) . "/../classes/User.class.php");
	Session::Start();

	// This page should be inaccessible if a user is logged in
	if(Session::UserLoggedIn())
	{
		header("Location: ./home.php");
	}

	// Proceed with login once the input fields are filled
	if(isset($_POST['submit']) && !empty($_POST)) 
	{
		$User_ID = $_POST['User_ID'];
		$User_Password = $_POST['User_Password'];
	
		// Create Session user and login
		$_SESSION['CURRENT_USER'] = new User();
		if ($_SESSION['CURRENT_USER']->Login($User_ID, $User_Password))
		{
			// If successful, proceed to home screen
			header("Location: ./home.php");
		}
		else
		{
			// If failed, display an error message
			echo "<script type='text/Javascript'>alert('Error: Username or Password invalid.');</script>";
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset=utf-8 />
		<link href ="./style.css" rel="stylesheet">
	</head>
	<body>
		<div class="w3-top w3-card">
			<div class="w3-bar w3-padding">
				<a class="w3-bar-item" ><h1>Project Planner</h1></a>
			</div>
		</div>
		<div class="w3-container">
			<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
				<div class="w3-panel w3-display-middle w3-border w3-center w3-padding">
			
					<label>User ID: </label>
					<input class="w3-input w3-border w3-center" type="text" placeholder="Enter ID" name="User_ID" required></br>
				
					<label>Password: </label>
					<input class="w3-input w3-border w3-center" type="password" placeholder="Enter Password" name="User_Password" required></br>
				
					</br>
					<button class="w3-button w3-green" type="submit" name="submit">Login</button>
					<a href="./Users/Create.php"><button class="w3-button w3-green" type="button">Create New User</button></a>
				</div>
			</form>
		</div>
	</body>
</html>
