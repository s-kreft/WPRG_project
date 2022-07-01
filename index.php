<?php
//session_start();
include("form.html");
$x = 'zmienna';
if (isset($_POST['language'])) {
    $x = $_POST['language'];
}

/////// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "wprg_projekt";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

$sql = "SELECT vote FROM glosowanie WHERE form_option = '$x'";
$result = $conn->query($sql);

$row = mysqli_fetch_assoc($result);
if($row != null){
    $db_value = (int)$row['vote'];
    $db_value++;
    $sql = "UPDATE glosowanie SET vote = '$db_value' WHERE form_option = '$x'";
    $result = $conn->query($sql);
}

authentication($conn);
echo "<br>";
echo "Suma głosowań: " . getResultsCount($conn);
echo "<br>";
echo "Procentowy rozkład głosów: " . "<br>";
foreach (displayPercentage(getResults($conn), $conn) as $key => $value){
    echo $key . ": " . $value . "%" . "<br>";
}

///////////////////////////////////////SREDNIA
function getResultsCount($conn)
{
    $zap = "SELECT vote FROM glosowanie";
    $wynik = $conn->query($zap);
    $sum = 0;

    while ($wiersz = mysqli_fetch_row($wynik)) {
        $sum += (int)$wiersz[0];
    }
    return $sum;
}

function getResults($conn)
{
    $zap = "SELECT form_option, vote FROM glosowanie";
    $wynik = $conn->query($zap);
    $array = array();

    while ($wiersz = mysqli_fetch_row($wynik)) {
        $array[$wiersz[0]] = $wiersz[1];
    }
    return $array;
}

function displayPercentage($array, $conn)
{
    $percentage_array = array();
    foreach ($array as $key => $value) {
        $percentage_array[$key] = ($value / getResultsCount($conn)) * 100;
        $percentage_array[$key] = round($percentage_array[$key], 2);
    }
    return $percentage_array;
}

function authentication($conn)
{
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $sql = "SELECT adres FROM adres_ip WHERE adres = '$ip_address'";
    $result = $conn->query($sql);


    $rows = mysqli_fetch_assoc($result);

    if ($rows === null) {
        $sql = "INSERT INTO adres_ip (adres) VALUES('$ip_address')";
        $conn->query($sql);
    } else {
        echo "JUŻ GŁOSOWANO Z TEGO ADRESU Ip!";
    }
}
?>