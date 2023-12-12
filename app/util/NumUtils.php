<?php
function numOrDefault($number, int $default): int {
    return filter_var($number, FILTER_VALIDATE_INT) !== false ? $number : $default;
}
