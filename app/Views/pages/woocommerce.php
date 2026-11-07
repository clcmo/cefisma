<?php
/** @var string $html saída de woocommerce_content() */
/** @var bool $pagina_loja */
?>
<div class="wrap secao">
  <?php if ($pagina_loja) : ?>
    <header class="loja-intro">
      <span class="tag">Loja do CEFISMA</span>
      <p>Camisetas e produtos do Centro Acadêmico. Consulte as opções e a disponibilidade diretamente em cada produto.</p>
    </header>
  <?php endif; ?>
  <?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- HTML gerado pelo WooCommerce ?>
</div>
