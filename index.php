<?php

include __DIR__ . '/config.php';
include __DIR__ . '/src/database.php';
include __DIR__ . '/src/functions.php';

$view = new View();
echo $view->render(__DIR__ . '/src/main.php');