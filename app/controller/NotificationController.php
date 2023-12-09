<?php

namespace controller;
use enum\NotificationType;

require_once 'app/enum/NotificationType.php';

class NotificationController
{
    public static function setNotification(NotificationType $notification, string $message): void
    {
        $_SESSION['notification'] = array($notification, $message, false);
    }

    public static function setNotificationSeen(): void
    {
        $_SESSION['notification'][2] = true;
    }

    public static function getNotificationText(): string
    {
        return isset($_SESSION['notification']) ? $_SESSION['notification'][1] : '';
    }
}