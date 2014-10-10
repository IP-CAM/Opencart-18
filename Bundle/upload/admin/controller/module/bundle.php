<!-- 
Date: October 10, 2014
Author: Zach Donnelly
About:
-->
<?php

class ControllerModuleBundle extends Controller{
	private $errors = array();
	
	public function Index()
	{
		$this->load->language('module/bundle');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('bundle', $this->request->post);

			$this->cache->delete('product');

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$data['heading_title'] = $this->language->get('heading_title');
		$data['message'] = $this->language->get('message');
		
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_module_add'] = $this->language->get('button_module_add');
		$data['button_remove'] = $this->language->get('button_remove');
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('module/bundle', 'token=' . $this->session->data['token'], 'SSL')
		);
		
		$data['action'] = $this->url->link('module/bundle', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		$data['text_edit'] = $this->language->get('text_edit');
		
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_enabled'] = $this->language->get('text_enabled');
		
		if (isset($this->request->post['bundle_status'])) {
			$data['bundle_status'] = $this->request->post['bundle_status'];
		} else {
			$data['bundle_status'] = $this->config->get('bundle_status');
		}
		
		if (isset($this->request->post['bundle_module'])) {
			$modules = $this->request->post['bundle_module'];
		} elseif ($this->config->has('bundle_module')) {
			$modules = $this->config->get('bundle_module');
		} else {
			$modules = array();
		}
		
		$data['bundle_modules'] = array();
		
		$data['entry_status'] = $this->language->get('entry_status');
		
		// load common files
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		// place after all data declarations
		$this->response->setOutput($this->load->view('module/bundle.tpl', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/bundle')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (isset($this->request->post['bundle_module'])) {
			foreach ($this->request->post['bundle_module'] as $key => $value) {
				if (!$value['width'] || !$value['height']) {
					$this->error['image'][$key] = $this->language->get('error_image');
				}
			}
		}

		return !$this->error;
	}
}