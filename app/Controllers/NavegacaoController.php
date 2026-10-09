<?php
declare(strict_types=1);

namespace Cefisma\Controllers;

use Cefisma\Core\Controller;

/** Ajusta o HTML dos menus do WordPress para casar com o CSS do tema. */
final class NavegacaoController extends Controller
{
    public function register(): void
    {
        add_filter('nav_menu_link_attributes', [$this, 'ariaCurrent'], 10, 3);
        add_filter('nav_menu_css_class', [$this, 'classeLoja'], 10, 3);
    }

    /** Marca o item atual com aria-current="page" (o CSS usa esse atributo). */
    public function ariaCurrent(array $atts, $item, $args): array
    {
        if (!empty($item->current)) {
            $atts['aria-current'] = 'page';
        }
        return $atts;
    }

    /** Dá a classe "menu-loja" ao item que aponta para a página da loja. */
    public function classeLoja(array $classes, $item, $args): array
    {
        if (function_exists('wc_get_page_id') && (int) $item->object_id === (int) wc_get_page_id('shop')) {
            $classes[] = 'menu-loja';
        }
        return $classes;
    }
}
