<?php
	/**
     * Project Planner
     * Project/Phases/Create.php
     * Creates a new phase under the specified project
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
	require_once(realpath(dirname(__FILE__)) . "/../../../classes/Session.class.php");
	require_once(realpath(dirname(__FILE__)) . "/../../../classes/User.class.php");
	Session::Start();

	// This page should be inaccessible if a user is not logged in
	if (!Session::UserLoggedIn())
	{
		header("Location: ../../login.php");
	}
	
	// Do not proceed if no project is specified
	if (!isset($_GET))
	{
		header("Location: ../../home.php");
	}
	
	// Attempt DB connection
	$conn = mysqli_connect($_SESSION["SERVER"], $_SESSION["DBUSER"], $_SESSION["DBPASS"], 
							$_SESSION["DATABASE"]);
	if (!$conn)
	{
		// Output error details
		die('Unable to connect' . mysqli_connect_error());
	}

	// Create the phase only once the input fields are filled
	if (isset($_POST['PhaseSubmit']) && !empty($_POST))
	{
		// prepare input for SQL query
		$phaseName = mysqli_real_escape_string($conn, $_POST['Name']);
		$description = mysqli_real_escape_string($conn, $_POST['Description']);
		$creator = $_SESSION['CURRENT_USER']->getUserID();
		$project = mysqli_real_escape_string($conn, $_GET['prid']);
		
		$sql = "INSERT INTO Phases (User_ID_FK, Project_ID_FK, Phase_Name, Phase_Description) 
									VALUES
									('$creator', '$project', '$phaseName', 
									'$description')";
		mysqli_query($conn, $sql);
		mysqli_close($conn);
		header("Location: ../View.php?proj=" . $project);
	}
?>


<!DOCTYPE html>
<html>
	<head>
		<meta charset=utf-8 />
		<link href ="../../style.css" rel="stylesheet">
	</head>
	<body>
		<div class="w3-top w3-card w3-white" style="height:10%">
			<div class="w3-bar w3-padding">
				<a class="w3-bar-item" href="../../home.php"><h1>Project Planner</h1></a>
				<div class="w3-right">
					<a class="w3-bar-item" href="../../Users/View.php">Logged in as <?php echo $_SESSION['CURRENT_USER']->GetFirstName() . " " . $_SESSION['CURRENT_USER']->GetLastName() . " (" . $_SESSION['CURRENT_USER']->GetUsername() . ")";?></a>
					<a href="../../logout.php"><button class="w3-bar-item w3-button w3-red">Sign Out</button></a>
				</div>
			</div>
		</div>
		<div class="w3-container" style="margin-top:10%">
			<div class="w3-container w3-display-middle" style="width:50%">
				<a href="<?php echo '../View.php?proj=' . $_GET['prid'] ?>"><button class="w3-button w3-red" type="cancel" name="cancel">Cancel</button></a>
				<div class="w3-border w3-padding">
					<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . "?prid=" . $_GET['prid']); ?>" autocomplete="off">					
						<label>Name:</label>
						<input class="w3-input w3-border" type="text" name="Name" required></br>
					
						<label>Description:</label>
						<textarea class="w3-input w3-border" rows="5" cols="50" maxlength="2000" placeholder="Type here" name="Description" required></textarea>
						</br>

						<p>Users can be assigned by editing this phase once it has been created.</p>

						<button class="w3-button w3-green" type="submit" name="PhaseSubmit">Save</button>
					</form>
				</div>
			</div>
		</div>		
	</body>
</html>