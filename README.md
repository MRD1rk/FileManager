# Тестовое задание
###Generator
Настройка генератора:
```
$gen = new test\Generator();
$gen->setFileCount(6)
    ->setMaxDepth(6)
    ->setMaxLengthName(16)
    ->setProbability(100)
    ->setMaxFileSize('10M');
```    
Запуск генератора:
```
$gen->process();
```
###Cleaner
```
$cleaner = new test\Cleaner();
$cleaner->cleanDir($outputDir);
```
###Comparator
```
$comparator = new test\Comparator($outputDir);
$comparator->process();
```