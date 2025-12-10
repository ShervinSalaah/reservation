<?php
session_start();

$values = [
    'name' => "",
    'indate' => "",
    'outdate' => "",
    'hotel' => "",
    'room' => "",
    'board' => "",
    'activities' => [],
    'hours' => []
];

function test_input($data){
    return htmlspecialchars(stripslashes(trim($data)));
}

if($_SERVER["REQUEST_METHOD"] === "POST"){
    foreach($values as $key => $v){
        if(isset($_POST[$key])){
            $values[$key] = $_POST[$key];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hotel Reservation</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-container">

<h1 class="title">🌴 Vacation Resort Reservation</h1>
<p class="subtitle">Plan your perfect getaway</p>

<form method="post" action="charges.php">

<div class="field">
    <label>Customer Name</label>
    <input type="text" name="name" required>
</div>

<div class="field">
    <label>Check-in Date</label>
    <input type="date" name="indate" required>
</div>

<div class="field">
    <label>Check-out Date</label>
    <input type="date" name="outdate" required>
</div>

<div class="field">
    <label>Hotel</label>
    <select name="hotel" required>
        <option value="">Select Hotel</option>
        <?php
        $hotels = ['Riverside hotel', 'Lagoon view hotel', 'Nature Villa', 'Beach Resort'];
        foreach($hotels as $hotel){
            echo "<option value='$hotel'>$hotel</option>";
        }
        ?>
    </select>
</div>

<div class="field">
    <label>Room Type</label><br>
    <?php 
    $rooms = ['Standard Double', 'Deluxe Twin Room', 'Executive Suite'];
    foreach($rooms as $room){
        echo "<label class='radio'><input type='radio' name='room' value='$room' required> $room</label><br>";
    }
    ?>
</div>

<h3 class="section-title">Activities</h3>

<table class="activity-table">
    <tr><th>Activity</th><th>Hours</th></tr>

<?php
$activities = ['Spa', 'Cycling', 'Swimming', 'Gym'];
foreach($activities as $act){
    echo "
    <tr>
        <td><label><input type='checkbox' name='activities[]' value='$act'> $act</label></td>
        <td><input type='number' name='hours[$act]' min='0' value='0'></td>
    </tr>";
}
?>
</table>

<h3 class="section-title">Board Type</h3>

<label class="radio"><input type="radio" name="board" value="half-board" required> Half Board</label>
<label class="radio"><input type="radio" name="board" value="full-board"> Full Board</label>

<button type="submit" class="btn">Generate Receipt</button>

</form>

</div>

</body>
</html>
