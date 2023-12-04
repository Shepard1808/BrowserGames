<?php

namespace BrowserGames;

require '../vendor/autoload.php';

use Fbartz\BrowserGames\Service\Database;

$dbData = parse_ini_file("setup.ini",false,INI_SCANNER_RAW);

$database = Database::getInstance();
$database::initConnection($dbData);