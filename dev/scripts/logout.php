<?php
	/**
     * Project Planner
     * Logout.php
     * Destroys the current session
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

    // Log out and destroy session
    $temp = $_SESSION['CURRENT_USER'];
    $temp->Logout();
    header("Location: ./login.php")
?>
