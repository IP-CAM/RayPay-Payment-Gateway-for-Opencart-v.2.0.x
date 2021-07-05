<?php 

class ModelPaymentRayPay extends Model
{
	public function getMethod($address)
	{
		$this->load->language('payment/raypay');

		if ($this->config->get('raypay_status')) {

			$status = true;

		} else {

			$status = false;
		}

		$method_data = array ();

		if ($status) {

			$method_data = array (
        		'code'       => 'raypay',
        		'title'      => $this->language->get('text_title'),
				'terms'      => null,
				'sort_order' => $this->config->get('raypay_sort_order')
			);
		}

		return $method_data;
	}
}
