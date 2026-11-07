<?php
/**
 * @var array  $canais
 * @var array  $transparencia
 * @var array  $contato   [email, site, sobre]
 * @var string $whatsapp
 */
?>
<footer class="rodape">
  <div class="wrap">
    <div>
      <h2>
        <a class="rodape-marca" href="<?php echo esc_url(home_url('/')); ?>" aria-label="CEFISMA, página inicial">
          <img src="<?php echo esc_url(get_theme_file_uri('assets/images/logo-cefisma-rodape.svg')); ?>" alt="CEFISMA" width="680" height="200" loading="lazy">
        </a>
      </h2>
      <p><?php echo esc_html($contato['sobre']); ?></p>
      <?php if (!empty($contato['site'])) : ?>
        <p><a href="<?php echo esc_url($contato['site']); ?>"><?php echo esc_html((string) wp_parse_url($contato['site'], PHP_URL_HOST)); ?></a></p>
      <?php endif; ?>
      <p><a href="mailto:<?php echo esc_attr(antispambot($contato['email'])); ?>"><?php echo esc_html(antispambot($contato['email'])); ?></a></p>
    </div>
    <div>
      <h2>Transparência</h2>
      <ul>
        <?php foreach ($transparencia as $link) : ?>
          <li><a href="<?php echo esc_url($link['url']); ?>"><?php echo esc_html($link['rotulo']); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div>
      <h2>Redes sociais</h2>
      <ul class="redes-sociais">
        <?php foreach ($canais as $canal) : ?>
          <li>
            <a href="<?php echo esc_url($canal['url']); ?>" target="_blank" rel="noopener noreferrer" title="<?php echo esc_attr($canal['rotulo']); ?>" aria-label="<?php echo esc_attr($canal['rotulo']); ?> do CEFISMA no IFUSP">
              <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <?php if ($canal['icone'] === 'instagram') : ?>
                  <rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.25" fill="currentColor"/>
                <?php elseif ($canal['icone'] === 'telegram') : ?>
                  <path fill="currentColor" d="M21.7 4.3 18.5 20c-.2 1.1-.9 1.4-1.8.9l-5-3.7-2.4 2.3c-.3.3-.5.5-1 .5l.4-5.1 9.3-8.4c.4-.4-.1-.6-.6-.2L6 13.5l-4.9-1.5c-1.1-.3-1.1-1.1.2-1.6L20.4 3c.9-.3 1.6.2 1.3 1.3Z"/>
                <?php elseif ($canal['icone'] === 'tiktok') : ?>
                  <path fill="currentColor" d="M19.6 7.2a6.8 6.8 0 0 1-4.2-1.5v8.1a6.3 6.3 0 1 1-5.4-6.2v3.5a2.9 2.9 0 1 0 2 2.7V2h3.4c.3 2.1 1.8 3.7 4.2 4.1v3.5Z"/>
                <?php elseif ($canal['icone'] === 'x') : ?>
                  <path fill="currentColor" d="M18.9 2H22l-6.8 7.8L23.2 22h-6.3L12 14.6 5.5 22H2.3l7.3-8.4L1.8 2h6.5l4.5 6.8L18.9 2Zm-1.1 17.8h1.7L7.3 4H5.5l12.3 15.8Z"/>
                <?php elseif ($canal['icone'] === 'facebook') : ?>
                  <path fill="currentColor" d="M13.4 22v-8.9h3l.5-3.5h-3.5V7.4c0-1 .3-1.7 1.8-1.7H17V2.5c-.3 0-1.4-.1-2.7-.1-2.7 0-4.5 1.6-4.5 4.6v2.6h-3v3.5h3V22h3.6Z"/>
                <?php endif; ?>
              </svg>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <p class="nota nota-tema">© <?php echo esc_html(wp_date('Y')); ?> CEFISMA. Todos os direitos reservados. Criado por <a href="dev.camilaloliveira.com">Camila L. Oliveira </a></p>
  </div>
</footer>
<?php if ($whatsapp !== '') : ?>
  <a class="zap" href="<?php echo esc_url($whatsapp); ?>" aria-label="Falar com o CEFISMA no WhatsApp">Fale com o CEFISMA</a>
<?php endif; ?>
