<?php

use Dompdf\Dompdf;

/**
 * Class Payment
 * @property Midtrans $midtrans
 */
class Payment extends MY_Controller
{
	public function __construct(){
		parent::__construct();
		if($this->user_session_expired() && $this->router->method != "notification")
			redirect(base_url("site/login"));

		$config = $this->config->item("midtrans");
		$params = array('server_key' =>$config['server_key'], 'production' => (ENVIRONMENT == "production"));
		$this->load->library(['midtrans','veritrans']);
		$this->midtrans->config($params);
		$this->veritrans->config($params);
	}

	public function notification(){
		$json_result = file_get_contents('php://input');
		$result = json_decode($json_result);
		if($result){
			$notif = $this->veritrans->status($result->order_id);
		}
		error_log(print_r($result,TRUE));
		if(isset($notif)) {
			$this->load->model("Transaction_m");
			$update['midtrans_data'] = json_encode($notif);
			$update['channel'] = $notif->payment_type;

			$transaction = $notif->transaction_status;
			$type = $notif->payment_type;
			$fraud = $notif->fraud_status;
			if ($transaction == 'capture') {
				// For credit card transaction, we need to check whether transaction is challenge by FDS or not
				if ($type == 'credit_card'){
					if($fraud == 'challenge'){
						$update['status_payment'] = "Challenge by FDS";
					}
					else {
						$update['status_payment'] = Transaction_m::STATUS_FINISH;
					}
				}
			}
			else if ($transaction == 'settlement'){
				$update['status_payment'] = Transaction_m::STATUS_FINISH;
			}
			else if($transaction == 'pending'){
				$update['status_payment'] = Transaction_m::STATUS_PENDING;
			}
			else if ($transaction == 'deny') {
				$update['status_payment'] = Transaction_m::STATUS_UNFINISH;
			}
			$update['message_payment'] = $notif->status_message;
			$this->Transaction_m->update($update, $notif->order_id);
			if($update['status_payment'] == Transaction_m::STATUS_FINISH){
				$this->load->model("Gmail_api");
				$tr = $this->Transaction_m->findOne($notif->order_id);
				$member = $tr->member;

				$invoice = $tr->exportInvoice()->output();
				$this->Gmail_api->sendMessageWithAttachment($member->email,"INVOICE","Thank you for participating on events, Below is your invoice",$invoice,"INVOICE.pdf");

				$file = $tr->exportPaymentProof()->output();
				$this->Gmail_api->sendMessageWithAttachment($member->email,"Official Payment Proof","Thank you for registering and fulfilling your payment, below is offical payment proof",$file,"OFFICIAL_PAYMENT_PROOF.pdf");
			}
		}
	}

	public function after_checkout(){
		$data = $this->input->post();
		$this->load->model("Transaction_m");
		$this->Transaction_m->update(['checkout'=>1,'message_payment'=>$data['message_payment'],'created_at'=>date("Y-m-d H:i:s")],$data['id']);
	}

	public function checkout(){
		$this->load->model("Transaction_m");
		$user = $this->session->user_session;
		$transaction = $this->Transaction_m->findOne(['member_id'=>$user['id'],'checkout'=>0]);
		if($transaction) {
			$total_price = 0;
			$item_details = [];
			foreach ($transaction->details as $row) {
				$item_details[] = [
					'id' => $row->id,
					'price' => $row->price,
					'quantity' => 1,
					'name' => $row->product_name
				];
				$total_price += $row->price;
			}
			if(count($item_details) == 0){
				$response['status'] = false;
				$response['message'] = "No Transaction available to checkout";
			}else {
				$transaction_details = array(
					'order_id' => $transaction->id,
					'gross_amount' => $total_price,
				);

				$fullname = explode(" ", trim($user['fullname']));
				$firstname = (isset($fullname[0]) ? $fullname[0] : "");
				$lastname = (isset($fullname[1]) ? $fullname[1] : "");
				$billing_address = array(
					'first_name' => $firstname,
					'last_name' => $lastname,
					'address' => $user['address'],
					'city' => $user['city'],
//			'postal_code'   => "",
					'phone' => $user['phone'],
//			'country_code'  => 'IDN'
				);

				$customer_details = array(
					'first_name' => $firstname,
					'last_name' => $lastname,
					'email' => $user['email'],
					'phone' => $user['phone'],
					'billing_address' => $billing_address,
				);

				$credit_card['secure'] = true;

				$time = time();
				$custom_expiry = array(
					'start_time' => date("Y-m-d H:i:s O", $time),
					'unit' => 'day',
					'duration' => 1
				);

				$transaction_data = array(
					'transaction_details' => $transaction_details,
					'item_details' => $item_details,
					'customer_details' => $customer_details,
					'credit_card' => $credit_card,
					'expiry' => $custom_expiry
				);
				try {
					error_log(json_encode($transaction_data));
					$snapToken = $this->midtrans->getSnapToken($transaction_data);
					error_log($snapToken);
					$response['status'] = true;
					$response['token'] = $snapToken;
					$response['invoice'] = $transaction->id;
				} catch (Exception $ex) {
					$response['status'] = false;
					$response['message'] = $ex->getMessage();
				}
			}
		}else{
			$response['status'] = false;
			$response['message'] = "No Transaction available to checkout";
		}
		$this->output->set_content_type("application/json")
			->_display(json_encode($response));
	}


}
