<?php
declare(strict_types=1);

namespace Cefisma\Controllers;

use Cefisma\Core\Controller;
use Cefisma\Models\Edicao;
use Cefisma\Models\Participacao;
use Cefisma\Models\Produto;
use Cefisma\Models\Publicacao;

/** Arquivo de edições e página de cada edição do Boletim Supernova. */
final class SupernovaController extends Controller
{
    public function arquivo(): void
    {
        $edicoes = Edicao::todas();
        $this->render('supernova/arquivo', [
            'destaque'   => $edicoes[0] ?? null,
            'edicoes'    => $edicoes,
            'mediacast'  => Publicacao::porCategoria('mediacast', 3),
            'apoie'      => Produto::porCategoria(Produto::CATEGORIA_SUPERNOVA, 4),
            'url_loja'   => Produto::urlLoja(),
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
