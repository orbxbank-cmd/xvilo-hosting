<?php
require __DIR__ . '/../../core/Auth.php';
Auth::logout();
header('Location: /');
