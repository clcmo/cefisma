<?php
/**
 * Página institucional do CEFISMA.
 *
 * @var string $titulo
 * @var string $conteudo_extra
 */
?>
<article class="secao">
  <div class="wrap">
    <header class="cabecalho-pagina conteudo">
      <span class="tag">Institucional</span>
      <h1><?php echo esc_html($titulo); ?></h1>
      <p class="lede">Uma representação estudantil construída coletivamente por quem vive o Instituto de Física da USP.</p>
    </header>

    <div class="conteudo">
      <h2>O que é o CEFISMA</h2>
      <p>O Centro Acadêmico do Instituto de Física da Universidade de São Paulo (CEFISMA) é a entidade de representação das e dos estudantes do IFUSP. É um espaço de organização, diálogo e participação estudantil, feito por estudantes e para a comunidade discente.</p>

      <h2>Uma história feita em conjunto</h2>
      <p>A história do CEFISMA não se resume a uma diretoria ou a um momento específico. Ela é construída continuamente pela participação de diferentes gerações de estudantes: nas conversas do cotidiano, nas assembleias, nas atividades organizadas em conjunto e nas reivindicações levadas à comunidade universitária.</p>
      <p>Ao longo dessa trajetória, o Centro Acadêmico é um ponto de encontro para compartilhar experiências, discutir as condições de estudo e construir formas coletivas de atuação. Cada gestão recebe um trabalho que já vinha sendo feito, apresenta suas prioridades e contribui para que novas pessoas possam participar.</p>

      <h2>Representar, acolher e organizar</h2>
      <p>O trabalho do CEFISMA inclui ouvir as demandas estudantis, promover espaços de conversa e representar os estudantes nas discussões que afetam a vida acadêmica. Também envolve criar atividades culturais e de integração, divulgar informações de interesse dos alunos e aproximar diferentes grupos do IFUSP.</p>
      <p>Essas frentes dependem da participação da comunidade. Reuniões, projetos, publicações e iniciativas coletivas são oportunidades para contribuir, propor mudanças e manter viva a representação estudantil.</p>

      <h2>Preservar a memória e construir o futuro</h2>
      <p>Guardar registros e compartilhar experiências ajuda a compreender os caminhos percorridos e a transmitir aprendizados entre gerações. O <em>Supernova</em>, boletim dos estudantes do IFUSP, e os materiais de transparência fazem parte dessa memória e ajudam a documentar ideias, atividades e debates.</p>
      <p>A história do CEFISMA continua sendo escrita por quem participa. Se você estuda no IFUSP, pode conhecer o trabalho da gestão, acompanhar as publicações e trazer suas ideias para os espaços coletivos.</p>

      <div class="acoes">
        <a class="btn btn-primario" href="<?php echo esc_url(home_url('/formularios/')); ?>">Participe</a>
        <a class="btn btn-linha" href="<?php echo esc_url(home_url('/transparencia/')); ?>">Acompanhe a transparência</a>
      </div>

      <?php if ($conteudo_extra !== '') : ?>
        <div class="conteudo-editor">
          <?php echo $conteudo_extra; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content já filtrado ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</article>
