<?php

function clean($data) {
    return htmlspecialchars(trim($data));
}

function validPhone($number) {
    return preg_match('/^[0-9]{11}$/', $number);
}

function validYear($year) {
    $current = date('Y');
    return ($year >= 1950 && $year <= $current);
}

function calculateAge($dob) {
    return (new DateTime())->diff(new DateTime($dob))->y;
}
?>