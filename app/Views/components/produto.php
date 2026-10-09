<?php
/** @var array $produto dados vindos de Models\Produto */
?>
<li class="produto">
  <a class="arte" href="<?php echo esc_url($produto['url']); ?>" tabindex="-1" aria-hidden="true"><?php echo wp_kses_post($produto['imagem']); ?></a>
  <h3><a href="<?php echo esc_url($produto['url']); ?>"><?php echo esc_html($produto['nome']); ?></a></h3>
  <p class="preco"><?php echo wp_kses_post($produto['preco_html']); ?></p>
  <?php if ($produto['compravel']) : ?>
    <a class="btn btn-primario btn-pequeno<?php echo $produto['ajax'] ? ' add_to_cart_button ajax_add_to_cart' : ''; ?>"
       href="<?php echo esc_url($produto['compra_url']); ?>"
       data-product_id="<?php echo (int) $produto['id']; ?>" data-quantity="1" rel="nofollow"><?php echo esc_html($produto['compra_txt']); ?></a>
  <?php else : ?>
    <a class="btn btn-linha btn-pequeno" href="<?php echo esc_url($produto['url']); ?>">Ver produto</a>
  <?php endif; ?>
</li>
