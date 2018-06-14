<?php
	/**
     * Project Planner
     * home.php
     * Displays a table of all projects with links to view project details
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

	// This page should be inaccessible if a user is not logged in
	if(!Session::UserLoggedIn())
	{
		header("Location: ./login.php");
	}

	// Attempt DB connection
	$conn = mysqli_connect($_SESSION["SERVER"], $_SESSION["DBUSER"], $_SESSION["DBPASS"], 
							$_SESSION["DATABASE"]);
	if (!$conn)
	{
		// Output error details
		die('Unable to connect.  Error: ' . mysqli_error($conn));
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset=utf-8 />
		<link href ="./style.css" rel="stylesheet">
		<script type="text/JavaScript">
			// ViewProject()
			// Redirects to the View page for the specified project
			function ViewProject(projectid)
			{
				window.location.href="./Project/view.php?proj=" + projectid;
			}
		</script>
	</head>
	<body>
		<div class="w3-top w3-card w3-white" style="height:10%">
			<div class="w3-bar w3-padding">
				<a class="w3-bar-item" href="./home.php"><h1>Project Planner</h1></a>
				<div class="w3-right">
					<a class="w3-bar-item" href="./Users/View.php">Logged in as <?php echo $_SESSION['CURRENT_USER']->GetFirstName() . " " . $_SESSION['CURRENT_USER']->GetLastName() . " (" . $_SESSION['CURRENT_USER']->GetUsername() . ")";?></a>
					<a href="./logout.php"><button type="button" class="w3-bar-item w3-button w3-red">Sign Out</button></a>
				</div>
			</div>
		</div>
		<div class="w3-container" style="margin-top:20%">
			<div class="w3-panel w3-display-middle w3-padding">
				<div>
					<?php
						/**
						 * Displays the Create New Project button if user is a Manager
						 */
						if($_SESSION['CURRENT_USER']->getUserRole() == 1){
							echo "<a href='./Project/Create.php'><button class='w3-button w3-green'>"
									. "Create New Project</button></a>";
						}
					?>
				</div>
			<div>
				<?php
					/**
					 * Displays a table containing the projects visible to the user
					 */
					echo "<table class='w3-table-all w3-hoverable'>";
					echo "<thead><tr class='w3-light-grey'>
							<th>Project ID</th>
							<th>Project Name</th>
							<th>Total Hours</th>
							<th>Estimated Budget</th>
							<th>Remaining Budget</th>
							</thead>";
						
					// Retrieve and display all projects
					$sql = "SELECT * FROM Projects";
					if($Result = mysqli_query($conn, $sql))
					{
						while ($row = mysqli_fetch_array($Result))
						{
							// If user logged in is an employee, display only the projects to which
							// the account has been assigned
							if($_SESSION['CURRENT_USER']->GetUserRole() == 0)
							{
								$assignSql = "SELECT * FROM User_Assignments WHERE Project_ID_FK = " 
												. $row['Project_ID'] . " AND User_ID_FK = " 
												. $_SESSION['CURRENT_USER']->GetUserID();
								if($result = mysqli_query($conn, $assignSql))
								{
									if (mysqli_num_rows($result) > 0)
									{
										// Display project information as a row
										echo "<tr class='project_link' onclick='ViewProject(" . $row['Project_ID'] . ")'>";
										echo "<td>" . $row['Project_ID'] . "</td>";
										echo "<td>" . $row['Project_Name'] . "</td>";
										echo "<td>" . $row['Project_TotalHours'] . "</td>";
										echo "<td>$" . $row['Project_EstimatedBudget'] . "</td>";
										echo "<td>$" . $row['Project_RemainedBudget'] . "</td>";
										echo "</tr>";
									}
								}
							}
							else
							{
								// If a manager is logged in, Display all project information as rows
								echo "<tr class='project_link' onclick='ViewProject(" . $row['Project_ID'] . ")'>";
								echo "<td>" . $row['Project_ID'] . "</td>";
								echo "<td>" . $row['Project_Name'] . "</td>";
								echo "<td>" . $row['Project_TotalHours'] . "</td>";
								echo "<td>$" . $row['Project_EstimatedBudget'] . "</td>";
								echo "<td>$" . $row['Project_RemainedBudget'] . "</td>";
								echo "</tr>";
							}
						}
					}
					echo "</table>";
					mysqli_close($conn);
				?>
			</div>
		</div>	
	</body>
</html>
