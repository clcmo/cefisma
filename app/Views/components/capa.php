<?php
/**
 * Capa de uma edição: imagem real, ou capa gerada (SVG) quando não há imagem.
 *
 * @var array $edicao
 */
$paletas = [['#C8124D', '#FFE9A8'], ['#2A44D8', '#F7B731'], ['#1C1845', '#F7B731'], ['#F7B731', '#1C1845'], ['#E6E2F6', '#C8124D'], ['#C8124D', '#FFFFFF']];
$numero  = (int) ($edicao['numero'] ?? 0);
[$fundo, $traco] = $paletas[max(0, $numero - 1) % count($paletas)];
$alt = 'Capa do ' . ($edicao['titulo'] ?? 'Supernova');
?>
<?php if (!empty($edicao['capa'])) : ?>
  <img src="<?php echo esc_url($edicao['capa']); ?>" alt="<?php echo esc_attr($alt); ?>" width="600" height="800" loading="lazy">
<?php else : ?>
  <svg viewBox="0 0 300 400" role="img" aria-label="<?php echo esc_attr($alt); ?>">
    <rect width="300" height="400" fill="<?php echo esc_attr($fundo); ?>"/>
    <g fill="none" stroke="<?php echo esc_attr($traco); ?>" stroke-width="3">
      <circle cx="150" cy="175" r="40"/>
      <circle cx="150" cy="175" r="82" stroke-dasharray="3 10" stroke-linecap="round"/>
      <circle cx="150" cy="175" r="124"/>
    </g>
    <?php if ($numero > 0) : ?>
      <text x="272" y="70" text-anchor="end" font-family="Bricolage Grotesque,sans-serif" font-weight="800" font-size="64" fill="<?php echo esc_attr($traco); ?>"><?php echo (int) $numero; ?></text>
    <?php endif; ?>
    <text x="26" y="372" font-family="Bricolage Grotesque,sans-serif" font-weight="800" font-size="40" fill="<?php echo esc_attr($traco); ?>">Supernova</text>
  </svg>
<?php endif; ?>
