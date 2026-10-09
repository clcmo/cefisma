/* CEFISMA — comportamentos compartilhados */
(function () {
  'use strict';

  // Raios dos anéis, em % do lado do quadrado (viewBox 520: 80, 125, 170, 215, 250)
  var RAIOS = [15.4, 24, 32.7, 41.3, 48.1];

  function posicionarPontos(raiz) {
    (raiz || document).querySelectorAll('.ondas .ponto').forEach(function (p) {
      var r = RAIOS[parseInt(p.dataset.anel, 10)] || RAIOS[2];
      var a = (parseFloat(p.dataset.ang) || 0) * Math.PI / 180;
      p.style.left = (50 + Math.cos(a) * r) + '%';
      p.style.top = (50 + Math.sin(a) * r) + '%';
    });
  }

  function iniciarMenu() {
    var botao = document.querySelector('.nav-toggle');
    var menu = document.querySelector('.menu');
    if (!botao || !menu) return;
    botao.addEventListener('click', function () {
      var aberto = menu.classList.toggle('aberto');
      botao.setAttribute('aria-expanded', aberto ? 'true' : 'false');
    });
  }

  function iniciarAviso() {
    document.querySelectorAll('.aviso [data-fechar]').forEach(function (b) {
      b.addEventListener('click', function () { b.closest('.aviso').hidden = true; });
    });
  }

  posicionarPontos();
  iniciarMenu();
  iniciarAviso();
  window.CEFISMA = { posicionarPontos: posicionarPontos };
})();
