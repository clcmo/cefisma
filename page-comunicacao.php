<?php
/**
 * Template Name: Comunicação
 * Template Post Type: page
 * Template fino: o Controller monta os informes e o layout do tema.
 */
use Cefisma\Controllers\PaginaController;

Cefisma\Core\Theme::dispatch(PaginaController::class, 'comunicacao');
