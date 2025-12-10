<?php
session_start();

function test_input($data){
    return htmlspecialchars(stripslashes(trim($data)));
}

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

if($_SERVER["REQUEST_METHOD"] === "POST"){
    foreach($values as $k => $v){
        if(isset($_POST[$k])){
            $values[$k] = test_input($_POST[$k]);
        } elseif($k === 'hours' && isset($_POST['hours'])) {
            $values['hours'] = $_POST['hours'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Royal Resorts — Reservation</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<main class="page">
    <header class="hero">
        <div class="hero-inner">
            <div class="logo">ROYAL<span class="accent">•</span>RESORT</div>
            <h1 class="hero-title">Reservation — Royal Suite Experience</h1>
            <p class="hero-sub">Luxurious stay- Flawlessly curated.</p>
        </div>
    </header>

    <section class="form-card">
        <h2 class="card-title">Make a Reservation</h2>

        <form method="post" action="charges.php" class="reservation-form" novalidate>
            <div class="form-row">
                <label>Customer Name</label>
                <input type="text" name="name" required value="<?php echo test_input($values['name']); ?>">
            </div>

            <div class="form-row split">
                <div>
                    <label>Check-in</label>
                    <input type="date" name="indate" required value="<?php echo test_input($values['indate']); ?>">
                </div>
                <div>
                    <label>Check-out</label>
                    <input type="date" name="outdate" required value="<?php echo test_input($values['outdate']); ?>">
                </div>
            </div>

            <div class="form-row">
                <label>Hotel</label>
                <select name="hotel" required>
                    <option value=""> — Select hotel — </option>
                    <?php
                    $hotels = ['Riverside hotel', 'Lagoon view hotel', 'Nature Villa', 'Beach Resort'];
                    foreach($hotels as $hotel){
                        $sel = ($values['hotel'] === $hotel) ? "selected" : "";
                        echo "<option class= 'resorts' value=\"".htmlspecialchars($hotel)."\" $sel>".htmlspecialchars($hotel)."</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-row">
                <label>Room Type</label>
                <div class="radios">
                    <?php
                    $rooms = ['Standard Double', 'Deluxe Twin Room', 'Executive Suite'];
                    foreach($rooms as $r){
                        $chk = ($values['room'] === $r) ? "checked" : "";
                        echo "<label class='radio-inline'><input type='radio' name='room' value='".htmlspecialchars($r)."' required $chk> $r</label>";
                    }
                    ?>
                </div>
            </div>

            <div class="form-row">
                <label>Activities (select and enter hours)</label>
                <table class="activity-table">
                    <thead><tr><th>Activity</th><th>Hours</th></tr></thead>
                    <tbody>
                    <?php
                    $activities = ['Spa','Cycling','Swimming','Gym'];
                    foreach($activities as $act){
                        $checked = in_array($act, $values['activities'] ?? []) ? "checked" : "";
                        $hourVal = isset($values['hours'][$act]) ? intval($values['hours'][$act]) : 0;
                        echo "<tr>
                                <td class='act-cell'>
                                  <label><input type='checkbox' name='activities[]' value='".htmlspecialchars($act)."' $checked> $act</label>
                                </td>
                                <td><input type='number' min='0' name='hours[".htmlspecialchars($act)."]' value='".htmlspecialchars($hourVal)."' class='hours-input'></td>
                              </tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>

            <div class="form-row">
                <label>Board Type</label>
                <div class="radios">
                    <label class="radio-inline"><input type="radio" name="board" value="half-board" required <?php if($values['board']=="half-board") echo "checked"; ?>> Half board</label>
                    <label class="radio-inline"><input type="radio" name="board" value="full-board" <?php if($values['board']=="full-board") echo "checked"; ?>> Full board</label>
                </div>
            </div>

            <div class="form-row actions">
                <button type="submit" class="btn-primary">View Charges & Receipt</button>
            </div>
        </form>
    </section>

    <footer class="footer">© <?php echo date('Y'); ?> Royal Resorts — For distinguished travelers</footer>
</main>

</body>
</html>
