<?php
	/**
     * Project Planner
     * Project/Phases/Edit_User_Assignment.php
     * Assigns users to phases, and removes them from phases when requested
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
    
    // Do not proceed if no IDs or commands are specified
    if (empty($_REQUEST))
    {
        header("Location: ../../home.php");
    }

    // Attempt DB connection
    $conn = mysqli_connect($_SESSION["SERVER"], $_SESSION["DBUSER"], $_SESSION["DBPASS"], 
                            $_SESSION["DATABASE"]);
	if (!$conn)
	{
        // Output error details
		echo "Unable to connect.  Error: " . mysqli_error($conn);
    }
    
    // Prepare user ID, phase ID, and project ID for SQL query
    $uid = mysqli_real_escape_string($conn, $_REQUEST['uid']);
    $phid = mysqli_real_escape_string($conn, $_REQUEST['phid']);
    $prid = mysqli_real_escape_string($conn, $_REQUEST['prid']);

    // Retrieve command (1 = Add user to phase, 0 = remove user from phase)
    $command = $_REQUEST['com'];

    if ($command == 1)
    {   
        // Cancel assignment if the user is already assigned to the phase
        $sql = "SELECT * FROM User_Assignments WHERE User_ID_FK = '$uid' 
                    AND Phase_ID_FK = '$phid' AND Project_ID_FK = '$prid'";
        if ($result = mysqli_query($conn, $sql))
        {
            if (mysqli_num_rows($result) >= 1)
            {
                echo "This user is already assigned to this project.";
                exit;
            }
        }
        
        // If not already assigned, add user/phase association to the 
        // User_Assignments table
        $sql = "INSERT INTO User_Assignments (Phase_ID_FK, User_ID_FK, Project_ID_FK) 
                    VALUES ('$phid', '$uid', '$prid')";
    }
    else
    {
        // Retrive user assignment and remove it
        $sql = "SELECT * FROM User_Assignments WHERE User_ID_FK = '$uid'";
        if ($Result = mysqli_query($conn, $sql))
        {
            $count = mysqli_num_rows($Result);
            if ($count == 1)
            {
                $sql = "DELETE FROM User_Assignments WHERE User_ID_FK = '$uid'";
            }
        }
    }

    mysqli_query($conn, $sql);
    mysqli_close($conn);
?>