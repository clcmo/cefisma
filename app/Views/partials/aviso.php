<?php /** @var array $avisos lista de [texto, link, link_texto] */ ?>
<?php foreach ($avisos as $aviso) : ?>
<div class="aviso" role="region" aria-label="Aviso">
  <div class="wrap">
    <p>
      <?php echo esc_html($aviso['texto']); ?>
      <?php if ($aviso['link'] !== '') : ?>
        <a href="<?php echo esc_url($aviso['link']); ?>"><?php echo esc_html($aviso['link_texto']); ?></a>
      <?php endif; ?>
    </p>
    <button type="button" data-fechar aria-label="Dispensar aviso">Dispensar</button>
  </div>
</div>
<?php endforeach; ?>
