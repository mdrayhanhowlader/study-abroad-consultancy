<?php
require_once __DIR__ . '/../app/bootstrap.php';
App\Auth::logout();
redirect('/admin/login.php');
