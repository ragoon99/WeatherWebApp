<?php
    // $sname = "localhost";       // server name
    // $uname = "root";      // user name
    // $pswd = "";          // password for the user
    // $dbname = "db_2065901";     // database name
    // $table_name = "weather";    // table name

    // // connecting to mysql using mysqli
    // $mysqli = new mysqli($sname, $uname, $pswd);

    /*
        Check if there is already a database with the $dbname if not
        create a one and create a table with the following data definations.
    */
    if($mysqli->select_db($dbname) == TRUE){

        /*
            Check if there is a table with the $table_name if not create one
            with the following data definations.
        */
        if($result = $mysqli->query("SHOW TABLES LIKE '$table_name';")){
            if($result->num_rows == 0) {
                $mysqli->query("CREATE TABLE weather(
                    weather_description VARCHAR(30) NULL,
                    weather_temperature FLOAT DEFAULT NULL,
                    weather_wind FLOAT NULL,
                    weather_when DATETIME DEFAULT CURRENT_TIMESTAMP(),
                    weather_windSpeed FLOAT NULL,
                    weather_pressure INT NULL,
                    weather_humidity INT NULL,
                    weather_city VARCHAR(30) NULL,
                    weather_cloudiness INT NULL,
                    icon_code VARCHAR(6) NULL,
                    country VARCHAR (5) NULL);");
            }
        } 
    } else {
        $mysqli->query("CREATE DATABASE $dbname;");
        $mysqli->query("USE $dbname");
        $mysqli->query("CREATE TABLE weather(
            weather_description VARCHAR(30) NULL,
            weather_temperature FLOAT DEFAULT NULL,
            weather_wind FLOAT NULL,
            weather_when DATETIME DEFAULT CURRENT_TIMESTAMP(),
            weather_windSpeed FLOAT NULL,
            weather_pressure INT NULL,
            weather_humidity INT NULL,
            weather_city VARCHAR(30) NULL,
            weather_cloudiness INT NULL,
            icon_code VARCHAR(6) NULL,
            country VARCHAR (5) NULL);");
    }

    // selecting weather data for the $_GET['city'] city that was fetched before 5 minute.
    $sql = "SELECT *
    FROM weather
    WHERE weather_city = '{$_GET['city']}'
    AND weather_when >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
    ORDER BY weather_when DESC limit 1";

    $result = $mysqli -> query($sql);

    /*
        if 0 records are found while executing the above query than fetches a 
        fresh data from the opeanweathermap API and appends the fresh data to the
        database
    */
    if ($result->num_rows == 0) {

        // storing api url to $url variable and looks for the city variable in the url
        $url = 'https://api.openweathermap.org/data/2.5/weather?q=' . $_GET['city'] . '&appid=dfa59c8c1efce4d47d11d87e76ec5299&units=metric';

        // get data from openweathermap and store in JSON object
        $data = file_get_contents($url);
        $json = json_decode($data, true);

        // print_r($json);

        // storing the fetched values in variables
        $weather_description = $json['weather'][0]['description'];
        $weather_temperature = $json['main']['temp'];
        $weather_windDeg = $json['wind']['deg'];
        $weather_windSpeed = $json['wind']['speed'];
        $weather_pressure = $json['main']['pressure'];
        $weather_humidity = $json['main']['humidity'];
        $weather_city = $json['name'];
        $weather_cloudiness = $json['clouds']['all'];
        $weather_icon = $json['weather'][0]['icon'];
        $weather_country = $json['sys']['country'];

        // inserting the values fetched from the openweatherAPI to its respective entry
        $sql = "INSERT INTO weather(
            weather_description,
            weather_temperature,
            weather_wind,
            weather_windSpeed,
            weather_pressure,
            weather_humidity,
            weather_city,
            weather_cloudiness,
            icon_code,
            country) VALUES('{$weather_description}', {$weather_temperature}, {$weather_windDeg}, '{$weather_windSpeed}', '{$weather_pressure}', '{$weather_humidity}', '{$weather_city}', '{$weather_cloudiness}', '{$weather_icon}','{$weather_country}');";
            
        // Run SQL statement and report errors
        if (!$mysqli -> query($sql)) {
            echo("<h4>SQL error description: " . $mysqli -> error . "</h4>");
        }

    // deleting the data that is 24 hours old
    $sql = "DELETE
    FROM weather
    WHERE weather_when <= DATE_SUB(NOW(), INTERVAL 24 HOUR);";

    $mysqli -> query($sql);
    }
?>