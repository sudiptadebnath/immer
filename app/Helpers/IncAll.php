<?php

// Automatically include all PHP helper files from app/Helpers
foreach (glob(__DIR__ . '/*.php') as $file) {
    if ($file !== __FILE__) {
        require_once $file;
    }
}
