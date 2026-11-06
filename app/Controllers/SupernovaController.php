<?php
declare(strict_types=1);

namespace Cefisma\Controllers;

use Cefisma\Core\Controller;
use Cefisma\Models\Edicao;
use Cefisma\Models\Participacao;
use Cefisma\Models\Produto;
use Cefisma\Models\Publicacao;

/** Arquivos do Boletim e Folhetim Supernova e página de cada edição. */
final class SupernovaController extends Controller
{
    public function arquivo(): void
    {
        $serie = get_query_var('cefisma_serie') === 'folhetim' ? 'folhetim' : 'boletim';
        $edicoes = Edicao::todas(-1, 1, $serie);
        $this->render('supernova/arquivo', [
            'destaque'   => $edicoes[0] ?? null,
            'edicoes'    => $edicoes,
            'serie'      => $serie,
            'mediacast'  => Publicacao::porCategoria('mediacast', 3),
            'apoie'      => Produto::porCategoria(Produto::CATEGORIA_SUPERNOVA, 4),
            'url_loja'   => Produto::urlLoja(),
            'url_arquivo' => (string) get_post_type_archive_link(Edicao::TIPO),
            'url_envio'  => Participacao::urlEnvioSupernova(),
        ]);
    }

    public function edicao(): void
    {
        the_post(); // prepara o post atual (necessário para as edições vizinhas)
        $post = get_post();

        $this->render('supernova/edicao', [
            'edicao'     => Edicao::desdePost($post),
            'conteudo_html' => apply_filters('the_content', $post->post_content),
            'vizinhas'   => Edicao::vizinhas($post),
            'apoie'      => Produto::porCategoria(Produto::CATEGORIA_SUPERNOVA, 4),
            'url_loja'   => Produto::urlLoja(),
            'url_envio'  => Participacao::urlEnvioSupernova(),
            'url_arquivo' => (string) get_post_type_archive_link(Edicao::TIPO),
        ]);
    }
}
