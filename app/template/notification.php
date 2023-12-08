<?php

use controller\NotificationController;
use enum\NotificationType;

$successHidden = 'hidden';
$errorHidden = 'hidden';
$warningHidden = 'hidden';
$infoHidden = 'hidden';
if (isset($_SESSION['notification']) && $_SESSION['notification'][2] === false) {
    switch($_SESSION['notification'][0]) {
        case NotificationType::SUCCESS:
            $successHidden = '';
            break;
        case NotificationType::ERROR:
            $errorHidden = '';
            break;
        case NotificationType::WARNING:
            $warningHidden = '';
            break;
        case NotificationType::INFO:
            $infoHidden = '';
            break;
    }
    NotificationController::setNotificationSeen();
}
?>

<div class="alert <?php echo $errorHidden; ?>">
    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
    <strong><?php echo $_SESSION['notification'][1]; ?></strong>
</div>
<div class="alert success <?php echo $successHidden; ?>">
    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
    <strong><?php echo $_SESSION['notification'][1]; ?></strong>
</div>
<div class="alert info <?php echo $infoHidden; ?>">
    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
    <strong><?php echo $_SESSION['notification'][1]; ?></strong>
</div>
<div class="alert warning <?php echo $warningHidden; ?>">
    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
    <strong><?php echo $_SESSION['notification'][1]; ?></strong>
</div>