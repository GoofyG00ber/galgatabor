<?php
$host = "mysql.rackhost.hu";
$user = "c64634tabor";
$pass = "asdfasmc123";
$dbname = "c64634summer_camp";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>


<?php
define('SMTP_HOST', 'smtp.rackhost.hu');
define('SMTP_USERNAME', 'info@galgatabor.hu');
define('SMTP_PASSWORD', '5Jy#zWTi8@YiHtE');
define('SMTP_FROM', 'info@galgatabor.hu');
define('SMTP_TO', 'info@galgatabor.hu');
?>
