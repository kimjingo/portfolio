<?php
$dataStr = $_SERVER['HTTP_COOKIE'];
$tmp = explode(";", $dataStr);
foreach($tmp as $val){
    $tmp1 = explode("=", $val);
    $data[trim($tmp1[0])] = trim($tmp1[1]);
}
//replace below with yours
include_once $_SERVER['DOCUMENT_ROOT']."/includes/DBConfig.php";

$sql ="CREATE TABLE IF NOT EXISTS `mystock` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`pdate` datetime NOT NULL ,
`total_invest` decimal(8,2) NOT NULL,
`total_actual_amount` decimal(8,2) NOT NULL,
`total_tobe_percent` decimal(8,2) NOT NULL,
`safe_actual_amount` decimal(8,2) NOT NULL,
`safe_tobe_percent` decimal(8,2) NOT NULL,
`risky_actual_amount` decimal(8,2) NOT NULL,
`risky_tobe_percent` decimal(8,2) NOT NULL,
`ip` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
`uid` tinyint(4) DEFAULT NULL,
`sid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
`created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`id`),
UNIQUE KEY (`pdate`)
)";
$stmt = $con->prepare($sql);
if($con->error) { echo $con->error; exit; }

$stmt->execute();
if($stmt->error) { echo $stmt->error ; exit; }

$ip = getenv("REMOTE_ADDR");
    $sql = "INSERT INTO mystock(pdate, total_invest, total_actual_amount, total_tobe_percent, safe_actual_amount, safe_tobe_percent, risky_actual_amount, risky_tobe_percent, ip, uid, sid)
            VALUES ( now(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ) ";
    $stmt = $con->prepare($sql);
    if($con->error) { echo $con->error; exit; }
    
    $stmt->bind_param("dddddddsss",$data['total_invest'],$data['total_actual_amount'],$data['total_tobe_percent'],$data['safe_actual_amount'],$data['safe_tobe_percent'],$data['risky_actual_amount'],$data['risky_tobe_percent'],$ip,$uid,$data['smid']);
    
    $stmt->execute();
    if($stmt->error) { echo $stmt->error ; exit; }
$con->close();
?>