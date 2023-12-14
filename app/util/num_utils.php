<?php
function numOrDefault($number, int $default): int {
    return filter_var($number, FILTER_VALIDATE_INT) !== false ? $number : $default;
}

function percentage($divided, $divisor): float {
    return round($divided / $divisor * 100, 2);
}
