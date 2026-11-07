<?php
/**
 * Página com formas de participação estudantil.
 *
 * @var string $titulo
 * @var string $conteudo_extra
 * @var array $itens
 * @var array $contato
 */
$contribuicaoSupernova = null;
foreach ($itens as $item) {
    if (stripos($item['url'], 'supernova') !== false) {
        $contribuicaoSupernova = $item;
        break;
    }
}
?>
<article class="secao">
  <div class="wrap">
    <header class="cabecalho-pagina conteudo">
      <span class="tag">Comunidade IFUSP</span>
      <h1><?php echo esc_html($titulo); ?></h1>
      <p class="lede">O CEFISMA é feito com a participação dos estudantes. Há várias maneiras de contribuir, compartilhar ideias e construir atividades em conjunto.</p>
    </header>

    <div class="conteudo">
      <section aria-labelledby="h-rd">
        <h2 id="h-rd">Representação discente</h2>
        <p>Representantes discentes (RDs) levam as perspectivas dos estudantes aos espaços colegiados da Universidade. Acompanhe os informes e repasses, converse com as pessoas que representam o corpo discente e compartilhe questões que mereçam ser discutidas.</p>
        <p>As formas de escolha e eventuais inscrições dependem de cada representação e processo. Consulte os editais publicados e os informes do CEFISMA para acompanhar oportunidades abertas.</p>
        <div class="acoes">
          <a class="btn btn-primario" href="<?php echo esc_url(home_url('/category/rd/')); ?>">Acompanhar repasses dos RDs</a>
          <a class="btn btn-linha" href="<?php echo esc_url(home_url('/eleicao/')); ?>">Consultar informações de eleições</a>
        </div>
      </section>

      <section aria-labelledby="h-supernova">
        <h2 id="h-supernova">Escreva, ilustre ou colabore com o Supernova</h2>
        <p>O Boletim e o Folhetim Supernova são espaços para a comunidade IFUSPiana compartilhar textos, artes, ideias e informações. Estudantes podem propor contribuições para as próximas edições.</p>
        <?php if ($contribuicaoSupernova) : ?>
          <p><a class="btn btn-primario" href="<?php echo esc_url($contribuicaoSupernova['url']); ?>"><?php echo esc_html($contribuicaoSupernova['botao']); ?> sua contribuição</a></p>
        <?php else : ?>
          <p><a class="btn btn-primario" href="<?php echo esc_url(home_url('/supernova/')); ?>">Conhecer o Supernova</a></p>
        <?php endif; ?>
      </section>

      <section aria-labelledby="h-eventos">
        <h2 id="h-eventos">Organize atividades e eventos</h2>
        <p>Tem uma ideia para uma atividade cultural, acadêmica ou de integração? Convide outros estudantes para construir a proposta e entre em contato com o CEFISMA para conversar sobre como organizá-la em conjunto.</p>
        <p><a class="btn btn-linha" href="mailto:<?php echo esc_attr(antispambot($contato['email'])); ?>">Conversar com o CEFISMA</a></p>
      </section>

      <section aria-labelledby="h-outras-formas">
        <h2 id="h-outras-formas">Outras formas de participar</h2>
        <p>Compartilhe demandas sobre as disciplinas, acompanhe os canais de comunicação e participe das conversas e atividades abertas à comunidade.</p>
        <ul class="linhas">
          <?php foreach ($itens as $item) : ?>
            <?php if ($contribuicaoSupernova && $item['url'] === $contribuicaoSupernova['url']) : ?>
              <?php continue; ?>
            <?php endif; ?>
            <li>
              <div><h3><?php echo esc_html($item['titulo']); ?></h3><p><?php echo esc_html($item['texto']); ?></p></div>
              <a class="btn btn-linha btn-pequeno" href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['botao']); ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
        <p>Também é possível acompanhar os <a href="<?php echo esc_url(home_url('/comunicacao/')); ?>">informes</a> e as <a href="<?php echo esc_url(home_url('/supernova/')); ?>">publicações do Supernova</a>.</p>
      </section>

      <?php if ($conteudo_extra !== '') : ?>
        <section class="conteudo-editor">
          <?php echo $conteudo_extra; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content já filtrado ?>
        </section>
      <?php endif; ?>
    </div>
  </div>
</article>
