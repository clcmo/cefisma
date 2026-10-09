<?php
declare(strict_types=1);

namespace Cefisma\Controllers;

use Cefisma\Core\Controller;
use Cefisma\Models\Publicacao;

/** Páginas comuns, posts, listagens (categoria, tag, busca) e 404. */
final class PaginaController extends Controller
{
    /** page.php e single.php */
    public function conteudo(): void
    {
        the_post();
        $post = get_post();
        $dados = Publicacao::desdePost($post);
        $dados['conteudo'] = apply_filters('the_content', $post->post_content);
        $dados['eh_post'] = $post->post_type === 'post';
        $dados['imagem'] = get_the_post_thumbnail($post, 'large');

        $this->render('pages/conteudo', ['item' => $dados]);
    }

    /** index.php, archive.php e search.php */
    public function listar(): void
    {
        global $wp_query;

        if (is_search()) {
            $titulo = 'Busca: ' . get_search_query();
        } elseif (is_home() || is_front_page()) {
            $titulo = 'Publicações';
        } else {
            $titulo = wp_strip_all_tags(get_the_archive_title());
        }

        $this->render('pages/lista', [
            'titulo'    => $titulo,
            'itens'     => array_map([Publicacao::class, 'desdePost'], $wp_query->posts),
            'paginacao' => paginate_links(['type' => 'plain', 'prev_text' => 'Anterior', 'next_text' => 'Próxima']),
        ]);
    }

    public function naoEncontrado(): void
    {
        status_header(404);
        $this->render('pages/404');
    }
}
