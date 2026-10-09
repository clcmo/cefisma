<?php
/**
 * Bloco "Apoie o Supernova": produtos da categoria "supernova" da loja.
 * Some sozinho se o WooCommerce estiver inativo ou a categoria estiver vazia.
 *
 * @var array  $apoie
 * @var string $url_loja
 */
?>
<?php if ($apoie) : ?>
<section class="secao banda-lavanda" aria-labelledby="h-apoie">
  <div class="wrap">
    <div class="cab-secao">
      <h2 id="h-apoie">Apoie o Supernova</h2>
      <a class="btn btn-primario btn-pequeno" href="<?php echo esc_url($url_loja); ?>">Ver a loja</a>
    </div>
    <ul class="grade-produtos">
      <?php foreach ($apoie as $produto) : cefisma_view('components/produto', ['produto' => $produto]); endforeach; ?>
    </ul>
  </div>
</section>
<?php endif; ?>
