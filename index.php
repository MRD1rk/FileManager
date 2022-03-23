<?php

include_once 'vendor/autoload.php';

use test\Generator;
use test\Cleaner;
$start = microtime(true);
$gen = new Generator();
$gen->setFileCount(6)
    ->setMaxDepth(6)
    ->setMaxLengthName(16)
    ->setProbability(50)
    ->setMaxFileSize('10M');
$gen->process();
$outputDir = __DIR__.DIRECTORY_SEPARATOR.'output';
$comparator = new \test\Comparator($outputDir);
$comparator->process();

$cleaner = new Cleaner();
$cleaner->cleanDir($outputDir);
echo 'Время выполнения скрипта: '.round(microtime(true) - $start, 4).' сек.';