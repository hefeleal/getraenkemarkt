<?php
$ALLOW_HELPER = true;
require_once("helpers.php");

$pfand = $_POST['pfand'];
$order = $_POST['order'];
$total_sum = $_POST['totalSum'];
$selected_kitchen = $_POST['selectedKitchen'];


if(!empty($_POST['pin'])){
    $pin = $_POST["pin"];
    $remember = $_POST["remember"];
}
else if(!empty($_COOKIE['pin'])){
    $pinhash = $_COOKIE['pin'];
    $remember = "1";
}
else{
    echo '{"status": 1, "msg": "Nicht authentisiert."}';
    return;
}
sleep(1);

if((!empty($order) || !empty($pfand)) && !empty($selected_kitchen)){
    $config = parse_ini_file("data/config.ini");
    if($pinhash == hash("sha256", $config["getraenkemarkt_pw"].$config["cookie_secret"]) || password_verify($pin, $config["getraenkemarkt_pw"])){
        $current_time = date("d.m.Y H:i:s");
        if($remember == "1"){
            setcookie("pin", hash("sha256", $config["getraenkemarkt_pw"].$config["cookie_secret"]), 2147483647);
        }
        $ret = new_order($selected_kitchen, $pfand, $order, $total_sum, $current_time);
        echo $ret;
    }
    else{
        echo '{"status": 2, "msg": "Falsches Passwort."}';
    }
}
else{
    echo '{"status": 1, "msg": "Fehlender Parameter."}';
}

function new_order($selected_kitchen, $pfand, $order, $total_sum, $current_time){
    $new_order = array(
        htmlspecialchars($current_time, ENT_QUOTES),
        htmlspecialchars($selected_kitchen, ENT_QUOTES),
        htmlspecialchars($pfand, ENT_QUOTES),
        htmlspecialchars($order, ENT_QUOTES),
        htmlspecialchars($total_sum, ENT_QUOTES)
    );

    $handle = fopen("data/order.csv", 'a');
    if($handle){
        fputcsv($handle, $new_order);
        fclose($handle);

        return '{"status": 0, "msg": "Ok, alles eingetragen."}';
    }
    return '{"status": 1, "msg": "Unbekannter Fehler."}';
}

?>