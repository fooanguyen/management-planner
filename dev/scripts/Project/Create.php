<?php
	/**
     * Project Planner
     * Project/Create.php
     * Creates a new project
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
		die('Unable to connect' . mysqli_connect_error());
	}

	// Get list of available clients
	$sql = "SELECT Client_CompanyName FROM Clients";
	$Result = mysqli_query($conn, $sql);
	$Client_CompanyName = [];
	
	// If any clients are in the table, add them to the array
	if (mysqli_num_rows($Result) > 0)
	{
		while ($row = mysqli_fetch_assoc($Result))
		{
			$Client_CompanyName[] = $row;
		}
	}

	// Proceed with project creation once input fields are filled
	if (isset($_POST['ProjectSubmit']) && !empty($_POST)) 
	{
		// Prepare project details for SQL query
		$Company_Name = $_POST['Client_CompanyName'];
		
		$sql2 = "SELECT Client_ID FROM Clients where Client_CompanyName = '$Company_Name'";
		$Result2 = mysqli_query($conn, $sql2);
		$Row2 = mysqli_fetch_array($Result2, MYSQLI_ASSOC);

		$Project_Name = mysqli_real_escape_string($conn, $_POST['Project_Name']);
		$Project_Status = $_POST['Project_Status'];			//Dead, On Hold, Completed, Requested, Approved, Rejected
		$Project_EstimatedBudget = $_POST['Project_EstimatedBudget'];
		$Project_RemainedBudget = $_POST['Project_EstimatedBudget'];
		$Project_TotalHours= $_POST['Project_TotalHours'];
		
		// Get the start date, or select today if no date specified
		$date = $_POST['Project_StartDate'];
		if ($date != '')
		{
			$newDate = date("Y-m-d", strtotime($date));
		}
		$Project_StartDate = $date;

		$Project_Description = mysqli_real_escape_string($conn, $_POST['Project_Description']);
		
		// Get client ID for SQL query
		if ($Company_Name == '')
		{
			$Client_ID_FK = -1;
		}
		else
		{
			$Client_ID_FK = $Row2['Client_ID'];
		}
			
		// Insert new project into the Projects table
		$sql3 = "INSERT INTO Projects (Client_ID_FK, Project_Name, Project_Description,
										Project_Status, Project_StartDate, Project_EstimatedBudget,
										Project_RemainedBudget)
										VALUES
										('$Client_ID_FK', '$Project_Name', '$Project_Description',
										'$Project_Status', '$Project_StartDate', '$Project_EstimatedBudget',
										'$Project_RemainedBudget')";
		mysqli_query($conn, $sql3);
		mysqli_close($conn);
		header("Location: ../home.php");
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset=utf-8 />
		<link href ="../style.css" rel="stylesheet">
		<script type="text/JavaScript">
			// AddClient()
			// Redirects to Add_Client.php
			function AddClient()
			{
				if (confirm("Your changes to the project will not be saved.  Continue?"))
				{
					window.location.href="./Add_Client.php";
				}
			}
		</script>
	</head>
	<body>
		<div class="w3-top w3-card w3-white" style="height:10%">
			<div class="w3-bar w3-padding">
				<a class="w3-bar-item" href="../home.php"><h1>Project Planner</h1></a>
				<div class="w3-right">
					<a class="w3-bar-item" href="../Users/View.php">Logged in as <?php echo $_SESSION['CURRENT_USER']->GetFirstName() . " " . $_SESSION['CURRENT_USER']->GetLastName() . " (" . $_SESSION['CURRENT_USER']->GetUsername() . ")";?></a>
					<a href="../logout.php"><button class="w3-bar-item w3-button w3-red">Sign Out</button></a>
				</div>
			</div>
		</div>
		<div class="w3-container" style="margin-top:10%">
			<div class="w3-container w3-display-middle" style="width:50%">
			<a href="../home.php"><button class="w3-button w3-red" name="cancel">Cancel</button></a>
				<div class="w3-border w3-padding">
					<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off">
						<label>Project Name:</label>
						<input class="w3-input w3-border" type="text" placeholder="Project Name" name="Project_Name" required></br>
							
						<label>Select Client</label>
						<select class="w3-select w3-border" name="Client_CompanyName">
							<option value="" disabled selected hidden>Select Client</option>
							<?php
								/**
								 * Displays each client name in a drop-down list
								 */
								foreach ($Client_CompanyName as $Client_CompanyName)
								{
									echo "<option value=" . $Client_CompanyName['Client_CompanyName'] . ">" 
											. $Client_CompanyName['Client_CompanyName'] . "</option>";
								}
							?>
						</select></br>
						<button class="w3-button w3-green" onclick="AddClient()">Add Client</button></br></br>
							
						<label>Project Status</label>
						<select class="w3-select w3-border" name="Project_Status" required>
							<option value="" disabled selected hidden>Project Status</option>	
								<option value="Requested">Requested</option>
								<option value="Approved">Approved</option>
								<option value="On Hold">On Hold</option>
								<option value="Rejected">Rejected</option>
								<option value="Dead">Dead</option>
								<option value="Completed">Completed</option>
						</select></br></br>
							
						<label>Estimated Budget:</label> 
						<input class="w3-input w3-border" type="number" min="0" placeholder="Enter Estimated Budget" name="Project_EstimatedBudget" required></br>
							
						<label>Start Date: </label>
						<input class="w3-input w3-border" type="date" placeholder="dd/mm/yyyy" name="Project_StartDate" required></br>
							
						<label>Project Description:</label></br>
						<textarea class="w3-input w3-border" rows="5" cols="50" maxlength="2000" placeholder="Type here" name="Project_Description" required></textarea>
						</br>
						<button class="w3-button w3-green" type="submit" name="ProjectSubmit">Submit</button>					
					</form>
				</div>
			</div>
		</div>
	</body>
</html>