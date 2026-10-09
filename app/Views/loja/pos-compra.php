<?php
/**
 * Bloco na página "Pedido recebido" do WooCommerce.
 *
 * @var array|null $edicao
 * @var bool       $optin
 */
?>
<?php if ($edicao) : ?>
<aside class="pos-compra" aria-label="Leia o Supernova">
  <div><?php cefisma_view('components/capa', ['edicao' => $edicao]); ?></div>
  <div>
    <h3>Leia o <?php echo esc_html($edicao['titulo']); ?></h3>
    <p><a class="btn btn-primario btn-pequeno" href="<?php echo esc_url($edicao['url']); ?>">Abrir a edição</a></p>
    <?php if ($optin) : ?>
      <p class="meta">Você vai receber as próximas edições por e-mail.</p>
    <?php endif; ?>
  </div>
</aside>
<?php endif; ?>
