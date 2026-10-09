<?php
/**
 * @var string $titulo
 * @var array  $itens
 * @var string|null $paginacao
 */
?>
<div class="wrap cabecalho-pagina">
  <h1><?php echo esc_html($titulo); ?></h1>
</div>
<section class="secao" style="padding-top:1rem">
  <div class="wrap">
    <?php if ($itens) : ?>
      <ul class="pubs">
        <?php foreach ($itens as $item) : cefisma_view('components/publicacao', ['item' => $item]); endforeach; ?>
      </ul>
      <?php if ($paginacao) : ?>
        <nav class="paginacao" aria-label="Paginação"><?php echo wp_kses_post($paginacao); ?></nav>
      <?php endif; ?>
    <?php else : ?>
      <p>Nada encontrado aqui. <a href="<?php echo esc_url(home_url('/')); ?>">Voltar para o início</a>.</p>
    <?php endif; ?>
  </div>
</section>
