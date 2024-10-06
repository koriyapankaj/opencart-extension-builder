<?php

namespace Opencart\Catalog\Model\Extension\{{t_extension_dir_name}}\Payment;

class {{t_extension_name}} extends \Opencart\System\Engine\Model
{

	/**
	 * Get Methods
	 *
	 * @param array<string, mixed> $address
	 *
	 * @return array<string, mixed>
	 */
	public function getMethods(array $address = []): array
	{

		$this->load->language('extension/{{s_extension_dir_name}}/payment/{{s_extension_name}}');

		if ($this->cart->hasSubscription()) {
			$status = false;
		} elseif (!$this->cart->hasShipping()) {
			$status = false;
		} elseif (!$this->config->get('config_checkout_payment_address')) {
			$status = true;
		} elseif (!$this->config->get('payment_{{s_extension_name}}_geo_zone_id')) {
			$status = true;
		} else {
			$this->load->model('localisation/geo_zone');

			$results = $this->model_localisation_geo_zone->getGeoZone((int)$this->config->get('payment_{{s_extension_name}}_geo_zone_id'), (int)$address['country_id'], (int)$address['zone_id']);

			if ($results) {
				$status = true;
			} else {
				$status = false;
			}
		}

		$method_data = [];

		if ($status) {
			$option_data['{{s_extension_name}}'] = [
				'code' => '{{s_extension_name}}.{{s_extension_name}}',
				'name' => $this->language->get('heading_title')
			];

			$method_data = [
				'code'       => '{{s_extension_name}}',
				'name'       => $this->language->get('heading_title'),
				'option'     => $option_data,
				'sort_order' => $this->config->get('payment_{{s_extension_name}}_sort_order')
			];
		}

		return $method_data;
	}
}
