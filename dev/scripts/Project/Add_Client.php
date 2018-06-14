<?php
	/**
     * Project Planner
     * Project/Add_Client.php
     * Creates a client to assign to projects
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
	require_once(realpath(dirname(__FILE__)) . "/../../classes/Session.class.php");
	require_once(realpath(dirname(__FILE__)) . "/../../classes/User.class.php");
	Session::Start();

	// This page should be inaccessible if a user is not logged in
	if (!Session::UserLoggedIn())
	{
		header("Location: ../login.php");
	}

	// Attempt DB connection
	$conn = mysqli_connect($_SESSION["SERVER"], $_SESSION["DBUSER"], $_SESSION["DBPASS"], 
							$_SESSION["DATABASE"]);
	if (!$conn)
	{
		// Output error details
		die('Unable to connect.  Error: ' . mysqli_error($conn));
	}

	// Proceed with client creation once input fields are filled
	if (isset($_POST['ClientSubmit']) && !empty($_POST)) 
	{
		// Prepare client details for SQL query
		$Client_CompanyName = mysqli_real_escape_string($conn, $_POST['Client_CompanyName']);
		$Client_Firstname = mysqli_real_escape_string($conn, $_POST['Client_Firstname']);
		$Client_Lastname = mysqli_real_escape_string($conn, $_POST['Client_Lastname']);
		$Client_Industry = mysqli_real_escape_string($conn, $_POST['Client_Industry']);
		$Client_Email = mysqli_real_escape_string($conn, $_POST['Client_Email']);
		$Client_Phone = mysqli_real_escape_string($conn, $_POST['Client_Phone']);
		$Client_Street = mysqli_real_escape_string($conn, $_POST['Client_Street']);
		$Client_City = mysqli_real_escape_string($conn, $_POST['Client_City']);
		$Client_State = mysqli_real_escape_string($conn, $_POST['Client_State']);
		$Client_Zipcode = $_POST['Client_Zipcode'];
		$Client_Country = mysqli_real_escape_string($conn, $_POST['Client_Country']);
		
		
		$sql = "INSERT INTO Clients(Client_CompanyName, Client_Firstname, Client_Lastname, 
									Client_Industry, Client_Email, Client_Phone, 
									Client_Street, Client_City, Client_State, 
									Client_Zipcode, Client_Country) 
									VALUES 
									('$Client_CompanyName', '$Client_Firstname', '$Client_Lastname', 
									'$Client_Industry', '$Client_Email', '$Client_Phone', 
									'$Client_Street', '$Client_City', '$Client_State', 
									'$Client_Zipcode', '$Client_Country') ";	
		mysqli_query($conn, $sql);

		// If a return page is specified, go there, else go to the project creation page
		if (isset($_GET['ret']))
		{
			header("Location: ./Edit.php?proj=" . $_GET['ret']);
		}
		else
		{
			header("Location: ./Create.php");
		}
	}
	mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset=utf-8 />
		<link href ="../style.css" rel="stylesheet">
	</head>
	<body>
		<div class="w3-top w3-card w3-white" style="height:10%">
			<div class="w3-bar w3-padding">
				<a class="w3-bar-item"><h1>Project Planner</h1></a>
				<div class="w3-right">
					<a class="w3-bar-item" href="../Users/View.php">Logged in as <?php echo $_SESSION['CURRENT_USER']->GetUsername();?></a>
					<a href="../logout.php"><button class="w3-bar-item w3-button w3-red">Sign Out</button></a>
				</div>
			</div>
		</div>
		<div class="w3-container" style="top:80%">
			<div class="w3-container w3-display-topmiddle" style="width:50%;top:12%">
				<a href="<?php echo (isset($_GET['ret']) ? "./Edit.php?proj=" . $_GET['ret'] : "javascript:history.back()"); ?>"><button class="w3-button w3-red" name="cancel">Cancel</button></a>
				<div class="w3-border w3-padding">
					<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . (isset($_GET['ret']) ? "?ret=" . $_GET['ret'] : ""); ?>" autocomplete="off">
			
						<label>Company Name:</label>
						<input class="w3-input w3-border" type="text" placeholder="Company Name" name="Client_CompanyName" required></br>
				
						<label>Contact Firstname: </label>
						<input class="w3-input w3-border" type="text" placeholder="Firstname" name="Client_Firstname" required></br>
				
						<label>Contact Lastname: </label>
						<input class="w3-input w3-border" type="text"  placeholder="Lastname" name="Client_Lastname" required></br>
				
						<label>Contact Email: </label>
						<input class="w3-input w3-border" type="text"  placeholder="Email" name="Client_Email" required></br>
								
						<label>Phone: </label>
						<input class="w3-input w3-border" type="tel" maxlength="10" placeholder="Phone Number" name="Client_Phone" required></br>
				
						<label>Specialized Industry/Field: </label>
						<input class="w3-input w3-border" type="text"  placeholder="Industry" name="Client_Industry" required></br>
				
						<label>Street Address: </label>
						<input class="w3-input w3-border" type="text"  placeholder="Street Address" name="Client_Street" required></br>
				
						<label>City: </label>
						<input class="w3-input w3-border" type="text" placeholder="City" name="Client_City" required></br>
				
						<label>State: </label>
						<input class="w3-input w3-border" type="text"  maxlength="2" placeholder="State" name="Client_State" required></br>
				
						<label>Zipcode: </label>
						<input class="w3-input w3-border" type="number" min="0" maxlength="5" placeholder="Zipcode" name="Client_Zipcode" required></br>
				
						<label>Country: </label>
						<input class="w3-input w3-border" type="text"  placeholder="Country" name="Client_Country" required></br>
				
						</br>
						<button class="w3-button w3-green" type="submit" name="ClientSubmit">Submit</button>
					</form>
				</div>
			</br>
			</div>
		</div>
	</body>
</html>