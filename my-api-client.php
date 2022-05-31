<?php

    // if cors (cross-origin resource sharing) error occurs uncomment the 2 header function below
    //header("Access-Control-Allow-Origin: *");
    //header("Access-Control-Allow-Headers: *");

    $sname = "localhost";       // server name
    $uname = "2065901";         // user name
    $pswd = 244466666;          // password for the user
    $dbname = "db_2065901";     // database name
    $table_name = "weather";    // table name

    // connecting to mysql using mysqli
    $mysqli = new mysqli($sname, $uname, $pswd);
    
    // if connecting fails then outputs the error code
    if ($mysqli -> connect_errno) {
        echo("Failed to connect to MySQL: " . $mysqli -> connect_error);
        exit();
    }
    
    // import the my-api-server.php file
    include('my-api-server.php');

    // selecting the $dbname database
    $mysqli->select_db($dbname);

    // storing the sql command with the following parameter in $sql variable
    $sql = "SELECT *
    FROM weather
    WHERE weather_city = '{$_GET['city']}'
    AND weather_when >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
    ORDER BY weather_when DESC limit 1";

    $result = $mysqli -> query($sql);
    
    // Get data, convert to JSON and print
    $row = $result -> fetch_assoc();
    print(json_encode($row));
        
    // freeing the memory by clearing the values in the result and closing the currently used database
    $result -> free_result();
    $mysqli -> close();
?>