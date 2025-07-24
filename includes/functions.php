<?php
function validarCuit($cuit) {
    if (!preg_match('/^\d{11}$/', $cuit)) {
        return false;
    }
    $weights = [5,4,3,2,7,6,5,4,3,2];
    $sum = 0;
    for ($i = 0; $i < 10; $i++) {
        $sum += $cuit[$i] * $weights[$i];
    }
    $mod = 11 - ($sum % 11);
    if ($mod == 11) {
        $mod = 0;
    } elseif ($mod == 10) {
        $mod = 9;
    }
    return $mod == $cuit[10];
}

