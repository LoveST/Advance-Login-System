<?php


if (!isset($_GET['keyword'])) {
    die("");
}

include "coin_calculator.php";

$weight = $_GET['keyword'];
$weightType = $_GET['weightType'];
$calculate = $_GET['calculate'];
$calc = new Coin_Calculator($weight, $weightType, $calculate);
$array = Array();
array_push($array, Array("weight" => $calc->getCoinsWeight(), "worth" => $calc->getAmount(), "quantity" => $calc->getCoinsInHand()));
echo json_encode($array);
