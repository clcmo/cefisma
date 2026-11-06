<?php
/**
 * Página de informes e comunicação do CEFISMA.
 *
 * @var string $titulo
 * @var string $conteudo_extra
 * @var array $itens
 * @var string|null $paginacao
 */
?>
<article class="secao">
  <div class="wrap">
    <header class="cabecalho-pagina conteudo">
      <span class="tag">Informes do CEFISMA</span>
      <h1><?php echo esc_html($titulo); ?></h1>
      <p class="lede">Comunicados, atualizações e informações importantes para a comunidade estudantil do Instituto de Física da USP.</p>
      <p>Acompanhe nesta página os informes publicados pelo CEFISMA. As publicações são organizadas a partir da categoria <strong>informes</strong> no WordPress para facilitar a consulta e manter os comunicados reunidos em um só lugar.</p>
    </header>

    <?php if ($conteudo_extra !== '') : ?>
      <div class="conteudo">
        <?php echo $conteudo_extra; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content já filtrado ?>
      </div>
    <?php endif; ?>

    <section class="secao" aria-labelledby="h-informes">
      <h2 id="h-informes">Informes publicados</h2>
      <?php if ($itens) : ?>
        <ul class="pubs">
          <?php foreach ($itens as $item) : cefisma_view('components/publicacao', ['item' => $item]); endforeach; ?>
        </ul>
        <?php if ($paginacao) : ?>
          <nav class="paginacao" aria-label="Paginação dos informes"><?php echo wp_kses_post($paginacao); ?></nav>
        <?php endif; ?>
      <?php else : ?>
        <p class="suave">Ainda não há informes publicados nesta categoria.</p>
      <?php endif; ?>
    </section>
  </div>
</article>
