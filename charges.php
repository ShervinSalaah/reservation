<?php
session_start();

$name = $_POST['name'];
$hotel = $_POST['hotel'];
$room = $_POST['room'];
$board = $_POST['board'];

$activities = $_POST['activities'] ?? [];
$hours = $_POST['hours'] ?? [];

$indate = new DateTime($_POST['indate']);
$outdate = new DateTime($_POST['outdate']);
$days = $indate->diff($outdate)->days;

// ROOM PRICES
$prices = [
    "Riverside hotel" => ["Standard Double"=>7500,"Deluxe Twin Room"=>8500,"Executive Suite"=>10000],
    "Lagoon view hotel" => ["Standard Double"=>8500,"Deluxe Twin Room"=>10000,"Executive Suite"=>12500],
    "Nature Villa" => ["Standard Double"=>10000,"Deluxe Twin Room"=>12500,"Executive Suite"=>15000],
    "Beach Resort" => ["Standard Double"=>12500,"Deluxe Twin Room"=>15000,"Executive Suite"=>20000],
];

$room_rate = $prices[$hotel][$room];
$room_charge = $room_rate * $days;

// ACTIVITY PRICES
$act_rate = ["Spa"=>5000,"Cycling"=>400,"Swimming"=>1000,"Gym"=>850];

$activity_total = 0;

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Reservation Receipt</title>
</head>
<body>

<div class="receipt">

<h1 class="receipt-title">Reservation Receipt</h1>

<p><strong>Customer Name:</strong> <?php echo $name; ?></p>

<table class="receipt-table">
    <tr><td>Hotel:</td><td><?php echo $hotel; ?></td><td></td></tr>
    <tr><td>Room Type:</td><td><?php echo $room; ?></td><td></td></tr>

    <tr>
        <td>Number of Days:</td>
        <td><?php echo $days; ?></td>
        <td class="amount"><?php echo number_format($room_charge); ?>.00</td>
    </tr>

    <tr>
        <td>Board Type:</td>
        <td><?php echo ucfirst($board); ?></td>
        <td class="amount">
            <?php 
            echo ($board=="full-board") ? "3500.00" : "0.00";
            ?>
        </td>
    </tr>

    <tr><th colspan="3" class="activity-header">Activities</th></tr>

    <?php foreach($activities as $act): 
        $cost = $act_rate[$act] * intval($hours[$act]);
        $activity_total += $cost;
    ?>
        <tr>
            <td><?php echo $act . " (" . $hours[$act] . "h)"; ?></td>
            <td></td>
            <td class="amount"><?php echo number_format($cost); ?>.00</td>
        </tr>
    <?php endforeach; ?>

    <tr class="total-row">
        <td><strong>Total</strong></td>
        <td></td>
        <td class="amount">
            <strong>
            <?php 
                $board_charge = ($board=="full-board") ? 3500 : 0;
                echo number_format($room_charge + $activity_total + $board_charge);
            ?>.00
            </strong>
        </td>
    </tr>

</table>

</div>

</body>
</html>
