<?php
/**
 * @author Dmytro Sokolenko <sokol1294@gmail.com>
 * @license GNU General Public License, version 3. See licence.txt
 */
class ControllerExtensionModuleTelegram extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/telegram');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_telegram', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['bot'])) {
			$data['error_telegram_bot'] = $this->error['bot'];
		} else {
			$data['error_telegram_bot'] = '';
		}

		if (isset($this->error['chat'])) {
			$data['error_telegram_chat'] = $this->error['chat'];
		} else {
			$data['error_telegram_chat'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/telegram', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['module_telegram_bot'])) {
			$data['module_telegram_bot'] = $this->request->post['module_telegram_bot'];
		} else {
			$data['module_telegram_bot'] = $this->config->get('module_telegram_bot');
		}

		if (isset($this->request->post['module_telegram_chat'])) {
			$data['module_telegram_chat'] = $this->request->post['module_telegram_chat'];
		} else {
			$data['module_telegram_chat'] = $this->config->get('module_telegram_chat');
		}

		if (isset($this->request->post['module_telegram_status'])) {
			$data['module_telegram_status'] = $this->request->post['module_telegram_status'];
		} else {
			$data['module_telegram_status'] = $this->config->get('module_telegram_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/telegram', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/telegram')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (utf8_strlen($this->request->post['module_telegram_bot']) < 1) {
			$this->error['bot'] = $this->language->get('error_bot');
		}

		if (utf8_strlen($this->request->post['module_telegram_chat']) < 1) {
			$this->error['chat'] = $this->language->get('error_chat');
		}

		return !$this->error;
	}
}
