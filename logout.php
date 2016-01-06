<?php
require_once 'config.php';
require_once 'functions.php';

$_SESSION = array();
session_regenerate_id(true);

$_SESSION['logout'] = '<p class="success">You have been logged out.</p>';

redirect('login.php');
?>