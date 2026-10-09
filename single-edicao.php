<?php
/**
 * Template fino: o WordPress escolhe o arquivo, o Controller faz o resto.
 */
use Cefisma\Controllers\SupernovaController;

Cefisma\Core\Theme::dispatch(SupernovaController::class, 'edicao');
