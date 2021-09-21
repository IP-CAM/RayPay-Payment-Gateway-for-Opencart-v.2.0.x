<?php

class ControllerPaymentRayPay extends Controller
{
    /**
     * @param $id
     * @return string
     */
    public function generateString($id)
    {
        return 'RayPay Invoice ID: ' . $id;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $this->load->language('payment/raypay');
        $this->load->model('checkout/order');
        $data = array();

        /** @var \ModelCheckoutOrder $model */
        $model = $this->model_checkout_order;

        $order_info = $model->getOrder($this->session->data['order_id']);

        $amount = $this->correctAmount($order_info);
        $data['text_wait'] = $this->language->get('text_wait');

        $data['button_confirm'] = $this->language->get('button_confirm');

        $data['error_warning'] = false;

        if (extension_loaded('curl')) {

            $redirectUrl = $this->url->link('payment/raypay/callback', '', true).'&order_id='. $order_info['order_id'];
            $order_id = $order_info['order_id'];
            $desc = 'پرداخت فروشگاه اپن کارت 2.0 با شماره سفارش ' . $order_info['order_id'];
            $invoice_id             = round(microtime(true) * 1000);
            $user_id = $this->config->get('raypay_user_id');
            $marketing_id = $this->config->get('raypay_marketing_id');
            $sandbox = !($this->config->get('raypay_sandbox') == 'no');

            // Customer information
            $name = $order_info['firstname'] . ' ' . $order_info['lastname'];
            $mail = $order_info['email'];
            $phone = $order_info['telephone'];

            $raypay_data = array(
                'amount'       => strval($amount),
                'invoiceID'    => strval($invoice_id),
                'userID'       => $user_id,
                'redirectUrl'  => $redirectUrl,
                'factorNumber' => strval($order_id),
                'marketingID' => $marketing_id,
                'email'        => $mail,
                'mobile'       => $phone,
                'fullName'     => $name,
                'comment'      => $desc,
                'enableSandBox'=> $sandbox
            );

            $ch = curl_init('https://api.raypay.ir/raypay/api/v1/Payment/pay');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($raypay_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json'
            ));

            $result = curl_exec($ch);
            $result = json_decode($result);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_status != 200 || empty($result) || empty($result->Data)) {
                $msg         = sprintf('خطا هنگام ایجاد تراکنش. کد خطا: %s - پیام خطا: %s', $http_status, $result->Message);
                $data['error_warning'] = $msg;
                $model->addOrderHistory($order_id, 10, $msg, true);
            } else {
                $model->addOrderHistory($order_id, 1, $this->generateString($invoice_id), false);
                $model->addOrderHistory($order_id, 1, 'در حال هدایت به درگاه پرداخت رای پی', false);
                $token = $result->Data;
                $link='https://my.raypay.ir/ipg?token=' . $token;
                $data['action'] = $link;
            }

        } else {
            $data['error_warning'] = $this->language->get('error_curl');
        }

        return $this->load->view('default/template/payment/raypay.tpl', $data);
    }

    /**
     * http request callback
     */
    public function callback()
    {
        $this->load->language('payment/raypay');
        $this->load->model('checkout/order');

        /** @var \ModelCheckoutOrder $model */
        $model = $this->model_checkout_order;

        $this->document->setTitle($this->language->get('heading_title'));

        $order_id = $_GET['order_id'];

        //$order_id = isset($this->session->data['order_id']) ? $this->session->data['order_id'] : false;

        $order_info = $model->getOrder($order_id);
        $data['heading_title'] = $this->language->get('heading_title');
        $data['button_continue'] = $this->language->get('button_continue');
        $data['continue'] = $this->url->link('common/home', '', 'SSL');
        $data['error_warning'] = '';

        if (!$order_info) {
            $comment = $this->raypay_get_failed_message($_POST['invoiceid']);
            $model->addOrderHistory($order_id, 10, $comment, true);
            $data['error_warning'] = $comment;
            $data['button_continue'] = $this->language->get('button_view_cart');
            $data['continue'] = $this->url->link('checkout/cart');

        } else {
            $url = 'https://api.raypay.ir/raypay/api/v1/Payment/verify';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($_POST));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
            ));

            $result = curl_exec($ch);
            $result = json_decode($result);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
                if ($http_status != 200) {
                    $comment = sprintf('خطا هنگام بررسی تراکنش. کد خطا: %s - پیام خطا: %s', $http_status, $result->Message);
                    // Set Order status id to 10 (Failed) and add a history.
                    $model->addOrderHistory($order_id, 10, $comment, true);
                    $data['error_warning'] = $comment;
                    $data['button_continue'] = $this->language->get('button_view_cart');
                    $data['continue'] = $this->url->link('checkout/cart');
                } else {
                    $state           = $result->Data->Status;
                    $verify_order_id = $result->Data->FactorNumber;
                    $verify_invoice_id = $result->Data->InvoiceID;
                    $verify_amount   = $result->Data->Amount;
                    if (empty($verify_order_id) || empty($verify_amount) || $state !== 1) {
                        $comment = $this->raypay_get_failed_message($verify_invoice_id);
                        // Set Order status id to 10 (Failed) and add a history.
                        $model->addOrderHistory($order_id, 10, $comment, true);
                        $data['error_warning'] = $comment;
                        $data['button_continue'] = $this->language->get('button_view_cart');
                        $data['continue'] = $this->url->link('checkout/cart');

                    } else { // Transaction is successful.
                        $comment = $this->raypay_get_success_message($verify_invoice_id);
                        $config_successful_payment_status = $this->config->get('payment_raypay_order_status_id');
                        // Set Order status id to the configured status id and add a history.
                        $model->addOrderHistory($verify_order_id, $config_successful_payment_status, $comment, true);
                        $data['payment_result'] = $comment;
                        $data['button_continue'] = $this->language->get('button_complete');
                        $data['continue'] = $this->url->link('checkout/success');
                    }
                }

        }

        // Breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', '', true)
        );

        if ($data['error_warning']) {
            $data['breadcrumbs'][] = array(

                'text' => $this->language->get('text_basket'),
                'href' => $this->url->link('checkout/cart', '', 'SSL')
            );

            $data['breadcrumbs'][] = array(

                'text' => $this->language->get('text_checkout'),
                'href' => $this->url->link('checkout/checkout', '', 'SSL')
            );
        }

        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('default/template/payment/raypay_callback.tpl', $data));
    }

/**
     * @param $invoice_id
     * @return mixed
     */
    public function raypay_get_success_message($invoice_id)
    {
        return str_replace(["{invoice_id}"], [$invoice_id], $this->config->get('raypay_successful_message'));
    }

    /**
     * @param $invoice_id
     * @return string
     */
    public function raypay_get_failed_message($invoice_id)
    {
        return str_replace(["{invoice_id}"], [$invoice_id], $this->config->get('raypay_failed_message'));
    }

    /**
     * @param $order_info
     * @return int
     */
    private function correctAmount($order_info)
    {
        $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
        $amount = round($amount);
        $amount = $this->currency->convert($amount, $order_info['currency_code'], "RLS");
        return (int)$amount;
    }
}

