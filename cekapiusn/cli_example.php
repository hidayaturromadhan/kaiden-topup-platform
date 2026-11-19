<?php

require __DIR__ . '/src/ApiGames.php';

$api = new \Aditdev\ApiGames();

echo "1. Mobile Legends (Modern check):\n";
$res1 = $api->check('MOBILE_LEGENDS', '604210151', '8425');
print_r($res1);

echo "\n2. Free Fire (Modern check):\n";
$res2 = $api->check('FREEFIRE', '116502997');
print_r($res2);

echo "\n3. Valorant (Modern check):\n";
$res3 = $api->check('VALORANT', 'Diezyyy#666');
print_r($res3);

echo "\n4. AU2 Mobile (Modern check):\n";
$res4 = $api->check('AU_TWO_MOBILE', '662311');
print_r($res4);

echo "\n5. Legacy Magic Method check (\$api->MOBILE_LEGENDS(...)):\n";
echo $api->MOBILE_LEGENDS('604210151', '8425') . PHP_EOL;
