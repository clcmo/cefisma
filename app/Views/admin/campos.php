<?php
/**
 * Caixa "Detalhes" do editor (admin).
 *
 * @var array  $campos [chave, rotulo, tipo, valor]
 * @var string $nonce
 */
wp_nonce_field($nonce, $nonce);
?>
<table class="form-table" role="presentation">
  <?php foreach ($campos as $c) : ?>
    <tr>
      <th scope="row"><label for="cefisma-<?php echo esc_attr($c['chave']); ?>"><?php echo esc_html($c['rotulo']); ?></label></th>
      <td>
        <?php if ($c['tipo'] === 'linhas') : ?>
          <textarea id="cefisma-<?php echo esc_attr($c['chave']); ?>" name="cefisma[<?php echo esc_attr($c['chave']); ?>]" rows="6" class="large-text code"><?php echo esc_textarea($c['valor']); ?></textarea>
        <?php elseif ($c['tipo'] === 'serie') : ?>
          <select id="cefisma-<?php echo esc_attr($c['chave']); ?>" name="cefisma[<?php echo esc_attr($c['chave']); ?>]">
            <option value="boletim" <?php selected($c['valor'], 'boletim'); ?>>Boletim</option>
            <option value="folhetim" <?php selected($c['valor'], 'folhetim'); ?>>Folhetim</option>
          </select>
        <?php else : ?>
          <input id="cefisma-<?php echo esc_attr($c['chave']); ?>" name="cefisma[<?php echo esc_attr($c['chave']); ?>]"
                 type="<?php echo esc_attr(['number' => 'number', 'url' => 'url', 'date' => 'date'][$c['tipo']] ?? 'text'); ?>"
                 value="<?php echo esc_attr($c['valor']); ?>" class="regular-text">
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</table>
