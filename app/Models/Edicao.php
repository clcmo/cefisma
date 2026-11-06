<?php
declare(strict_types=1);

namespace Cefisma\Models;

use Cefisma\Core\Campos;

/**
 * Edição do Boletim ou Folhetim Supernova (CPT "edicao").
 * É a ponte entre as publicações e o site: o pipeline de build
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
            'serie'     => ['Série', 'serie'],
            'data_publicacao' => ['Data como aparece no arquivo', 'text'],
            'pdf_url'   => ['URL do PDF', 'url'],
            'capa_url'  => ['URL da capa (usada se não houver imagem destacada)', 'url'],
            'origem_url'=> ['Link para a edição original (opcional)', 'url'],
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
            'rewrite'      => ['slug' => 'supernova/edicoes', 'with_front' => false],
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

        add_rewrite_rule(
            '^supernova/boletim/?$',
            'index.php?post_type=' . self::TIPO . '&cefisma_serie=boletim',
            'top'
        );
        add_rewrite_rule(
            '^supernova/folhetim/?$',
            'index.php?post_type=' . self::TIPO . '&cefisma_serie=folhetim',
            'top'
        );
        add_filter('query_vars', [self::class, 'queryVars']);
        add_filter('post_type_link', [self::class, 'permalink'], 10, 2);
        add_action('pre_get_posts', [self::class, 'filtrarSerie']);
    }

    public static function queryVars(array $vars): array
    {
        $vars[] = 'cefisma_serie';
        return $vars;
    }

    public static function permalink(string $permalink, \WP_Post $post): string
    {
        if ($post->post_type === self::TIPO) {
            return home_url('/supernova/edicoes/' . $post->post_name . '/');
        }
        return $permalink;
    }

    public static function filtrarSerie(\WP_Query $query): void
    {
        $serie = $query->get('cefisma_serie');
        if (!$query->is_main_query() || $query->get('post_type') !== self::TIPO || !in_array($serie, ['boletim', 'folhetim'], true)) {
            return;
        }

        if ($serie === 'folhetim') {
            $query->set('meta_query', [[
                'key'   => 'serie',
                'value' => 'folhetim',
            ]]);
        } else {
            $query->set('meta_query', [
                'relation' => 'OR',
                ['key' => 'serie', 'value' => 'boletim'],
                ['key' => 'serie', 'compare' => 'NOT EXISTS'],
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
    public static function todas(int $limite = -1, int $pagina = 1, string $serie = 'boletim'): array
    {
        $consulta = new \WP_Query([
            'post_type'           => self::TIPO,
            'posts_per_page'      => $limite,
            'paged'               => $pagina,
            'meta_key'            => 'numero',
            'orderby'             => 'meta_value_num',
            'order'               => 'DESC',
            'no_found_rows'       => true,
            'ignore_sticky_posts' => true,
            'meta_query'          => $serie === 'folhetim'
                ? [['key' => 'serie', 'value' => 'folhetim']]
                : [
                    'relation' => 'OR',
                    ['key' => 'serie', 'value' => 'boletim'],
                    ['key' => 'serie', 'compare' => 'NOT EXISTS'],
                ],
        ]);
        return array_map([self::class, 'desdePost'], $consulta->posts);
    }

    /** Edições anterior e próxima, limitadas à mesma série. */
    public static function vizinhas(\WP_Post $post): array
    {
        $serie = get_post_meta($post->ID, 'serie', true) === 'folhetim' ? 'folhetim' : 'boletim';
        $edicoes = get_posts([
            'post_type'      => self::TIPO,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_key'       => 'numero',
            'orderby'        => 'meta_value_num',
            'order'          => 'ASC',
            'meta_query'     => $serie === 'folhetim'
                ? [['key' => 'serie', 'value' => 'folhetim']]
                : [
                    'relation' => 'OR',
                    ['key' => 'serie', 'value' => 'boletim'],
                    ['key' => 'serie', 'compare' => 'NOT EXISTS'],
                ],
        ]);
        $ids = array_map(static function (\WP_Post $edition): int {
            return (int) $edition->ID;
        }, $edicoes);
        $posicao = array_search((int) $post->ID, $ids, true);
        $anterior = $posicao !== false && $posicao > 0 ? $edicoes[$posicao - 1] : null;
        $proxima = $posicao !== false && $posicao < count($edicoes) - 1 ? $edicoes[$posicao + 1] : null;

        return [
            'anterior' => $anterior instanceof \WP_Post ? self::desdePost($anterior) : null,
            'proxima'  => $proxima instanceof \WP_Post ? self::desdePost($proxima) : null,
        ];
    }

    public static function desdePost(\WP_Post $p): array
    {
        $numero = (int) get_post_meta($p->ID, 'numero', true);
        $sumario = get_post_meta($p->ID, 'sumario', true);
        $serie = get_post_meta($p->ID, 'serie', true);
        $capa = get_the_post_thumbnail_url($p, 'capa');
        if (!$capa) {
            $capa = (string) get_post_meta($p->ID, 'capa_url', true);
        }
        return [
            'id'      => $p->ID,
            'numero'  => $numero,
            'serie'   => $serie === 'folhetim' ? 'folhetim' : 'boletim',
            'titulo'  => get_the_title($p),
            'resumo'  => has_excerpt($p) ? get_the_excerpt($p) : '',
            'data'    => (string) get_post_meta($p->ID, 'data_publicacao', true) ?: get_the_date('j \d\e F \d\e Y', $p),
            'iso'     => get_post_time('c', false, $p),
            'url'     => get_permalink($p),
            'pdf'     => (string) get_post_meta($p->ID, 'pdf_url', true),
            'origem'  => (string) get_post_meta($p->ID, 'origem_url', true),
            'capa'    => $capa ?: '',
            'sumario' => is_array($sumario) ? $sumario : [],
        ];
    }

    /* ---------- Escrita (usada pela API) ---------- */

    /**
     * Cria ou atualiza a edição pela série e número.
     * Idempotente: rodar duas vezes atualiza o mesmo registro.
     *
     * @return int|\WP_Error ID do post
     */
    public static function salvarPorNumero(array $d)
    {
        $numero = absint($d['numero'] ?? 0);
        $serie = Campos::sanitizar('serie', $d['serie'] ?? 'boletim');
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
            'meta_query'     => $serie === 'folhetim'
                ? [['key' => 'serie', 'value' => 'folhetim']]
                : [
                    'relation' => 'OR',
                    ['key' => 'serie', 'value' => 'boletim'],
                    ['key' => 'serie', 'compare' => 'NOT EXISTS'],
                ],
        ]);
        $id = $existentes[0] ?? 0;

        $post = [
            'post_type'    => self::TIPO,
            'post_status'  => 'publish',
            'post_title'   => sanitize_text_field($d['titulo'] ?? "Supernova #{$numero}"),
            'post_excerpt' => sanitize_textarea_field($d['resumo'] ?? ''),
        ];
        if (!empty($d['slug'])) {
            $post['post_name'] = sanitize_title($d['slug']);
        }
        if (!empty($d['data'])) {
            $data = \DateTimeImmutable::createFromFormat('!Y-m-d', (string) $d['data'], wp_timezone());
            if ($data && $data->format('Y-m-d') === $d['data']) {
                $post['post_date'] = $data->setTime(12, 0)->format('Y-m-d H:i:s');
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
        update_post_meta($resultado, 'serie', $serie);
        foreach (self::campos() as $chave => [, $tipo]) {
            if ($chave !== 'numero' && array_key_exists($chave, $d)) {
                update_post_meta($resultado, $chave, Campos::sanitizar($tipo, $d[$chave]));
            }
        }
        return (int) $resultado;
    }
}
