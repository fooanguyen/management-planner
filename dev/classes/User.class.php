<?php
    /**
     * Project Planner
     * User.class.php
     * Defines User-specific functions and 
     * stores information about the user currently logged in
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
    require_once(realpath(dirname(__FILE__)) . "/Session.class.php");

    class User
    {
        // User globals
        private $id = "";           // User ID
        private $firstame = "";    // User's first name
        private $lastname = "";     // User's last name
        private $username = "";     // User's username
        private $role = "";         // User's role (manager or employee)

        // Login()
        // Authenticates user's login information and starts session if verified
        public function Login($userid, $password)
        {
            // Don't procede if username and password fields are blank
            if ($userid != '' && $password != '')
            {
                // Attempt DB connection
                $conn = mysqli_connect($_SESSION["SERVER"], $_SESSION["DBUSER"], 
                                        $_SESSION["DBPASS"], $_SESSION["DATABASE"]);
                if (!$conn)
                {
                    // Output error details
                    die('Unable to connect.  Error: ' . mysqli_error($conn));
                }

                // Format SQL for query
                $userid = mysqli_real_escape_string($conn, $userid);
                $sql = "SELECT * FROM Users WHERE User_Name = '$userid' ";
                
                if ($Result = mysqli_query($conn, $sql))
				{
                    $count = mysqli_num_rows($Result);
				    $Row = mysqli_fetch_array($Result, MYSQLI_ASSOC);
                    
                    // Check if there is only one row with specified user_id and 
                    // match password to hash
                    if ($count == 1 && password_verify($password, $Row['User_Password']))
                    {
                        // Ses session ID and User information
                        Session::SetUserID($Row['User_ID']);
                        $this->id = $Row['User_ID'];
                        $this->firstname = $Row['User_Firstname'];
                        $this->lastname = $Row['User_Lastname'];
                        $this->username = $Row['User_Name'];
                        $this->role = $Row['User_Role'];
                    
                        mysqli_close($conn);
                        return true;
                    }
                    else 
                    {
                        // Authenitcation failure
                        mysqli_close($conn);
                        return false;
                    }
                }
            }
        }
        
        // GetFirstName()
        // Return's user's first name only
        public function GetFirstName()
        {
            return $this->firstname;
        }

        // GetLastName()
        // Returns user's last name only
        public function GetLastName()
        {
            return $this->lastname;
        }

        // GetUsername()
        // Returns user's username only
        public function GetUsername()
        {
            return $this->username;
        }

        // GetFullName()
        // Returns user's name in format "Firstname Lastname (username)"
        public function GetFullName()
        {
            return $this->firstname . " " . $this->lastname . " (" . $this->username . ")";
        }

        // GetUserID()
        // Return's user's ID number
        public function GetUserID()
        {
            return $this->id;
        }

        // GetUserRole()
        // Returns 1 for Manager or 0 for Employee
        public function GetUserRole()
        {
            return $this->role;
        }

        // Logout()
        // Closes session and returns to login screen
        public static function Logout()
        {
            Session::CloseSession();
        }
    }
?>
