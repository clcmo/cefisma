<?php
declare(strict_types=1);

namespace Cefisma\Models;

/**
 * Camada de acesso à loja (WooCommerce). Entrega arrays simples às Views
 * e funciona (devolvendo listas vazias) se o WooCommerce não estiver ativo.
 */
final class Produto
{
    /** Slug da categoria de produtos ligados ao Supernova (zine impressa, PDFs, etc.). */
    public const CATEGORIA_SUPERNOVA = 'supernova';
    public const CATEGORIA_BLUSAS = 'camiseta';

    public static function ativa(): bool
    {
        return class_exists('WooCommerce');
    }

    public static function destaques(int $limite = 4): array
    {
        if (!self::ativa()) {
            return [];
        }
        $produtos = wc_get_products(['status' => 'publish', 'featured' => true, 'limit' => $limite]);
        if (count($produtos) < $limite) {
            $produtos = wc_get_products(['status' => 'publish', 'limit' => $limite, 'orderby' => 'date', 'order' => 'DESC']);
        }
        return array_map([self::class, 'dados'], $produtos);
    }

    public static function blusas(int $limite = 4): array
    {
        if (!self::ativa()) {
            return [];
        }
        return array_map([self::class, 'dados'], wc_get_products([
            'status'       => 'publish',
            'stock_status' => 'instock',
            'category'     => [self::CATEGORIA_BLUSAS],
            'limit'        => $limite,
            'orderby'      => 'date',
            'order'        => 'DESC',
        ]));
    }

    public static function porCategoria(string $slug, int $limite = 4): array
    {
        if (!self::ativa()) {
            return [];
        }
        return array_map([self::class, 'dados'], wc_get_products([
            'status'   => 'publish',
            'category' => [$slug],
            'limit'    => $limite,
        ]));
    }

    public static function urlLoja(): string
    {
        return self::ativa() ? (string) wc_get_page_permalink('shop') : home_url('/loja/');
    }

    /** Contagem e link do carrinho para o topo do site. */
    public static function resumoCarrinho(): array
    {
        if (!self::ativa() || !function_exists('WC') || !WC()->cart) {
            return ['ativo' => false, 'qtd' => 0, 'url' => ''];
        }
        return ['ativo' => true, 'qtd' => (int) WC()->cart->get_cart_contents_count(), 'url' => wc_get_cart_url()];
    }

    private static function dados(\WC_Product $p): array
    {
        return [
            'id'          => $p->get_id(),
            'nome'        => $p->get_name(),
            'url'         => $p->get_permalink(),
            'preco_html'  => $p->get_price_html(),
            'imagem'      => $p->get_image('woocommerce_thumbnail'),
            'compra_url'  => $p->add_to_cart_url(),
            'compra_txt'  => $p->add_to_cart_text(),
            'compravel'   => $p->is_purchasable() && $p->is_in_stock(),
            'ajax'        => $p->supports('ajax_add_to_cart') && $p->is_type('simple'),
        ];
    }
}
