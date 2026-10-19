<?php

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/AuthController.php';

AuthController::logout();
header('Location: login.php?salida=1');
exit;
