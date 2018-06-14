<?php
	/**
     * Project Planner
     * Project/View.php
     * Displays information about a specified project
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
	if(!Session::UserLoggedIn())
	{
		header("Location: ../login.php");
	}

	// Do not proceed if no project is specified
	if(empty($_GET))
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

	// Prepare project ID for SQL query
	$proj = mysqli_real_escape_string($conn, $_GET['proj']);
	$sql = "SELECT * FROM Projects WHERE Project_ID = '$proj'";
	if($result = mysqli_query($conn, $sql))
	{
		// If project found, store its information
		$count = mysqli_num_rows($result);
		$project = mysqli_fetch_array($result);
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset=utf-8 />
		<link href ="../style.css" rel="stylesheet">
		<script type="text/JavaScript">
			// ConfirmDelete()
			// Prevents accidental project deletion
			function ConfirmDelete(pid)
			{
				// Confirm that the user actually wants to delete the project
				if (confirm("Once you delete this project, it cannot be recovered.  "
							+ "Additionally, all associated phases and tasks will be deleted.  "
							+ "Are you absolutely sure?"))
				{
					// Pass project ID and delkey to Delete script
					window.location.href="./Delete.php?p=" + pid + "&d=" 
											+ "<?php echo password_hash($_GET['proj'] . "delete" 
																		. $_GET['proj'], PASSWORD_BCRYPT); ?>";
				}
			}
		</script>
	</head>
	<body>
		<div class="w3-top w3-card w3-white" style="height:10%">
			<div class="w3-bar w3-padding">
				<a class="w3-bar-item" href="/home.php"><h1>Project Planner</h1></a>
				<div class="w3-right">
					<a class="w3-bar-item" href="../Users/View.php">Logged in as <?php echo $_SESSION['CURRENT_USER']->GetFirstName() . " " . $_SESSION['CURRENT_USER']->GetLastName() . " (" . $_SESSION['CURRENT_USER']->GetUsername() . ")";?></a>
					<a href="../logout.php"><button class="w3-bar-item w3-button w3-red">Sign Out</button></a>
				</div>
			</div>
		</div>
		<div class="w3-container" style="margin-top:6%">
			<a href="../home.php"><button class="w3-button w3-green">Return to Home</button></a>
			<div class="w3-sidebar w3-bar-block w3-white w3-border" style="width:25%; height:auto; min-height:516px; max-height:80%; overflow-y:auto">
				<div class="w3-panel">
					<?php
						/**
						 * Displays Project outline (project's phases and tasks)
						 */
						if ($count == 1)
						{
							// Outline list header
							echo "<h3 class='w3-border-bottom'>" . $project['Project_Name'] . "</h3>";
							echo "<ul id='project_list'>";

							// Get all phases associated with the project
							$phaseSql = "SELECT * FROM Phases WHERE Project_ID_FK = '$proj'";
							if($result = mysqli_query($conn, $phaseSql))
							{
								$phaseCount = mysqli_num_rows($result);
								while ($phase = mysqli_fetch_array($result))
								{
									// Display phase as a button that displays phase details
									echo "<li class='w3-padding'><a href='./Phases/View.php?prid=" 
											. $project['Project_ID'] . "&phid=" . $phase['Phase_ID'] 
											. "'><button class='w3-button w3-blue'>" . $phase['Phase_Name'] 
											. "</button></a>";
									
									// Get all tasks associated with the project
									$taskSql = "SELECT * FROM Tasks WHERE Phase_ID_FK = " 
												. $phase['Phase_ID'] . " AND Project_ID_FK = '$proj'";
									if ($taskResult = mysqli_query($conn, $taskSql))
									{
										echo "<ul id='tasks_phase_" . $phase['Phase_ID'] . "'>";
										while($task = mysqli_fetch_array($taskResult))
										{
											// Display task as a button that displays task details
											echo "<li class='w3-padding'><a href='./Tasks/View.php?prid=" 
													. $project['Project_ID'] . "&tid=" . $task['Task_ID'] 
													. "'><button class='w3-button w3-light-blue'>" 
													. $task['Task_Name'] . "</button></a></li>";
										}

										// Get user assignments for the user logged in
										$assignSQL = "SELECT * FROM User_Assignments WHERE User_ID_FK = " 
														. $_SESSION['CURRENT_USER']->GetUserID() 
														. " AND Phase_ID_FK = " . $phase['Phase_ID'];

										// If a manager is logged in, or if an employee is assigned to the task, 
										// display the "Create Task" button
										if(mysqli_query($conn, $assignSQL) != FALSE 
											|| $_SESSION['CURRENT_USER']->GetUserID() == 1)
										{
											echo "<li class='w3-padding'><a href='./Tasks/Create.php?prid=" 
													. $project['Project_ID'] . "&phid=" . $phase['Phase_ID'] 
													. "'><button class='w3-button w3-green'>Create Task</button></a></li>";
										}
										echo "</ul>";
									}
									echo "</li>";
								}
							}

							// If a manager is logged in, display the "Create Phase" button
							if($_SESSION['CURRENT_USER']->GetUserRole() == 1)
							{
								echo "<li><a href='./Phases/Create.php?prid=" . $project['Project_ID'] 
										. "'><button class='w3-button w3-green'>Create Phase</button></a></li>";
								echo "</ul>";
							}
						}
					?>
				</div>
			</div>
			<div class="w3-container" style="margin-left:25%; height:auto; min-height:516px; max-height:80%; overflow-y:auto">
				<div class="w3-border w3-padding">
					<?php
						/**
						 * Displays Project details and Manager functions
						 */
						if ($count == 1)
						{
							// Project details header
							echo "<h2 class='w3-border-bottom'>Project Name: " 
									. $project['Project_Name'] . "</h2>";

							// Display project ID
							echo "<h4>Project ID#: ". $project['Project_ID'] . "</h4>";

							// Display client information
							$clientSql = "SELECT * FROM Clients WHERE Client_ID = " . $project['Client_ID_FK'];
							if ($result = mysqli_query($conn, $clientSql))
							{
								if (mysqli_num_rows($result) == 1)
								{
									$client = mysqli_fetch_array($result);
									if($client['Client_Firstname'] != 'NA' && $client['Client_Lastname'] != 'NA' 
										&& $client['Client_CompanyName'] != 'NA')
									{
										echo "<h4>Client: " . $client['Client_Firstname'] . " " 
												. $client['Client_Lastname'] . "</h4>";
										echo "<h4>Client Company: " . $client['Client_CompanyName'] . "</h4>";
									}
				
								}
							}

							// Display remaining project details
							echo "<h4>Project Status: " . $project['Project_Status'] . "</h4>";
							echo "<h4>Start Date: " . $project['Project_StartDate'] . "</h4>";
							echo "<h4>Estimated Hours to complete: " . $project['Project_TotalHours'] . "</h4>";
							echo "<h4>Total Budget: $" . $project['Project_EstimatedBudget'] . "</h4>";
							echo "<h4>Remaining Budget: $" . $project['Project_RemainedBudget'] . "</h4>";					
							echo "<h4>Description: " . $project['Project_Description'] . "</h4>";
						}

						// If a manager is logged in, display Edit and Delete buttons
						if($_SESSION['CURRENT_USER']->GetUserRole() == 1)
						{
							echo "<a href='./Edit.php?proj=" . $project['Project_ID'] 
									. "'><button class='w3-button w3-green'>Edit Project</button></a>";
							echo "<button class='w3-margin w3-button w3-red' onclick='ConfirmDelete(" 
									. $project['Project_ID'] . ")'>Delete Project</button>";
						}
						mysqli_close($conn);
					?>
				</div>
			</div>
		</div>
	</body>
</html>
