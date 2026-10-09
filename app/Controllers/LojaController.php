<?php
declare(strict_types=1);

namespace Cefisma\Controllers;

use Cefisma\Core\Controller;
use Cefisma\Core\View;
use Cefisma\Models\Edicao;

/**
 * Integração loja <-> Supernova:
 *  - checkout: opção de receber o boletim por e-mail;
 *  - pós-compra: link para a edição mais recente;
 *  - páginas do WooCommerce dentro do layout do tema.
 */
final class LojaController extends Controller
{
    public const META_NEWSLETTER = '_cefisma_newsletter';

    public function register(): void
    {
        // Tira os wrappers padrão do WooCommerce: o layout do tema já cuida disso.
        add_action('init', static function (): void {
            remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
            remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
            remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
        });

        add_action('woocommerce_after_order_notes', [$this, 'campoNewsletter']);
        add_action('woocommerce_checkout_create_order', [$this, 'salvarOpcao'], 10, 2);
        add_action('woocommerce_checkout_order_processed', [$this, 'avisarOptin'], 10, 3);
        add_action('woocommerce_thankyou', [$this, 'posCompra'], 5);
    }

    /** woocommerce.php: loja, produto, categorias. */
    public function loja(): void
    {
        ob_start();
        woocommerce_content();
        $html = (string) ob_get_clean();

        $this->render('pages/woocommerce', ['html' => $html]);
    }

    /* ---------- Hooks do checkout ---------- */

    public function campoNewsletter(): void
    {
        View::partial('loja/campo-newsletter');
    }

    /** O nonce do checkout do WooCommerce já protege este envio. */
    public function salvarOpcao(\WC_Order $pedido, array $dados): void
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        $quer = isset($_POST['cefisma_newsletter']) ? 'sim' : 'nao';
        $pedido->update_meta_data(self::META_NEWSLETTER, $quer);
    }

    /**
     * Dispara um hook para o plugin de newsletter (MailPoet, etc.) se inscrever:
     *   add_action('cefisma_newsletter_optin', function ($email, $pedido) { ... }, 10, 2);
     */
    public function avisarOptin(int $pedidoId, array $dados, \WC_Order $pedido): void
    {
        if ($pedido->get_meta(self::META_NEWSLETTER) === 'sim') {
            do_action('cefisma_newsletter_optin', $pedido->get_billing_email(), $pedido);
        }
    }

    /** Bloco "Leia o Supernova" na página de confirmação do pedido. */
    public function posCompra(int $pedidoId): void
    {
        $pedido = wc_get_order($pedidoId);
        View::partial('loja/pos-compra', [
            'edicao' => Edicao::ultima(),
            'optin'  => $pedido && $pedido->get_meta(self::META_NEWSLETTER) === 'sim',
        ]);
    }
}
