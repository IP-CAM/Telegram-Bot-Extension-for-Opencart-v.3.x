<?php
/**
 * @author Dmytro Sokolenko <sokol1294@gmail.com>
 * @license GNU General Public License, version 3. See licence.txt
 */
class ControllerExtensionModuleTelegram extends Controller {
	public function send($text) {
    if (!$this->config->get('module_telegram_status')) {
      return;
    }

		$json = array();

		$bot_token = $this->config->get('module_telegram_bot');

		$api_url = 'https://api.telegram.org/bot' . $bot_token . '/sendMessage';

		$handle = curl_init($api_url);

		$parameters = array(
			'chat_id' => $this->config->get('module_telegram_chat'),
			'text'    => $text
		);

		curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($handle, CURLOPT_TIMEOUT, 10);
		curl_setopt($handle, CURLOPT_POST, 1);
		curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
		curl_setopt($handle, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

		$response = curl_exec($handle);

		if (curl_error($handle)) {
			$this->log->write('Telegram Bot CURL ERROR: ' . curl_errno($handle) . '::' . curl_error($handle));
		} elseif (!$response) {
			$this->log->write('Telegram Bot CURL ERROR: Empty Gateway Response');
		}

		curl_close($handle);

		$result = json_decode($response, true);

		if (!$result['ok']) {
			$this->log->write(json_encode($response));

			$json['error'] = $this->language->get('error_unexpected');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
