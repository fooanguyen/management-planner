<?php
	/**
     * Project Planner
     * Project/Phases/Delete.php
     * Deletes a phase (and associated tasks) from a project
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

    // Do not proceed if no phase, project, and delkey are specified
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
		die('Unable to connect.  Error: ' . mysqli_error($conn));
    }
    
    // Get phase ID and unique deletion key
    $p = mysqli_real_escape_string($conn, $_GET['p']);
    $delkey = $_GET['d'];

    // If delkey verified, proceed
    if (password_verify($p . "delete" . $p, $delkey))
    {
        // Retrieve and delete all tasks associated with this phase
        $sql = "SELECT * FROM Tasks WHERE Phase_ID_FK = '$p'";
        if ($Result = mysqli_query($conn, $sql))
        {
            while ($task = mysqli_fetch_array($Result))
            {
                // Erase the task's cost and work hours from the project's running totals
                $sql = "UPDATE Projects SET Project_TotalHours = Project_TotalHours - " 
                        . $task['Task_EstimatedHours'] . ", Project_RemainedBudget = Project_RemainedBudget + " 
                        . $task['Task_EstimatedCost'] . " WHERE Project_ID = " . $task['Project_ID_FK'];
                mysqli_query($conn, $sql);
                
                // Delete the task
                $delSql = "DELETE FROM Tasks WHERE Task_ID = " . $task['Task_ID'];
                mysqli_query($conn, $delSql);
            }
        }

        // Retrieve and delete all user assignments for this phase
        $sql = "SELECT * FROM User_Assignments WHERE Phase_ID_FK = '$p'";
        if ($Result = mysqli_query($conn, $sql))
        {
            while ($assign = mysqli_fetch_array($Result))
            {
                $delSql = "DELETE FROM User_Assignments WHERE Assignment_ID = " 
                            . $assign['Assignment_ID'];
                mysqli_query($conn, $delSql);
            }
        }
    
        // Delete the phase
        $sql = "SELECT * FROM Phases WHERE Phase_ID = '$p'";
        if ($Result = mysqli_query($conn, $sql))
        {
            $count = mysqli_num_rows($Result);
            if ($count == 1)
            {
                $sql = "DELETE FROM Phases WHERE Phase_ID = '$p'";
                mysqli_query($conn, $sql);
            }
        }
    }

    // Return to project view
    mysqli_close($conn);
    header("Location: ../View.php?proj=" . $_GET['prid']);
?>