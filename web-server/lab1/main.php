<?php
    // 1
    $number = readline("write number: ");

    $sum = 0;

    for ($i = 0; $i < strlen($number); $i++) {
        $sum += $number[$i];
    }

    echo "Sum: $sum\n";


    //2
    $number = "442158755745";
    $search = "5";
    $count = 0;

    for ($i = 0; $i < strlen($number); $i++) {
        if ($number[$i] == $search) {
            $count++;
        }
    }
    echo "Count: $count\n";

    //3
    $sum = 0;

    for ($i = 20; $i <= 45; $i++) {
        if (fmod($i, 5) == 0) {
            $sum += $i;
            echo "$i ";
        }
    }

    echo "Sum: $sum\n";

    //4
    $arr = [];
    for ($i = 0; $i < 10; $i++) {
        $arr[] = rand(1, 100);
    }

    echo "Original: " . implode(", ", $arr) . "\n";

    $minIdx = array_search(min($arr), $arr);
    $maxIdx = array_search(max($arr), $arr);

    $temp = $arr[$minIdx];
    $arr[$minIdx] = $arr[$maxIdx];
    $arr[$maxIdx] = $temp;

    echo "After replacement: " . implode(", ", $arr) . "\n";

    //5
    $num = 7;
    for ($i = 1; $i <= 10; $i++) {
        echo "$num x $i = " . ($num * $i) . "\n";
    }

    //6
    $str = "Ostroh";
    $reversed = "";

    for ($i = strlen($str) - 1; $i >= 0; $i--) {
        $reversed .= $str[$i];
    }

    echo $reversed . "\n";

    //7
    $numbers = [];
    for ($i = 0; $i < 20; $i++) {
        $numbers[] = rand(1, 10);
    }

    echo "Array: " . implode(", ", $numbers) . "\n";

    $counts = array_count_values($numbers);
    echo "Uniq: ";

    foreach ($counts as $num => $occurrence) {
        if ($occurrence == 1) {
            echo "$num ";
        }
    }
    echo "\n";

    //8
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $password = "";

    for ($i = 0; $i < 8; $i++) {
        $index = rand(0, strlen($chars) - 1);
        $password .= $chars[$index];
    }

    echo "Ваш пароль: $password";
?>