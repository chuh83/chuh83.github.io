<?php
$db = new PDO('sqlite:sixflags.db') or die ("cannot open database");
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT * FROM RIDE_INFO";
$result = $db->query($sql);

foreach($result as $row) {
    echo $row['rideID'].' '.$row['thrillLevel'].' '.$row['rideType'].' '.$row['minimumHeight'].'<br/>';
}

// while ($row = $result->fetchArray($result)) {
//     echo $row['thrillLevel'] . ': $'. $row['minimumHeight'] . '<br/>';
// }
// echo $result;

// echo phpinfo();

$db = null;
?>