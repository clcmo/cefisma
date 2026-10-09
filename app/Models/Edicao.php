<?php
declare(strict_types=1);

namespace Cefisma\Models;

use Cefisma\Core\Campos;

/**
 * Edição do Boletim Supernova (CPT "edicao").
 * É a ponte entre o boletim em LaTeX e o site: o pipeline de build
 * publica PDF, capa e sumário pela API (EdicaoApiController).
 */
final class Edicao
{
    public const TIPO = 'edicao';

    public static function register(): void
    {
        add_action('init', [self::class, 'registrar']);
    }

    /** Campos meta: chave => [rótulo, tipo]. Usado pelo admin e pela API. */
    public static function campos(): array
    {
        return [
            'numero'    => ['Número da edição', 'number'],
            'pdf_url'   => ['URL do PDF', 'url'],
            'capa_url'  => ['URL da capa (usada se não houver imagem destacada)', 'url'],
            'sumario'   => ['Sumário: uma seção por linha, no formato Título | URL', 'linhas'],
        ];
    }

    public static function registrar(): void
    {
        register_post_type(self::TIPO, [
            'labels' => [
                'name'          => 'Edições do Supernova',
                'singular_name' => 'Edição',
                'add_new_item'  => 'Adicionar edição',
                'edit_item'     => 'Editar edição',
                'menu_name'     => 'Supernova',
            ],
            'public'       => true,
            'has_archive'  => 'supernova/boletim',
            'rewrite'      => ['slug' => 'supernova/boletim', 'with_front' => false],
            'menu_icon'    => 'dashicons-star-filled',
            'supports'     => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
            'show_in_rest' => true,
        ]);

        foreach (self::campos() as $chave => [, $tipo]) {
            register_post_meta(self::TIPO, $chave, [
                'single'            => true,
                'type'              => $tipo === 'number' ? 'integer' : ($tipo === 'linhas' ? 'array' : 'string'),
                'show_in_rest'      => $tipo === 'linhas'
                    ? ['schema' => ['type' => 'array', 'items' => ['type' => 'object', 'properties' => [
                        'titulo' => ['type' => 'string'], 'url' => ['type' => 'string'],
                    ]]]]
                    : true,
                'sanitize_callback' => static fn($v) => Campos::sanitizar($tipo, $v),
                'auth_callback'     => static fn() => current_user_can('edit_posts'),
            ]);
        }
    }

    /* ---------- Consultas ---------- */

    public static function ultima(): ?array
    {
        $lista = self::todas(1);
        return $lista[0] ?? null;
    }

    /** @return array[] da mais recente para a mais antiga */
    public static function todas(int $limite = -1, int $pagina = 1): array
    {
        $consulta = new \WP_Query([
            'post_type'           => self::TIPO,
            'posts_per_page'      => $limite,
            'paged'               => $pagina,
            'orderby'             => 'date',
            'order'               => 'DESC',
            'no_found_rows'       => true,
            'ignore_sticky_posts' => true,
        ]);
        return array_map([self::class, 'desdePost'], $consulta->posts);
    }

    /** Anterior e próxima edição em relação a $post (por data). */
    public static function vizinhas(\WP_Post $post): array
    {
        $anterior = get_adjacent_post(false, '', true);
        $proxima  = get_adjacent_post(false, '', false);
        return [
            'anterior' => $anterior instanceof \WP_Post ? self::desdePost($anterior) : null,
            'proxima'  => $proxima instanceof \WP_Post ? self::desdePost($proxima) : null,
        ];
    }

    public static function desdePost(\WP_Post $p): array
    {
        $numero = (int) get_post_meta($p->ID, 'numero', true);
        $sumario = get_post_meta($p->ID, 'sumario', true);
        $capa = get_the_post_thumbnail_url($p, 'capa');
        if (!$capa) {
            $capa = (string) get_post_meta($p->ID, 'capa_url', true);
        }
        return [
            'id'      => $p->ID,
            'numero'  => $numero,
            'titulo'  => get_the_title($p),
            'resumo'  => has_excerpt($p) ? get_the_excerpt($p) : '',
            'data'    => get_the_date('j \d\e F \d\e Y', $p),
            'iso'     => get_post_time('c', false, $p),
            'url'     => get_permalink($p),
            'pdf'     => (string) get_post_meta($p->ID, 'pdf_url', true),
            'capa'    => $capa ?: '',
            'sumario' => is_array($sumario) ? $sumario : [],
        ];
    }

    /* ---------- Escrita (usada pela API) ---------- */

    /**
     * Cria ou atualiza a edição pelo número. Idempotente: rodar o pipeline
     * duas vezes para o mesmo número atualiza a mesma edição.
     *
     * @return int|\WP_Error ID do post
     */
    public static function salvarPorNumero(array $d)
    {
        $numero = absint($d['numero'] ?? 0);
        if ($numero < 1) {
            return new \WP_Error('cefisma_numero', 'Informe o número da edição.', ['status' => 400]);
        }

        $existentes = get_posts([
            'post_type'      => self::TIPO,
            'post_status'    => 'any',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'meta_key'       => 'numero',
            'meta_value'     => $numero,
        ]);
        $id = $existentes[0] ?? 0;

        $post = [
            'post_type'    => self::TIPO,
            'post_status'  => 'publish',
            'post_title'   => sanitize_text_field($d['titulo'] ?? "Supernova #{$numero}"),
            'post_excerpt' => sanitize_textarea_field($d['resumo'] ?? ''),
        ];
        if (!empty($d['data'])) {
            $ts = strtotime((string) $d['data']);
            if ($ts) {
                $post['post_date'] = wp_date('Y-m-d H:i:s', $ts);
            }
        }
        if ($id) {
            $post['ID'] = $id;
            $resultado = wp_update_post($post, true);
        } else {
            $resultado = wp_insert_post($post, true);
        }
        if (is_wp_error($resultado)) {
            return $resultado;
        }

        update_post_meta($resultado, 'numero', $numero);
        foreach (self::campos() as $chave => [, $tipo]) {
            if ($chave !== 'numero' && array_key_exists($chave, $d)) {
                update_post_meta($resultado, $chave, Campos::sanitizar($tipo, $d[$chave]));
            }
        }
        return (int) $resultado;
    }
}
