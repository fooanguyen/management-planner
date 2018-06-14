<?php
	/**
     * Project Planner
     * Project/Tasks/Edit.php
     * Edits the details of a specified task
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

	// Do not proceed if no IDs are specified
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

	// Prepare Task and Project IDs for SQL query
	$prid = mysqli_real_escape_string($conn, $_GET['prid']);
	$tid = mysqli_real_escape_string($conn, $_GET['tid']);
	$sql = "SELECT * FROM Tasks WHERE Task_ID = '$tid'";
	if ($result = mysqli_query($conn, $sql))
	{
		$count = mysqli_num_rows($result);
		$task = mysqli_fetch_array($result);
	}

	// If task detail input is set, proceed with editing
	if (isset($_POST['TaskSubmit']) && !empty($_POST))
	{
		$taskName = mysqli_real_escape_string($conn, $_POST['Name']);
		$taskHours = $_POST['Hours'];
		$taskCost = $_POST['Cost'];
		$description = mysqli_real_escape_string($conn, $_POST['Description']);

		$budgetUpdate = $taskCost - $task['Task_EstimatedCost'];
		$hoursUpdate = $taskHours - $task['Task_EstimatedHours'];
		
		// Update project budget and hours running totals with task cost and hours
		$sql = "UPDATE Projects SET Project_TotalHours = Project_TotalHours + '$hoursUpdate', 
					Project_RemainedBudget = Project_RemainedBudget - '$budgetUpdate' 
					WHERE Project_ID = '$prid'";
		mysqli_query($conn, $sql);

		// Edit task
		$sql = "UPDATE Tasks SET Task_Name = '$taskName', Task_Description = '$description', 
					Task_EstimatedHours = '$taskHours', Task_EstimatedCost = '$taskCost' 
					WHERE Task_ID='$tid'";
		mysqli_query($conn, $sql);

		mysqli_close($conn);
		header("Location: ./View.php?prid=" . $prid . "&tid=" . $tid);
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
				<a href="./View.php?prid=<?php echo $_GET['prid'] . '&tid=' . $_GET['tid'] ?>"><button class="w3-button w3-red" type="cancel" name="cancel">Cancel</button></a>
				<div class="w3-border w3-padding">
					<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . "?prid=" . $_GET['prid'] . "&tid=" . $_GET['tid']); ?>" autocomplete="off">				
						<?php
							/**
							 * Displays input fields with pre-filled task details
							 */
							if ($count == 1)
							{
								echo "<label>Task Name:</label> <input class='w3-input w3-border' "
										. "type='text' name='Name' value='" . $task['Task_Name'] 
										. "' required /></p>";
								echo "<label>Estimated Hours:</label> <input class='w3-input w3-border' "
										. "type='number' min='0' name='Hours' value='" 
										. $task['Task_EstimatedHours'] . "' required />";
								echo "<label>Estimated Cost $:</label> <input class='w3-input w3-border' "
										. "type='number' min='0' name='Cost' value='" 
										. $task['Task_EstimatedCost'] . "' required />";						
								echo "<label>Description:<label> <textarea class='w3-input w3-border' "
										. "rows='5' cols='50' maxlength='2000' placeholder='Type here' "
										. "name='Description' required>" . $task['Task_Description'] 
										. "</textarea>";
							}
							mysqli_close($conn);
						?>
						</br>
						<button class="w3-button w3-green" type="submit" name="TaskSubmit">Save</button>
					</form>
				</div>
			</div>
		</div>
		
	</body>
	<footer>
	</footer>
</html>