<?php
	/**
     * Project Planner
     * Project/Tasks/View.php
     * Displays details of a specified phase
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

	// Do not proceed if no phase is specified
	if (empty($_GET))
	{
		header("Location: ../../home.php");
	}

	// Attempt DB connection
	$conn = mysqli_connect($_SESSION["SERVER"], $_SESSION["DBUSER"], $_SESSION["DBPASS"], 
							$_SESSION["DATABASE"]);
	if (!$conn)
	{
		// Output error details
		die('Unable to connect.  Error: ' . mysqli_error($conn));
	}

	// Prepare task ID for SQL query
    $tid = mysqli_real_escape_string($conn, $_GET['tid']);
    $sql = "SELECT * FROM Tasks WHERE Task_ID = '$tid'";
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset=utf-8 />
		<link href ="../../style.css" rel="stylesheet">
		<script type="text/JavaScript">
			// ConfirmDelete()
			// Prevents accidental task deletion
			function ConfirmDelete(tid)
			{
				// Confirm that the user actually wants to delete the phase
				if (confirm("Once you delete this task, it cannot be recovered.  "
							+ "Are you absolutely sure?"))
				{
					// Pass task ID and delkey to Delete script
					window.location.href="./Delete.php?prid=<?php echo $_GET['prid']; ?>" + "&t=" + tid 
											+ "&d=" + "<?php echo password_hash($_GET['tid'] 
																. "delete" . $_GET['tid'], PASSWORD_BCRYPT); ?>";
				}
			}
		</script>
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
				<a href="../View.php?proj=<?php echo $_GET['prid'] ?>"><button class="w3-button w3-green">Return to Project</button></a>
				<div class="w3-border w3-padding">
					<?php
						/**
						 * Displays task details and Manager functions
						 */
						if ($result = mysqli_query($conn, $sql))
						{
							$count = mysqli_num_rows($result);
							$task = mysqli_fetch_array($result);
							if ($count == 1)
							{
								// Display task name and ID
								echo "<h1>Task Name: " . $task['Task_Name'] . "</h1>";
								echo "<p>Task ID#: " . $task['Task_ID'] . "</p>";

								// Display details on the author of the task
								$userSql = "SELECT * FROM Users WHERE User_ID = " 
												. $task['User_ID_FK'];
								if ($user = mysqli_query($conn, $userSql))
								{
									if (mysqli_num_rows($user) == 1)
									{
										$user = mysqli_fetch_array($user);
										echo "<p>Created by: " . $user['User_Firstname'] . " " 
											. $user['User_Lastname'] . " (" . $user['User_Name'] . ")</p>";
									}
								}

								// Display phase description, cost, and hours
								echo "<p>Estimated Hours to complete: " . $task['Task_EstimatedHours'] . "</p>";
								echo "<p>Estimated Cost: $" . $task['Task_EstimatedCost'] . "</p>";
								echo "<p>Task Description: " . $task['Task_Description'] . "</p>";
							}
						}
					
						// If a manager (or task creator) is logged in, display
						// Edit and Delete buttons
						if ($user['User_ID'] == $_SESSION['CURRENT_USER']->GetUserID() 
							|| $_SESSION['CURRENT_USER']->GetUserRole() == 1)
						{
							echo "<a href='./Edit.php?prid=" . $_GET['prid'] 
									. "&tid=" . $_GET['tid'] . "'><button class='w3-button w3-green'>"
									. "Edit Task</button></a>";
							echo " <button class='w3-button w3-red' onclick='ConfirmDelete(" 
									. $_GET['tid'] . ")'>Delete Task</button>";
						}
						mysqli_close($conn);
					?>
				</div>
			</div>
        </div>
    </body>
</html>