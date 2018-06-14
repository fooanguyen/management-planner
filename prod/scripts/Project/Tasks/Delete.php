<?php
	/**
     * Project Planner
     * Project/Task/Delete.php
     * Deletes a task from a phase
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

    // Do not proceed if no task, project, and delkey are specified
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
    
    // Get task ID and unique deletion key
    $t = mysqli_real_escape_string($conn, $_GET['t']);
    $delkey = $_GET['d'];

    // If delkey verified, proceed
    if (password_verify($t . "delete" . $t, $delkey))
    {
        // Retrieve and delete the task
        $sql = "SELECT * FROM Tasks WHERE Task_ID = '$t'";
        if ($Result = mysqli_query($conn, $sql))
        {
            $count = mysqli_num_rows($Result);
            if ($count == 1)
            {
                // Erase the task's cost and work hours from the project's running totals
                $task = mysqli_fetch_array($Result);
                $sql = "UPDATE Projects SET Project_TotalHours = Project_TotalHours - " 
                            . $task['Task_EstimatedHours'] . ", Project_RemainedBudget = Project_RemainedBudget + " 
                            . $task['Task_EstimatedCost'] . " WHERE Project_ID = " . $task['Project_ID_FK'];
                mysqli_query($conn, $sql);
                
                // Delete the task
                $sql = "DELETE FROM Tasks WHERE Task_ID = '$t'";
                mysqli_query($conn, $sql);
            }
        }
    }

    // Return to project view
    mysqli_close($conn);
    header("Location: ../View.php?proj=" . $_GET['prid']);
?>