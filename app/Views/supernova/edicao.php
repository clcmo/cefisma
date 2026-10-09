<?php
/**
 * Página de uma edição.
 *
 * @var array  $edicao
 * @var string $conteudo_html
 * @var array  $vizinhas  [anterior, proxima]
 * @var array  $apoie
 * @var string $url_loja
 * @var string $url_envio
 * @var string $url_arquivo
 */
?>
<div class="wrap cabecalho-pagina">
  <p class="meta"><a href="<?php echo esc_url($url_arquivo); ?>">Todas as edições do Supernova</a></p>
</div>
<section class="secao" style="padding-top:0">
  <div class="wrap">
    <?php cefisma_view('components/edicao-destaque', ['edicao' => $edicao, 'titulo_tag' => 'h1', 'link_titulo' => false]); ?>
    <?php if (trim($conteudo_html) !== '') : ?>
      <div class="conteudo" style="margin-top:3rem"><?php echo $conteudo_html; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content ?></div>
    <?php endif; ?>
    <nav class="acoes" aria-label="Outras edições" style="margin-top:3rem">
      <?php if ($vizinhas['anterior']) : ?>
        <a class="btn btn-linha btn-pequeno" href="<?php echo esc_url($vizinhas['anterior']['url']); ?>">Anterior: <?php echo esc_html($vizinhas['anterior']['titulo']); ?></a>
      <?php endif; ?>
      <?php if ($vizinhas['proxima']) : ?>
        <a class="btn btn-linha btn-pequeno" href="<?php echo esc_url($vizinhas['proxima']['url']); ?>">Próxima: <?php echo esc_html($vizinhas['proxima']['titulo']); ?></a>
      <?php endif; ?>
    </nav>
  </div>
</section>
<?php cefisma_view('supernova/envio', ['url_envio' => $url_envio]); ?>
<?php cefisma_view('loja/apoie', ['apoie' => $apoie, 'url_loja' => $url_loja]); ?>
