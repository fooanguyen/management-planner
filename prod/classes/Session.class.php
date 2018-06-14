<?php
    /**
     * Project Planner
     * Session.class.php
     * Defines Session variables, including database credentials and user ID
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
    class Session
    {
        // Start()
        // Starts/resumes session and sets DB credentials
        public static function Start()
        {
            // Start or resume session
            session_start();

            // Get DB credentials from config file
            $config = fopen(realpath(dirname(__FILE__)) . "/../scripts/dbconfig.ini", "r");
            $_SESSION['SERVER'] = trim(explode(" ", fgets($config))[2]);
            $_SESSION['DBUSER'] = trim(explode(" ", fgets($config))[2]);
            $_SESSION['DBPASS'] = trim(explode(" ", fgets($config))[2]);
            $_SESSION['DATABASE'] = trim(explode(" ", fgets($config))[2]);
            fclose($config);
            return;
        }

        // UserLoggedIn()
        // Returns true if the Session's user ID is set
        public static function UserLoggedIn()
        {
            return (Session::GetUserID() != NULL);
        }   

        // GetUserID()
        // Returns the ID of the user currently logged in
        public static function GetUserID()
        {
            if (isset($_SESSION))
            {
                if (isset($_SESSION['user_id']))
                {
                    return $_SESSION['user_id'];
                }
            }
            else return NULL;
        }

        // SetUserID()
        // Sets the user ID session variable
        public static function SetUserID($userid)
        {
            if (isset($_SESSION))
            {
                $_SESSION['user_id'] = $userid;
            }
        }

        // CloseSession()
        // Destroys all dession data
        public static function CloseSession()
        {
            $_SESSION = NULL;
            return session_destroy();
        }
    }
?>
