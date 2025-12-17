<?php
require_once __DIR__ . '/config/session.php';

// Destroy session
session_destroy();

// Redirect ke login
header('Location: login.php');
exit;
