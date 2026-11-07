<?php
/**
 * Template Name: Participe
 * Template Post Type: page
 * Template fino: o Controller monta as formas de participação.
 */
use Cefisma\Controllers\PaginaController;

Cefisma\Core\Theme::dispatch(PaginaController::class, 'participe');
