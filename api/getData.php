<?php
include_once $_SERVER['DOCUMENT_ROOT']."/includes/DBConfig.php";
include_once $_SERVER['DOCUMENT_ROOT']."/includes/functions.php";

$mode = $_GET["mode"];
$colno = $_GET["type"];
// This is just an example of reading server side data and sending it to the client.
// It reads a json formatted text file and outputs it.
if(!$mode) { exit();}
$data = array();
switch($mode) {
    case "expense" :
        switch($colno){
            case 0:
                $colname="vendor";
                $sql = "SELECT date_format(pdate, '%Y-%m') yymm, year(pdate) yy, month(pdate) mm, mp vendor, abs(sum(amt)) val FROM manualposts WHERE (mp IS NOT NULL AND mp != '') GROUP BY yy, mm, vendor";
            break;
            case 1:
            default:
                $colname="material";
                $sql = "SELECT date_format(pdate, '%Y-%m') yymm, year(pdate) yy, month(pdate) mm, material, abs(sum(amt)) val FROM manualposts WHERE (material IS NOT NULL AND material != '') GROUP BY yy, mm, material";
        }
        $QR = $con->query($sql);
        while($row = $QR->fetch_assoc()) {
            if( !isset( $monthlyexp[$row["yymm"]]) ) {
                $monthlyexp[$row["yymm"]] = array(
                    $row[$colname] => $row["val"]
                );
            } elseif( !isset( $monthlyexp[$row["yymm"]][$row[$colname]])) {
                $monthlyexp[$row["yymm"]][$row[$colname]] = $row["val"];
            }
            if( !in_array( $row[$colname], $expenses) ) {
                $expenses[] = $row[$colname] ;
            }
        }
        
        $cols = [];
        // formating data for google chart 
        $col = array("id"=>"","label"=>"Y-M","pattern"=>"","type"=>"string");
        $cols[] = $col;
        foreach($expenses as $v) {
            $col["label"] = $v;
            $col["type"] = "number";
            $cols[] = $col;
        }
        foreach($monthlyexp as $ym=>$v){
            $c = [];
            $c[]= array("v"=>$ym, "f"=>null);
            foreach($expenses as $exp ) {
                $vv = ($v[$exp]? $v[$exp]:0);
                $c[]= array("v"=>$vv, "f"=>null);
            }
            $rows[] = array("c"=>$c);
        }
    break;

    case "stock":
        if(!$_SERVER['HTTP_COOKIE']){ exit; }
        $cookies = getCookies();

        $sql = "SELECT DATE_FORMAT(pdate, '%Y-%m-%dT%TZ') pdate, total_invest, total_actual_amount, safe_actual_amount, risky_actual_amount FROM mystock WHERE sid = '".$cookies['smid']."' ORDER BY pdate";
        $QR = $con->query($sql);
        while($row = $QR->fetch_assoc()) { $rows[] = $row; }

        // data types : date, number, string for google chart
        $cols = array(
            "Date"=>"date",
            "total_invest"=>"number",
            "total"=>"number",
            "safe"=>"number",
            "risky"=>"number"
        );
}  
$data["cols"] = $cols;
$data["rows"] = $rows;
// Instead you can query your database and parse into JSON etc
echo json_encode($data);        
?>