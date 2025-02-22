<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment("Cookies. That's what I think about - cookies.");
})->purpose('Display an inspiring quote');
