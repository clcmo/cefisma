<?php
/**
 * Template Name: Supernova
 * Template Post Type: page
 * Template fino: o Controller monta a entrada das séries do Supernova.
 */
use Cefisma\Controllers\SupernovaController;

Cefisma\Core\Theme::dispatch(SupernovaController::class, 'inicio');
