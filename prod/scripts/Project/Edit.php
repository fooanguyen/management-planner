<?php
	/**
     * Project Planner
     * ProjectEdit.php
     * Edits the details of a specified project
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

	// Do not proceed if no project ID is specified
	if (empty($_GET))
	{
		header("Location: ../home.php");
	}

	// Attempt DB connection
	$conn = mysqli_connect($_SESSION["SERVER"], $_SESSION["DBUSER"], $_SESSION["DBPASS"], 
							$_SESSION["DATABASE"]);
	if (!$conn)
	{
		// Output error details
		die('Unable to connect.  Error: ' . mysqli_error($conn));
	}

    // If any clients are in the table, add them to the array
    $sql = "SELECT Client_CompanyName FROM Clients";
	$Result = mysqli_query($conn, $sql);
	$comps = [];
	
	if (mysqli_num_rows($Result) > 0)
	{
		while ($row = mysqli_fetch_assoc($Result))
		{
			$comps[] = $row;
		}
	}

	// Retrieve details on the specified project
	$proj = mysqli_real_escape_string($conn, $_GET['proj']);
	$sql = "SELECT * FROM Projects WHERE Project_ID = '$proj'";
	if ($result = mysqli_query($conn, $sql))
	{
		$project = mysqli_fetch_array($result);
	}
	
	// Proceed with project editing once input fields are filled
    if (isset($_POST['ProjectSubmit']) && !empty($_POST))
    {
		// Prepare project details for SQL query
		$Company_Name = $_POST['Client_CompanyName'];
        $sql2 = "SELECT Client_ID FROM Clients where Client_CompanyName = '$Company_Name'";
		$Result2 = mysqli_query($conn, $sql2);
        $Row2 = mysqli_fetch_array($Result2, MYSQLI_ASSOC);
        
        $projectName = mysqli_real_escape_string($conn, $_POST['Name']);
        $projectStatus = $_POST['Project_Status'];
		$projectBudget = $_POST['Budget'];
		
		$budgetUpdate = $project['Project_RemainedBudget'] + ($projectBudget - $project['Project_EstimatedBudget']);
		
		// Get the start date, or select today if no date specified
		$date = $_POST['StartDate'];
		if ($date != '')
		{
			$newDate = date("Y-m-d", strtotime($date));
		}
		$projectStartDate = $date;

        $projectDescription = mysqli_real_escape_string($conn, $_POST['Project_Description']);
		
		// Get client ID for SQL query
		if ($Company_Name == '')
		{
			$clientID = -1;
		}
		else
		{
			$clientID = $Row2['Client_ID'];
		}
		
		// Update project details
		$sql = "UPDATE Projects SET Client_ID_FK = '$clientID', Project_Name = '$projectName', 
					Project_Status = '$projectStatus', Project_EstimatedBudget = '$projectBudget', 
					Project_RemainedBudget = '$budgetUpdate', Project_StartDate = '$projectStartDate', 
					Project_Description = '$projectDescription' WHERE Project_ID = '$proj'";
		mysqli_query($conn, $sql);
		
		mysqli_close($conn);
        header("Location: ./View.php?proj=" . $proj);
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
					window.location.href="./Add_Client.php?ret=<?php echo $_GET['proj']; ?>";
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
		<div class="w3-container" style="margin-top:6%">
			<a href="<?php echo './View.php?proj=' . $_GET['proj'] ?>"><button class="w3-button w3-red" name="cancel">Cancel</button></a>
			<div class="w3-sidebar w3-bar-block w3-white w3-border" style="width:25%; height:auto; min-height:516px; max-height:80%; overflow-y:auto">
				<div class="w3-panel">
					<?php
						/**
						 * Displays project outline (associated phases and tasks)
						 */
						$sql = "SELECT * FROM Projects WHERE Project_ID = '$proj'";
						$result = mysqli_query($conn, $sql);
						$count = mysqli_num_rows($result);
						if ($result)
						{
							$project = mysqli_fetch_array($result);
							if ($count == 1)
							{
								// Output list header
								echo "<h3 class='w3-border-bottom'>" . $project['Project_Name'] . "</h3>";
								echo "<ul id='project_list'>";

								// Get all phases associated with the project
								$phaseSql = "SELECT * FROM Phases WHERE Project_ID_FK = '$proj'";
								if ($result = mysqli_query($conn, $phaseSql))
								{
									$phaseCount = mysqli_num_rows($result);
									while ($phaseCount >= 1 && $phase = mysqli_fetch_array($result))
									{
										// Display phase as a non-interactive list element
										echo "<li class='w3-padding'><button class='w3-button w3-button-special "
												. "w3-blue' style='cursor:default'>" . $phase['Phase_Name'] 
												. "</button>";

										// Get all tasks associated with the phase
										$taskSql = "SELECT * FROM Tasks WHERE Phase_ID_FK = " 
														. $phase['Phase_ID'] . " AND Project_ID_FK = '$proj'";
										if ($taskResult = mysqli_query($conn, $taskSql))
										{
											echo "<ul id='tasks_phase_" . $phase['Phase_ID'] . "'>";
											while ($task = mysqli_fetch_array($taskResult))
											{
												// Display task as a non-interactive list element
												echo "<li class='w3-padding'><button class='w3-button "
														. "w3-button-special w3-light-blue' style='cursor:default'>" 
														. $task['Task_Name'] . "</button></li>";
											}
											echo "</ul>";
										}
										echo "</li>";
									}
								}
								echo "</ul>";
							}
						}
					?>
				</div>
			</div>
			<div class="w3-container" style="margin-left:25%; height:auto; min-height:516px; max-height:80%; overflow-y:auto">
				<div class="w3-border w3-padding">
					<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . "?proj=" . $_GET['proj']); ?>" autocomplete="off">
						<?php
							/**
							 * Displays input fields with pre-filled project details
							 */
							if ($count == 1)
							{
								// Display project name and ID
								echo "<label>Project Name:</label> <input class='w3-input w3-border' "
										. "type='text' name='Name' value='" . $project['Project_Name'] 
										. "' required /></p>";
								echo "<p>Project ID#: ". $project['Project_ID'] . "</p>";

								// Display client information
								$clientSql = "SELECT * FROM Clients WHERE Client_ID = " . $project['Client_ID_FK'];
								if ($result = mysqli_query($conn, $clientSql))
								{
									if (mysqli_num_rows($result) == 1)
									{
										$client = mysqli_fetch_array($result);
										if ($client['Client_Firstname'] != 'NA' && $client['Client_Lastname'] != 'NA'){
											echo "<p>Client: " . $client['Client_Firstname'] . " " . $client['Client_Lastname'] . "</p>";
										}
									}
								}

								// Display clients as a drop-down list
								echo "<label>Client Company:</label>";
								echo "<select class='w3-select w3-border' name='Client_CompanyName' >";
								foreach ($comps as $comps)
								{ 
									echo "<option value='" . $comps['Client_CompanyName'] . "'";
									if ($client['Client_CompanyName'] == $comps['Client_CompanyName']) 
									{
										echo "selected";
									}
									echo ">" . $comps['Client_CompanyName'] . "</option>";
								}
								echo "</select></p>";	
								echo "<button type='button' class='w3-button w3-green' "
										. "onclick='AddClient()'>Add Client</button></p>";
								
								// Display project status as a drop-down list
								echo "<label>Project Status:</label>";
								echo "<select class='w3-select w3-border' name='Project_Status' required>";
								foreach (array("Requested", "Approved", "On Hold", "Rejected", "Dead", "Completed") as $status)
								{
									echo "<option value='" . $status . "' ";
									if ($project['Project_Status'] == $status)
									{
										echo "selected";
									}
									echo ">" . $status . "</option>";
								}
								echo "</select></p>";

								// Display remaining project information
								echo "<label>Start Date:<label> <input class='w3-input w3-border' type='date' "
										. "name='StartDate' value='" . $project['Project_StartDate'] 
										. "' required />";
								echo "<label>Total Budget:<label> <input class='w3-input w3-border' type='number' "
										. "min='0' name='Budget' value='" . $project['Project_EstimatedBudget'] 
										. "' required />";
								echo "<p>Remaining Budget: $" . $project['Project_RemainedBudget'] . "</p>";					
								echo "<label>Description:<label> <textarea class='w3-input w3-border' rows='5' "
										. "cols='50' maxlength='2000' placeholder='Type here' "
										. "name='Project_Description' required>" . $project['Project_Description'] 
										. "</textarea>";
							}
							mysqli_close($conn);
						?>
						
						</br>
						<button class="w3-button w3-green" type="submit" name="ProjectSubmit">Save</button>   
					</form>
				</div>
			</div>
		</div>
	</body>
</html>
