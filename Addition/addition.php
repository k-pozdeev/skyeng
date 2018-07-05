<?php

/**
 * Цифра числа в позиции pos
 *
 * @param string $val
 * @param int $pos -- отрицательное число, позиция -1 соответствует последней цифре числа
 * @return int
 */
function digitAtPos(string $val, int $pos): int {
    return strlen($val) >= abs($pos) ? (int) $val[strlen($val) - abs($pos)] : 0;
};

/**
 * Функция сложения двух неотрицательных целых чисел
 *
 * @param string $val1 - неотрицательное целое
 * @param string $val2 - неотрицательное целое
 * @return string
 * @throws Exception
 */
function sumPositive(string $val1, string $val2): string {
    assert(preg_match('/^0$|^[1-9][0-9]*$/', $val1));
    assert(preg_match('/^0$|^[1-9][0-9]*$/', $val2));

    $maxLen = max(strlen($val1), strlen($val2));
    $result = '';
    $store = 0;
    for ($pos = -1; abs($pos) <= $maxLen; $pos--) {
        $digit1 = digitAtPos($val1, $pos);
        $digit2 = digitAtPos($val2, $pos);
        $digitResult = $digit1 + $digit2 + $store;
        $result = (string) ($digitResult % 10) . $result;
        $store = $digitResult >= 10 ? 1 : 0;
    }
    if ($store) $result = '1' . $result;
    return $result;
}

/**
 * Возвращает 1, если число $val1 больше числа $val2.
 * Возвращает 2, если $val2 больше $val1.
 * Возвращает 0, если числа равны.
 *
 * @param string $val1 - неотрицательное целое
 * @param string $val2 - неотрицательное целое
 * @return int
 */
function absGreater(string $val1, string $val2): int {
    assert(preg_match('/^0$|^[1-9][0-9]*$/', $val1));
    assert(preg_match('/^0$|^[1-9][0-9]*$/', $val2));

    if (strlen($val1) > strlen($val2)) return 1;
    if (strlen($val2) > strlen($val1)) return 2;
    for ($pos = 0; $pos < strlen($val1); $pos++) {
        if ($val1[$pos] > $val2[$pos]) return 1;
        if ($val2[$pos] > $val1[$pos]) return 2;
    }
    return 0;
}

/**
 * Вычитает второе число из первого.
 *
 * @param string $val1 - неотрицательное целое
 * @param string $val2 - неотрицательное целое, меньшее первого
 * @return string
 */
function subtractPositive(string $val1, string $val2): string {
    assert(preg_match('/^0$|^[1-9][0-9]*$/', $val1));
    assert(preg_match('/^0$|^[1-9][0-9]*$/', $val2));
    assert(absGreater($val1, $val2) == 1);

    $result = '';
    $store = 0;
    for ($pos = -1; abs($pos) <= strlen($val1); $pos--) {
        $digit1 = digitAtPos($val1, $pos);
        $digit2 = digitAtPos($val2, $pos);
        $digitResult = $digit1 - $digit2 + $store;
        if ($digitResult < 0) {
            $result = (string) (10 + $digitResult) . $result;
            $store = -1;
        }
        else {
            $result = (string) $digitResult . $result;
            $store = 0;
        }
    }
    return ltrim($result, '0');
}

/**
 * Функция складывает целые десятичные числа, представленные в строковом выражении.
 * Искомая функция по ТЗ.
 *
 * @param string $val1
 * @param string $val2
 * @return string
 * @throws Exception
 */
function sum(string $val1, string $val2): string {
    if (!preg_match('/^0$|^-?[1-9][0-9]*$/', $val1)) {
        throw new Exception("Invalid value 1");
    }
    if (!preg_match('/^0$|^-?[1-9][0-9]*$/', $val2)) {
        throw new Exception("Invalid value 2");
    }

    $val1Negative = $val1[0] == '-';
    if ($val1Negative) $val1 = substr($val1, 1);

    $val2Negative = $val2[0] == '-';
    if ($val2Negative) $val2 = substr($val2, 1);

    if ($val1Negative == false && $val2Negative == false) {
        return sumPositive($val1, $val2);
    }
    elseif ($val1Negative && $val2Negative) {
        return '-' . sumPositive($val1, $val2);
    }
    else {
        switch (absGreater($val1, $val2)) {
            case 1:
                return ($val1Negative ? '-' : '') . subtractPositive($val1, $val2);
                break;
            case 2:
                return ($val2Negative ? '-' : '') . subtractPositive($val2, $val1);
                break;
            case 0:
                return '0';
        }
    }
}

for ($i = 0; $i < 100; $i++) {
    $a = rand(-1000000000, 1000000000);
    $b = rand(-1000000000, 1000000000);
    echo $a . '; ' . $b . '; ' . ($a + $b) . '; ' . sum($a, $b) . '; ' . (($a + $b == sum($a, $b)) ? 'y' : 'n') . "\n";
}

echo sum("12", "-100000000000000000000000000000000000000000000000000000000000000000000000000");