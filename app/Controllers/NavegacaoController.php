<?php
declare(strict_types=1);

namespace Cefisma\Controllers;

use Cefisma\Core\Controller;
use Cefisma\Models\Edicao;
use Cefisma\Models\Produto;

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
        $slug = sanitize_title(remove_accents(wp_strip_all_tags($item->title)));
        $destino = $this->destinoPagina($slug);
        $atual = !empty($item->current);

        if ($destino !== null) {
            $atts['href'] = $destino;
            if (in_array($slug, ['supernova', 'boletim', 'boletim-supernova', 'folhetim', 'folhetim-supernova'], true)) {
                $atual = is_post_type_archive(Edicao::TIPO) || is_singular(Edicao::TIPO) || get_query_var('cefisma_serie') === 'folhetim';
            } else {
                $destinoPath = trailingslashit((string) wp_parse_url($destino, PHP_URL_PATH));
                $requisicaoPath = trailingslashit((string) wp_parse_url(
                    isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '/',
                    PHP_URL_PATH
                ));
                $atual = $destinoPath === $requisicaoPath;
            }
        }

        if ($atual) {
            $atts['aria-current'] = 'page';
        } else {
            unset($atts['aria-current']);
        }
        return $atts;
    }

    private function destinoPagina(string $slug): ?string
    {
        $paginas = [
            'inicio'      => home_url('/'),
            'quem-somos'  => home_url('/quem-somos/'),
            'comunicacao' => home_url('/comunicacao/'),
            'participe'   => home_url('/participe/'),
            'boletim'     => (string) get_post_type_archive_link(Edicao::TIPO),
            'boletim-supernova' => (string) get_post_type_archive_link(Edicao::TIPO),
            'folhetim'    => home_url('/supernova/folhetim/'),
            'folhetim-supernova' => home_url('/supernova/folhetim/'),
            'loja'        => Produto::urlLoja(),
        ];

        if ($slug === 'supernova') {
            return (string) get_post_type_archive_link(Edicao::TIPO);
        }

        return $paginas[$slug] ?? null;
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
