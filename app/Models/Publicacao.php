<?php
declare(strict_types=1);

namespace Cefisma\Models;

/** Publicações comuns do site (posts): RDs, tribunas, relatórios, notas do Supernova. */
final class Publicacao
{
    public static function recentes(int $limite = 3): array
    {
        $consulta = new \WP_Query([
            'post_type'           => 'post',
            'posts_per_page'      => $limite,
            'no_found_rows'       => true,
            'ignore_sticky_posts' => true,
        ]);
        return array_map([self::class, 'desdePost'], $consulta->posts);
    }

    public static function porCategoria(string $slug, int $limite = 3): array
    {
        $consulta = new \WP_Query([
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => $limite,
            'category_name'       => sanitize_title($slug),
            'no_found_rows'       => true,
            'ignore_sticky_posts' => true,
        ]);
        return array_map([self::class, 'desdePost'], $consulta->posts);
    }

    public static function paginaDaCategoria(string $slug, int $pagina = 1, int $limite = 10): array
    {
        $consulta = new \WP_Query([
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => $limite,
            'paged'               => max(1, $pagina),
            'category_name'       => sanitize_title($slug),
            'ignore_sticky_posts' => true,
        ]);

        return [
            'itens' => array_map([self::class, 'desdePost'], $consulta->posts),
            'paginacao' => $consulta->max_num_pages > 1
                ? paginate_links([
                    'type'      => 'plain',
                    'total'     => $consulta->max_num_pages,
                    'current'   => max(1, $pagina),
                    'prev_text' => 'Anterior',
                    'next_text' => 'Próxima',
                ])
                : '',
        ];
    }

    public static function desdePost(\WP_Post $p): array
    {
        $tags = [];
        foreach (get_the_category($p->ID) as $c) {
            // Categorias-guarda-chuva ("boletim", "cefisma") não viram etiqueta.
            if (!in_array($c->slug, ['boletim', 'cefisma', 'sem-categoria', 'uncategorized'], true)) {
                $tags[] = ['slug' => $c->slug, 'nome' => $c->name];
            }
        }
        return [
            'id'       => $p->ID,
            'titulo'   => get_the_title($p),
            'url'      => get_permalink($p),
            'resumo'   => wp_trim_words(get_the_excerpt($p), 28),
            'data'     => get_the_date('j \d\e F \d\e Y', $p),
            'iso'      => get_post_time('c', false, $p),
            'tags'     => $tags,
            'conteudo' => '', // preenchido por quem precisa (ver PaginaController)
        ];
    }
}
