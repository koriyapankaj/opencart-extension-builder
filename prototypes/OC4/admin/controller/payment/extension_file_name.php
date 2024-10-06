<?php
namespace Opencart\Admin\Controller\Extension\{{t_extension_dir_name}}\Payment;
class {{t_extension_name}} extends \Opencart\System\Engine\Controller {

	private $error = [];
	private $separator = '';
	
	public function __construct($registry) {
        parent::__construct($registry);

		if (VERSION >= '4.0.2.0') {
			$this->separator = '.';
		} else {
			$this->separator = '|';
		}
		
    }
	
	public function index(): void {
		$this->load->language('extension/{{s_extension_dir_name}}/payment/{{s_extension_name}}');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/{{s_extension_dir_name}}/payment/{{s_extension_name}}', 'user_token=' . $this->session->data['user_token'])
		];

		$data['save'] = $this->url->link('extension/{{s_extension_dir_name}}/payment/{{s_extension_name}}' . $this->separator . 'save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');

		$data['payment_{{s_extension_name}}_order_status_id'] = $this->config->get('payment_{{s_extension_name}}_order_status_id');

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['payment_{{s_extension_name}}_geo_zone_id'] = $this->config->get('payment_{{s_extension_name}}_geo_zone_id');
		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$data['payment_{{s_extension_name}}_status'] = $this->config->get('payment_{{s_extension_name}}_status');
		$data['payment_{{s_extension_name}}_sort_order'] = $this->config->get('payment_{{s_extension_name}}_sort_order');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/{{s_extension_dir_name}}/payment/{{s_extension_name}}', $data));
	}

	public function save(): void {
		$this->load->language('extension/{{s_extension_dir_name}}/payment/{{s_extension_name}}');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/{{s_extension_dir_name}}/payment/{{s_extension_name}}')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('payment_{{s_extension_name}}', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	public function install(): void {
		if ($this->user->hasPermission('modify', 'extension/payment')) {
			$this->load->model('setting/setting');
			$setting = [];
			$setting['payment_{{s_extension_name}}_sort_order'] = '1';
			$this->model_setting_setting->editSetting('payment_{{s_extension_name}}', $setting);
		}
	}

	public function uninstall(): void {
		if ($this->user->hasPermission('modify', 'extension/payment')) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->deleteSetting('payment_{{s_extension_name}}');
		}
	}
}
