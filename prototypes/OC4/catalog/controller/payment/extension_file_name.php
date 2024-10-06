<?php
namespace Opencart\Catalog\Controller\Extension\{{t_extension_dir_name}}\Payment;
class {{t_extension_name}} extends \Opencart\System\Engine\Controller {
	
	/**
	 * Index
	 *
	 * @return string
	 */
	public function index(): string {
		$this->load->language('extension/{{s_extension_dir_name}}/payment/{{s_extension_name}}');
		$data['language'] = $this->config->get('config_language');
		return $this->load->view('extension/{{s_extension_dir_name}}/payment/{{s_extension_name}}', $data);
	}

	/**
	 * Confirm
	 *
	 * @return void
	 */
	public function confirm(): void {
		$this->load->language('extension/{{s_extension_dir_name}}/payment/{{s_extension_name}}');
		
		$json = [];

		if (isset($this->session->data['order_id'])) {
			$this->load->model('checkout/order');

			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

			if (!$order_info) {
				$json['redirect'] = $this->url->link('checkout/failure', 'language=' . $this->config->get('config_language'), true);

				unset($this->session->data['order_id']);
			}
		} else {
			$json['error'] = $this->language->get('error_order');
		}

		if (!isset($this->session->data['payment_method']) || $this->session->data['payment_method']['code'] != '{{s_extension_name}}.{{s_extension_name}}') {
			$json['error'] = $this->language->get('error_payment_method');
		}

		if (!$json) {
			$this->load->model('checkout/order');

			$this->model_checkout_order->addHistory($this->session->data['order_id'], $this->config->get('payment_{{s_extension_name}}_order_status_id'));

			$json['redirect'] = $this->url->link('checkout/success', 'language=' . $this->config->get('config_language'), true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}