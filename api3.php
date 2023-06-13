<html>
<head>
</head>
<body>

<form method="get" action="">
  <input type="text" name="city" placeholder="Enter city name">
  <input type="submit" name="submit" value="Search">
</form>

<?php

if (isset($_GET['submit'])) {
  $city = $_GET['city'];
} else {
  $city = "kodiak";
}

$url = "https://api.openweathermap.org/data/2.5/weather?q=$city&appid=12b804b396326a7144c45c58f7a34539";


// Make API request and parse JSON response
$response = file_get_contents($url);
$data = json_decode($response, true);

if (!$data) {
  // Handle API error
  die("Error: Failed to retrieve data from OpenWeatherMap API.");
}

// Extract relevant weather data
$city_name = $data['name'];
$condition = $data['weather'][0]['main'];
$icon = $data['weather'][0]['icon'];
$temperature = $data['main']['temp'];
$pressure = $data['main']['pressure'];
$humidity = $data['main']['humidity'];
$wind_speed = $data['wind']['speed'];
$rain = isset($data['rain']['1h']) ? $data['rain']['1h'] : 'not given';


// Insert or update weather data in database
$host = 'localhost';
$username = 'root';
$password = "";
$dbname = 'weather forecast';

$conn = mysqli_connect("localhost","root", "", "weather forecast");

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}else{
  // echo"Connection established";
}

// Check if data for the current hour is already present in database
$sql = "SELECT * FROM `data` WHERE `city`='$city_name' AND DATE(`date`) = CURDATE()";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  // Update existing row with latest weather data
  $sql = "UPDATE `data` SET `weather_condition`='$condition', `icon`='$icon', `temperature`='$temperature', `rain`= 0, `humidity`='$humidity', `wind_speed`='$wind_speed' WHERE `city`='$city_name' AND `date`= DATE_FORMAT(NOW(), '%Y-%m-%d %H:00:00')";
} else {
  // Insert new row with current weather data
 
  $sql = "INSERT INTO `data` (`city`, `date`, `weather_condition`, `icon`, `temperature`, `rain`, `wind_speed`, `humidity`)
        VALUES ('$city_name', NOW(), '$condition', '$icon', '$temperature', '0', '$wind_speed', '$humidity')";
}

 mysqli_query($conn, $sql);



$id = 5866583;
$api_key = "12b804b396326a7144c45c58f7a34539";
$i = 7;
while ($i >= 1) {
    $unix_time = strtotime("$i days ago");
    $date = date('Y-m-d', $unix_time);
    $query = "SELECT * FROM data WHERE city = '$city_name' AND date = '$date';";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) == 0) {
        $url = "https://history.openweathermap.org/data/2.5/history/city?id=$id&start=$unix_time&cnt=1&units=metric&appid=$api_key";

        $response = file_get_contents($url);

        $data = json_decode($response);
        $icon = $data->list[0]->weather[0]->icon;
        $description = $data->list[0]->weather[0]->description;
        $humidity = $data->list[0]->main->humidity;
        $pressure = $data->list[0]->main->pressure;
        $temperature = $data->list[0]->main->temp;
        $wind = $data->list[0]->wind->speed;

        $insertQuery = "INSERT INTO data (`city`, `date`, `weather_condition`, `icon`, `temperature`, `rain`, `wind_speed`, `humidity`) 
                        VALUES ('$city_name', '$date','$description', '$icon',$temperature, 0,$wind, $humidity)";
        mysqli_query($conn, $insertQuery);
    }

    $i = $i - 1;
}


// Retrieve latest weather data from database
$sql = "SELECT * FROM  `data` WHERE `city`='$city_name' ORDER BY `date` DESC LIMIT 8";
$result = mysqli_query($conn, $sql);

echo "<h1> $city_name</h1>";

echo "<table border='1'>";
echo "<tr>";
echo "<th>Date/Time</th>";
echo "<th>Weather_Condition</th>";
echo "<th>Icon</th>";
echo "<th>Temperature</th>";
echo "<th>Humidity</th>";
echo "<th>Wind Speed</th>";
echo "</tr>";
while ($row = mysqli_fetch_assoc($result)) {
  $date = date('Y-m-d H:i:s', strtotime($row['date']));
  $condition = $row['weather_condition'];
  $icon = $row['icon'];
  $temperature = $row['temperature'];
  $humidity = $row['humidity'];
  $wind_speed = $row['wind_speed'];

  echo "<tr>";
  echo "<td>{$date}</td>";
  echo "<td>{$condition}</td>";
  echo "<td><img src='http://openweathermap.org/img/w/{$icon}.png'></td>";
  echo "<td>{$temperature}°C</td>";
  echo "<td>{$humidity}%</td>";
  echo "<td>{$wind_speed} m/s</td>";
  echo "</tr>";
}
echo "</table>";




// Close database connection
mysqli_close($conn);
?>
</body>
</html>