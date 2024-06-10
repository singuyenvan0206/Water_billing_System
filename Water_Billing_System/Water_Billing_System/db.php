<?php
$host = 'localhost';
$username = 'root';
$password = '123456'; // Make sure to replace 'your_mysql_password' with your actual MySQL root password
$database = 'sourcecodester_wbsdb'; // Replace 'your_database_name' with the name of your database
$conn = mysqli_connect('localhost', 'root', '123456',"sourcecodester_wbsdb");
	 if (!$conn)
    {
	 die('Could not connect: ' . mysql_error());
	} 
	

