<?php

$date = date('Y-m-d');
$con = mysqli_connect("localhost","root",'','api1'); 
$response = array(); 
if($con){ 
    $sql = "SELECT * FROM `users` where `date` = $date"; 
    $result = mysqli_query($con,$sql);
    if($result){  
        header("Content-Type:JSON");
        $i=0;
        while($row = mysqli_fetch_assoc($result)){ 
            $response[$i]['id'] = $row['id']; 
            $response[$i]['city'] = $row['city'];
            $response[$i]['weather'] = $row['weather'];
            $response[$i]['humidity'] = $row['humidity'];
            $response[$i]['pressure'] = $row['pressure'];
            $response[$i]['time'] = $row['time'];
            $response[$i]['date'] = $row['date']; 
            $response[$i]['weather'] = $row['weather'];
            $i++;
        } 
        echo json_encode($response,JSON_PRETTY_PRINT);
    }
    else{
        $url = "https://api.openweathermap.org/data/2.5/weather?q=SaintPaulHarbor&units=metric&appid=12b804b396326a7144c45c58f7a34539";
        $res = file_get_contents($url);
        $data = json_decode($res);
        $query = "INSERT INTO `users`(`city`, `weather`, `humidity`, `pressure`, `wind`, `date`) VALUES ($data->name,$data->weather[0]->main,$data->humidity,$data->pressure,$data->wind->speed,'$date')";

        $sql = "SELECT * FROM `users` where `date` = $date"; 
        $result = mysqli_query($con,$sql);
        if($result){  
            header("Content-Type:JSON");
            $i=0;
            while($row = mysqli_fetch_assoc($result)){ 
                $response[$i]['id'] = $row['id']; 
                $response[$i]['city'] = $row['city'];
                $response[$i]['weather'] = $row['weather'];
                $response[$i]['humidity'] = $row['humidity'];
                $response[$i]['pressure'] = $row['pressure'];
                $response[$i]['time'] = $row['time'];
                $response[$i]['date'] = $row['date']; 
                $response[$i]['weather'] = $row['weather'];
                $i++;
            } 
            echo json_encode($response,JSON_PRETTY_PRINT);
    }
}
} 
else{ 
    echo "Datbase connection failed";
}
?>