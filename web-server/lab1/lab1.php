<?php

//2 варіант

// task 1
function factorial($n) {
    return $n <= 1 ? 1 : $n * factorial($n - 1);
}

$numbers = [];
for ($i = 0; $i < 5; $i++) {
    $numbers[] = rand(1, 10);
}

$factorials = array_map('factorial', $numbers);

echo "Початковий масив: " . implode(', ', $numbers) . "\n";
echo "Масив факторіалів: " . implode(', ', $factorials) . "\n\n";

// task 2
$numbers2 = [];
for ($i = 0; $i < 30; $i++) {
    $numbers2[] = rand(1, 100);
}

$sum2 = 0;
foreach ($numbers2 as $num) {
    if ($num % 3 == 0 && $num % 5 == 0) {
        $sum2 += $num;
    }
}

echo "Згенерований масив: " . implode(', ', $numbers2) . "\n";
echo "Сума чисел кратних 3 і 5: " . $sum2 . "\n\n";

// task 3
$input = readline("Введіть числа через пробіл: ");
$inputArray = explode(' ', $input);

$maxValue = max($inputArray);

echo "Введений масив: " . implode(', ', $inputArray) . "\n";
echo "Найбільше значення: " . $maxValue . "\n\n";

// task 4
function isPrime($num) {
    for ($i = 2; $i < $num; $i++) {
        if ($num % $i == 0) {
            return false;
        }
    }
    return true;
}

$numbers4 = [];
for ($i = 0; $i < 20; $i++) {
    $numbers4[] = rand(10, 100);
}

$primeCount = 0;
foreach ($numbers4 as $num) {
    if (isPrime($num)) {
        $primeCount++;
    }
}

echo "Масив чисел: " . implode(', ', $numbers4) . "\n";
echo "Кількість простих чисел: " . $primeCount . "\n\n";

// task 5
$numbers5 = [];
for ($i = 0; $i < 20; $i++) {
    $numbers5[] = rand(0, 30);
}

echo "Початковий масив: " . implode(', ', $numbers5) . "\n";

for ($i = 0; $i < count($numbers5); $i++) {
    if ($i % 2 == 0) {
        $numbers5[$i] = 0;
    }
}

echo "Модифікований масив: " . implode(', ', $numbers5) . "\n\n";

// task 6
$numbers6 = [];
for ($i = 0; $i < 12; $i++) {
    $numbers6[] = rand(-20, 20);
}

$sum6 = 0;
foreach ($numbers6 as $num) {
    if ($num % 3 == 0) {
        $sum6 += $num;
    }
}

echo "Масив елементів: " . implode(', ', $numbers6) . "\n";
echo "Сума елементів, кратних 3: " . $sum6 . "\n\n";

// task 7
$input = readline("Введіть прізвище та ім'я: ");

$parts = explode(' ', $input);

$lastName = $parts[0];
$initial = mb_substr($parts[1], 0, 1);

echo "Результат: $lastName $initial.\n\n";

// task 8
$input = readline("Введіть роки через пробіл: ");
$years = explode(' ', $input);

$leapYears = [];

foreach ($years as $year) {
    if (($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0)) {
        $leapYears[] = $year;
    }
}

if (!empty($leapYears)) {
    echo "Найменший високосний рік: " . min($leapYears) . "\n\n";
} else {
    echo "Високосних років не знайдено.\n\n";
}
// task 9
$arr9 = [];
for ($i = 0; $i < 10; $i++) {
    $arr9[] = rand(1, 100);
}

echo "Масив до обміну: " . implode(', ', $arr9) . "\n";

$minIndex = array_search(min($arr9), $arr9);
$maxIndex = array_search(max($arr9), $arr9);

$temp = $arr9[$minIndex];
$arr9[$minIndex] = $arr9[$maxIndex];
$arr9[$maxIndex] = $temp;

echo "Масив після обміну: " . implode(', ', $arr9) . "\n\n";

// task 10
$n = 5;
$totalSum = 0;

echo "Натуральне число n = " . $n . "\n";
echo "Квадрати чисел ряду:\n";

for ($i = 1; $i <= $n; $i++) {
    $square = $i * $i;
    echo $i . "^2 = " . $square . "\n";
    $totalSum += $square;
}

echo "Сума квадратів усіх чисел ряду: " . $totalSum . "\n";