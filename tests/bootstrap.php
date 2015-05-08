<?php
$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader->addPsr4('Mekras\\Speller\\Tests\\', __DIR__);

@date_default_timezone_set(date_default_timezone_get());
