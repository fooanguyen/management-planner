<?php
	/**
     * Project Planner
     * Project/Phases/Edit.php
     * Edits the details of a specified phase
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
		die('Unable to connect' . mysqli_connect_error($conn));
	}

	// Prepare Phase and Project IDs for SQL query
	$prid = mysqli_real_escape_string($conn, $_GET['prid']);
	$phid = mysqli_real_escape_string($conn, $_GET['phid']);
	$sql = "SELECT * FROM Phases WHERE Phase_ID = '$phid'";

	// If phase detail input is set, proceed with editing
	if (isset($_POST['PhaseSubmit']) && !empty($_POST))
	{
		$phaseName = mysqli_real_escape_string($conn, $_POST['Name']);
		$description = mysqli_real_escape_string($conn, $_POST['Description']);

		$sql = "UPDATE Phases SET Phase_Name = '$phaseName', Phase_Description = '$description' 
					WHERE Phase_ID='$phid'";
		mysqli_query($conn, $sql);

		mysqli_close($conn);
		header("Location: ./View.php?prid=" . $prid . "&phid=" . $phid);
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset=utf-8 />
		<link href ="../../style.css" rel="stylesheet">
		<script type="text/JavaScript">
			// GetSearchResults()
			// Requests search results from User_Search.php and inserts results list
			function GetSearchResults()
            {
				// Get search text and if not empty, prepare request
                var searchText = document.getElementById("SearchBar").value;
                if (searchText != "")
                {
                    var handler = new XMLHttpRequest();
                    handler.onreadystatechange = function()
                    {
                        if (this.readyState == 4 && this.status == 200)
                        {
							// Receive search results
                            var users = JSON.parse(this.responseText);
                            if (users.length > 0)
                            {
								// Check if results were found
                                if (users[0] == "No results found!")
                                {
                                    document.getElementById("AssignResults").innerHTML = users[0];
                                }
                                else
                                {
									// If results found, push each result to the list
                                    document.getElementById("AssignResults").innerHTML = "";
                                    for (var u = 0; u < users.length; u += 2)
                                    {
                                        document.getElementById("AssignResults").innerHTML 
											+= "<li title='Click to add...' style='cursor:pointer' onclick='AssignUser(" 
											+ users[u+1] + ", 1)'>" + users[u] + "</li>";
                                    }

                                }
                            }
                        }
                    }

					// Send request to User_Search.php
                    handler.open("GET", "./User_Search.php?u=" + searchText, false);
                    handler.send();
                }
			}

			// AssignUser()
			// Requests user assignment change from Edit_User_Assignment.php
			function AssignUser(uid, com)
			{
				// Prepare confirmation dialogue before assigning or removing the user
				if (com == 1)
				{
					var message = "Are you sure you want to assign this user to this phase?  "
									+ "Your other changes to the phase will not be saved.";
				}
				else
				{
					var message = "Are you sure you want to remove this user from this phase?  "
									+ "Your other changes to the phase will not be saved.";
				}
				if (confirm(message))
                {
                    var handler = new XMLHttpRequest();
                    handler.onreadystatechange = function()
                    {
                        if (this.readyState == 4 && this.status == 200)
                        {
							// Any response is an error, so alert it
                            if (String(this.responseText).length > 0)
                            {
                                alert(this.responseText);
                            }
                            else
                            {
								// If successful, reload the page to show changes
                                location.reload();
                            }
                        }
                    }

					// Send request to Edit_User_Assignment.php
					if (com == 1)
					{
                    	handler.open("GET", "./Edit_User_Assignment.php?uid=" + uid 
										+ "&phid=<?php echo $_GET['phid']; ?>&prid=<?php echo $_GET['prid']; ?>"
										+ "&com=1", false);
					}
					else
					{
						handler.open("GET", "./Edit_User_Assignment.php?uid=" + uid 
										+ "&phid=<?php echo $_GET['phid']; ?>&prid=<?php echo $_GET['prid']; ?>"
										+ "&com=0", false);
					}
                    handler.send();
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
				<a href="./View.php?prid=<?php echo $_GET['prid'] . '&phid=' . $_GET['phid'] ?>"><button class="w3-button w3-red" type="cancel" name="cancel">Cancel</button></a>
				<div class="w3-border w3-padding">
					<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . "?prid=" . $_GET['prid'] . "&phid=" . $_GET['phid']); ?>" autocomplete="off">
						<?php
							/**
							 * Displays input fields pre-filled with existing phase details
							 */
							if ($result = mysqli_query($conn, $sql))
							{
								$count = mysqli_num_rows($result);
								$phase = mysqli_fetch_array($result);
								if ($count == 1)
								{
									// Display phase name and description
									echo "<label>Name:</label> <input class='w3-input w3-border' "
											. "type='text' name='Name' value='" . $phase['Phase_Name'] 
											. "' required />";
									echo "<label>Description:<label> <textarea class='w3-input w3-border' "
											. "rows='5' cols='50' maxlength='2000' placeholder='Type here' "
											. "name='Description' required>" . $phase['Phase_Description'] 
											. "</textarea>";
								}
							}

							// Display assigned users
							echo "<h3>Assigned Users: </h3>";
							echo "<ul class='w3-ul w3-hoverable'>";
							$sql = "SELECT * FROM User_Assignments WHERE Phase_ID_FK = " 
										. $phase['Phase_ID'];
							if ($result = mysqli_query($conn, $sql))
							{
								// For each user assignment, grab the full name of the assigned user
								while ($assign = mysqli_fetch_array($result))
								{
									$userSql = "SELECT * FROM Users WHERE User_ID = " 
													. $assign['User_ID_FK'];
									if ($userResult = mysqli_query($conn, $userSql))
									{
										// If user matches assignment ID, display name as an 
										// interactive list element
										$user = mysqli_fetch_array($userResult);
										echo "<li title='Click to remove...' style='cursor:pointer' "
												. "onclick='AssignUser(" . $user['User_ID'] . ", 0)'>" 
												. $user['User_Firstname'] . " " . $user['User_Lastname'] 
												. " (" . $user['User_Name'] . ")</li>";
									}
								}
							}
							echo "</ul>";

							// Display User search bar and search results field
							echo "</br>";
							echo "<label>Assign User:</label> <input type='search' id='SearchBar' "
									. "placeholder='Search for User...' onkeydown='if (event.keyCode == 13) "
									. "return false;'/> <button class='w3-button w3-green' type='button' "
									. "onclick='GetSearchResults()'>Search</button></p>";
							echo "<ul class='w3-ul w3-hoverable' id='AssignResults'></ul>";
							mysqli_close($conn);
						?>
						
						</br>
						<button class="w3-button w3-green" type="submit" name="PhaseSubmit">Save</button>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>