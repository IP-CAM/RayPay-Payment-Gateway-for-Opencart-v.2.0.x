<?php 

class ControllerPaymentRayPay extends Controller
{
    /**
     * @var array
     */
	private $error = array ();

    /**
     * IDpay setting
     */
	public function index()
	{
		$this->load->language('payment/raypay');
		$this->load->model('setting/setting');

		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {

			$this->model_setting_setting->editSetting('raypay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_authorization'] = $this->language->get('text_authorization');
		$data['text_sale'] = $this->language->get('text_sale');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_successful_message'] = $this->language->get('successful_message_label');
        $data['text_failed_message'] = $this->language->get('failed_message_label');
        $data['text_sort_order'] = $this->language->get('text_sort_order');
        
		$data['text_user_id'] = $this->language->get('text_user_id');
		$data['text_acceptor_code'] = $this->language->get('text_acceptor_code');
        $data['text_status'] = $this->language->get('text_status');
        $data['text_order_status'] = $this->language->get('text_order_status');
        $data['text_order_status'] = $this->language->get('text_order_status');

        $data['entry_payment_successful_message_default'] = $this->language->get('entry_payment_successful_message_default');
        $data['entry_payment_failed_message_default'] = $this->language->get('entry_payment_failed_message_default');
        $data['text_successful_message_help'] = $this->language->get('text_successful_message_help');
        $data['text_failed_message_help'] = $this->language->get('text_failed_message_help');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

        $data['tab_general'] = $this->language->get('tab_general');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array (

			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array (

			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array (

			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/raypay', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['action'] = $this->url->link('payment/raypay', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->error['warning'])) {

			$data['error_warning'] = $this->error['warning'];

		} else {

			$data['error_warning'] = false;
		}

		if (isset($this->error['user_id'])) {

			$data['error_user_id'] = $this->error['user_id'];

		} else {

			$data['error_user_id'] = false;
		}
		if (isset($this->error['acceptor_code'])) {

			$data['error_acceptor_code'] = $this->error['acceptor_code'];

		} else {

			$data['error_acceptor_code'] = false;
		}

		if (isset($this->request->post['user_id'])) {

			$data['raypay_user_id'] = $this->request->post['raypay_user_id'];

		} else {

			$data['raypay_user_id'] = $this->config->get('raypay_user_id');
		}
		if (isset($this->request->post['acceptor_code'])) {

			$data['raypay_acceptor_code'] = $this->request->post['raypay_acceptor_code'];

		} else {

			$data['raypay_acceptor_code'] = $this->config->get('raypay_acceptor_code');
		}
       

		if (isset($this->request->post['raypay_order_status_id'])) {

			$data['raypay_order_status_id'] = $this->request->post['raypay_order_status_id'];

		} else {

			$data['raypay_order_status_id'] = $this->config->get('raypay_order_status_id');
		}

        if (isset($this->request->post['raypay_successful_message'])) {

            $data['raypay_successful_message'] = trim($this->request->post['raypay_successful_message']);
            $data['entry_payment_successful_message_default'] = trim($this->request->post['raypay_successful_message']);

        } else {

            $data['raypay_successful_message'] = trim($this->config->get('raypay_successful_message'));
            $data['entry_payment_successful_message_default'] = trim($this->config->get('raypay_successful_message'));
        }

        if (isset($this->request->post['raypay_failed_message'])) {

            $data['raypay_failed_message'] = trim($this->request->post['raypay_failed_message']);
            $data['entry_payment_failed_message_default'] = trim($this->request->post['raypay_failed_message']);

        } else {

            $data['raypay_failed_message'] = trim($this->config->get('raypay_failed_message'));
            $data['entry_payment_failed_message_default'] = trim($this->config->get('raypay_failed_message'));
        }

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['raypay_status'])) {

			$data['raypay_status'] = $this->request->post['raypay_status'];

		} else {

			$data['raypay_status'] = $this->config->get('raypay_status');
		}

		if (isset($this->request->post['raypay_sort_order'])) {

			$data['raypay_sort_order'] = $this->request->post['raypay_sort_order'];

		} else {

			$data['raypay_sort_order'] = $this->config->get('raypay_sort_order');
		}

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/raypay.tpl', $data));
	}

    /**
     * @return bool
     */
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'payment/raypay')) {

			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['raypay_user_id'] || !$this->request->post['raypay_acceptor_code']) {

			$this->error['warning'] = $this->language->get('error_validate');
			$this->error['user_id'] = $this->language->get('error_user_id');
			$this->error['acceptor_code'] = $this->language->get('error_acceptor_code');
		}

		if (!$this->error) {

			return true;

		} else {

			return false;
		}
	}

}