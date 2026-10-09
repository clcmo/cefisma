<?php
/**
 * Template fino: o WordPress escolhe o arquivo, o Controller faz o resto.
 */
use Cefisma\Controllers\HomeController;

Cefisma\Core\Theme::dispatch(HomeController::class, 'index');
