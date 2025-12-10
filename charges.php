<?php
// charges.php
session_start();

function test_input($d){ return htmlspecialchars(stripslashes(trim($d))); }

// Basic retrieval (with safe defaults)
$name = test_input($_POST['name'] ?? 'Guest');
$hotel = test_input($_POST['hotel'] ?? '');
$room  = test_input($_POST['room'] ?? '');
$board = test_input($_POST['board'] ?? 'half-board');
$activities = $_POST['activities'] ?? [];         // array of activity names
$hours = $_POST['hours'] ?? [];                   // associative: hours[Activity] => value

// Validate & parse dates
try {
    $indate = new DateTime(test_input($_POST['indate'] ?? ''));
    $outdate = new DateTime(test_input($_POST['outdate'] ?? ''));
} catch(Exception $e){
    // fallback to today +1
    $indate = new DateTime();
    $outdate = (new DateTime())->modify('+1 day');
}

// compute days: at least 1
$interval = $indate->diff($outdate);
$days = max(1, (int)$interval->days);

// Room prices (per day)
$prices = [
    "Riverside hotel" => ["Standard Double"=>7500,"Deluxe Twin Room"=>8500,"Executive Suite"=>10000],
    "Lagoon view hotel" => ["Standard Double"=>8500,"Deluxe Twin Room"=>10000,"Executive Suite"=>12500],
    "Nature Villa" => ["Standard Double"=>10000,"Deluxe Twin Room"=>12500,"Executive Suite"=>15000],
    "Beach Resort" => ["Standard Double"=>12500,"Deluxe Twin Room"=>15000,"Executive Suite"=>20000],
];

// Activity rates (per hour)
$act_rate = ["Spa"=>5000,"Cycling"=>400,"Swimming"=>1000,"Gym"=>850];

// compute room charge safely
$room_rate = 0;
if(isset($prices[$hotel]) && isset($prices[$hotel][$room])){
    $room_rate = $prices[$hotel][$room];
}
$room_charge = $room_rate * $days;

// board charge (flat fee)
$board_charge = ($board === "full-board") ? 3500 : 0;

// compute activity totals
$activity_items = [];
$activity_total = 0;
foreach($activities as $act){
    $act = test_input($act);
    $h = isset($hours[$act]) ? max(0,intval($hours[$act])) : 0;
    $rate = isset($act_rate[$act]) ? $act_rate[$act] : 0;
    $cost = $rate * $h;
    if($h > 0 && $rate > 0){
        $activity_items[] = ['name'=>$act,'hours'=>$h,'rate'=>$rate,'cost'=>$cost];
        $activity_total += $cost;
    } elseif($h > 0 && $rate === 0) {
        // unknown activity — show but cost 0
        $activity_items[] = ['name'=>$act,'hours'=>$h,'rate'=>0,'cost'=>0];
    }
}

$total = $room_charge + $board_charge + $activity_total;

function fmt($n){ return number_format($n,0, '.', ','); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reservation Receipt — Royal Resort</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="page">

    <header class="hero small">
        <div class="hero-inner">
            <div class="logo">ROYAL<span class="accent">•</span>RESORT</div>
            <h1 class="hero-title small">Reservation Receipt</h1>
        </div>
    </header>

    <section class="receipt-card">
        <div class="receipt-header">
            <div>
                <h2 class="receipt-customer"><?php echo test_input($name); ?></h2>
                <div class="muted">Reservation details & charges</div>
            </div>
            <div class="receipt-meta">
                <div><strong>Issued:</strong> <?php echo (new DateTime())->format('F j, Y'); ?></div>
                <div><strong>Stay:</strong> <?php echo htmlspecialchars($indate->format('Y-m-d'))." → ".htmlspecialchars($outdate->format('Y-m-d')); ?></div>
            </div>
        </div>

        <table class="receipt-table receipt-breakdown">
            <thead>
                <tr>
                    <th class="left">Description</th>
                    <th class="center">Details</th>
                    <th class="right">Charges (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Hotel</td>
                    <td><?php echo htmlspecialchars($hotel ?: '—'); ?></td>
                    <td class="right">—</td>
                </tr>

                <tr>
                    <td>Room Type</td>
                    <td><?php echo htmlspecialchars($room ?: '—'); ?></td>
                    <td class="right">—</td>
                </tr>

                <tr class="row-emphasis">
                    <td>Room charge (<?php echo $days; ?> day<?php echo $days>1?'s':''; ?>)</td>
                    <td><?php echo fmt($room_rate)." × ".$days; ?></td>
                    <td class="right"><?php echo fmt($room_charge); ?>.00</td>
                </tr>

                <tr>
                    <td>Board</td>
                    <td><?php echo ($board==='full-board') ? 'Full board (flat fee)' : 'Half board'; ?></td>
                    <td class="right"><?php echo ($board_charge>0) ? fmt($board_charge).'.00' : '0.00'; ?></td>
                </tr>

                <?php if(count($activity_items) > 0): ?>
                    <tr class="activity-header-row"><td colspan="3" class="center">Activities</td></tr>
                    <?php foreach($activity_items as $it): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($it['name']); ?> (<?php echo $it['hours']; ?>h)</td>
                            <td><?php echo fmt($it['rate'])." × ".$it['hours']; ?></td>
                            <td class="right"><?php echo fmt($it['cost']); ?>.00</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td>Activities</td>
                        <td>None selected or zero hours</td>
                        <td class="right">0.00</td>
                    </tr>
                <?php endif; ?>

                <tr class="total-row">
                    <td><strong>Total</strong></td>
                    <td></td>
                    <td class="right"><strong><?php echo fmt($total); ?>.00</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="receipt-note">
            <p><em>Thank you for choosing Royal Resort. We look forward to hosting you in luxurious comfort.</em></p>
        </div>

        <div class="receipt-actions">
            <a class="btn-primary" href="index.php">New Reservation</a>
        </div>
    </section>

    <footer class="footer">Royal Resorts • Excellence in hospitality</footer>
</main>
</body>
</html>
