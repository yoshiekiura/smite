<?php

/**
 * Class Area
 * @property Member_m $Member_m
 * @property User_account_m $User_account_m
 */

class Area extends MY_Controller
{
	private $theme;
	public function __construct()
	{
		parent::__construct();
		if ($this->user_session_expired() && $this->router->method != "page")
			redirect(base_url("site/home"));

		$this->theme = $this->config->item("theme");
		$this->layout->setTheme($this->theme);
		$this->layout->setLayout("layouts/$this->theme");
		$this->layout->setBaseView('member/' . $this->theme . '/area/');
		$this->load->model(['Member_m', 'User_account_m', 'Sponsor_link_m']);
		if ($this->session->user_session['role'] != User_account_m::ROLE_MEMBER) {
			redirect(base_url('admin/dashboard'));
		}
	}

	public function index()
	{
		$this->load->model("Transaction_m");
		$user = Member_m::findOne(['username_account' => $this->session->user_session['username']]);
		if (!$user)
			show_error("Member not found in sistem or not registered yet !", 500, "Member not found");

		$data = [
			'user' => $user,
			'statusToUpload' => json_decode(Settings_m::getSetting("status_to_upload"), true) ?? [],
			'isLogin' => $this->user_session_expired()
		];
		$this->layout->render('index', $data);
	}

	public function presentationList()
	{
		$this->load->model("Papers_m");
		$data = $this->Papers_m->findAllPoster();
		$this->output->set_content_type("application/json")
			->_display(json_encode(['status' => true, 'data' => $data]));
	}

	public function certificate($event_id, $member_id)
	{
		$this->load->model(["Event_m", "Member_m"]);
		$member = $this->input->post();
		if (file_exists(APPPATH . "uploads/cert_template/$event_id.txt")) {
			$member = Member_m::findOne(['username_account' => $this->session->user_session['username']])
				->toArray();
			$member['status_member'] = "Peserta";
			$this->Event_m->exportCertificate($member, $event_id)->stream("certificate.pdf", array("Attachment" => false));
		} else {
			show_error("Sertifikat belum tersedia. Sertifikat dapat didownload setelah acara selesai", 400, "Not Yet Available");
		}
	}

	public function card($event_id, $member_id)
	{
		$this->load->model('Member_m');
		$member = $this->Member_m->findOne($member_id);
		try {
			$member->getCard($event_id)->stream($member->fullname . "-member_card.pdf", array("Attachment" => false));
		} catch (ErrorException $ex) {
			show_error($ex->getMessage());
		}
	}

	public function save_profile()
	{
		if ($this->input->post()) {
			$post = $this->input->post();
			$user = Member_m::findOne(['username_account' => $this->session->user_session['username']]);
			$user->setAttributes($post);
			$this->output->set_content_type("application/json")
				->_display(json_encode(['status' => $user->save(false)]));
		} else {
			show_404("Page not found !");
		}
	}

	public function reset_password()
	{
		if ($this->input->post()) {
			$this->load->model('User_account_m');
			$this->load->library('form_validation');

			$this->form_validation->set_rules("confirm_password", "Confirm Password", "required|matches[new_password]")
				->set_rules("new_password", "New Password", "required")
				->set_rules("old_password", "Old Password", [
					'required',
					['verify_old', function ($old_password) {
						return User_account_m::verify($this->session->user_session['username'], $old_password);
					}]
				])->set_message("verify_old", "Old Password Wrong !");

			if ($this->form_validation->run()) {
				$account = User_account_m::findOne(['username' => $this->session->user_session['username']]);
				$account->password = password_hash($this->input->post('new_password'), PASSWORD_DEFAULT);
				$this->output->set_content_type("application/json")
					->_display(json_encode(['status' => $account->save()]));
			} else {
				$this->output->set_content_type("application/json")
					->_display(json_encode($this->form_validation->error_array()));
			}
		} else {
			show_404("Page not found !");
		}
	}

	public function get_events()
	{
		ini_set('memory_limit', '2048M');
		if ($this->input->method() !== 'post')
			show_404("Page not found !");
		$this->load->model("Event_m");
		$events = $this->Event_m->eventVueModel($this->session->user_session['id'], $this->session->user_session['status_name']);
		$this->output->set_content_type("application/json")
			->_display(json_encode(['status' => true, 'events' => $events]));
	}

	public function file_presentation($name, $id)
	{
		$type = "Presentation";
		$filepath = APPPATH . "uploads/papers/" . $name;
		$this->load->model("Papers_m");
		$paper = $this->Papers_m->findOne($id);
		if (file_exists($filepath)) {
			list(, $ext) = explode(".", $name);
			$member = $paper->member;
			header('Content-Description: File Transfer');
			header('Content-Type: ' . mime_content_type($filepath));
			header('Content-Disposition: attachment; filename="' . $type . '-' . $paper->getIdPaper() . '-' . $member->fullname . '.' . $ext . '"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filepath));
			flush(); // Flush system output buffer
			readfile($filepath);
			exit;
		} else {
			show_404('File not found on server !', false);
		}
	}

	public function upload_material()
	{
		$response = [];
		$this->load->library('upload');
		$this->load->model("Member_upload_material_m");
		$type = $this->input->post("type");
		if (isset($_FILES['filename']) && $type == Member_upload_material_m::TYPE_FILE) {
			$config = [
				'upload_path' => APPPATH . 'uploads/material/',
				'allowed_types' => 'doc|docx|jpg|jpeg|png|bmp|ppt|pdf|mp4|ppt|pptx',
				'max_size' => 20480,
				'overwrite' => false,
			];
			$this->upload->initialize($config);
			$status = $this->upload->do_upload('filename');
			$data = $this->upload->data();
			$error = $this->upload->display_errors("", "");
		} else {
			$status = true;
			$data = ['file_name' => $this->input->post("filename")];
		}
		if ($status) {
			$id = $this->input->post("id");
			if ($id && $id != "" && $id != 'null')
				$model = $this->Member_upload_material_m->findOne($id);
			else
				$model = new Member_upload_material_m();

			$model->member_id = $this->session->user_session['id'];
			$model->ref_upload_id = $this->input->post("ref_upload_id");
			$model->type = $this->input->post("type");
			$model->filename = $data['file_name'];
			$response['data']['fullpaper'] = $model->toArray();
			$response['status'] = $model->save(false);
		} else {
			$response['status'] = false;
			$response['message'] = $error;
		}
		$this->output->set_content_type("application/json")
			->_display(json_encode($response));
	}

	public function list_material()
	{
		$this->load->model("Ref_upload_m");
		$data = $this->Ref_upload_m->getListMaterialMember($this->session->user_session['id']);
		$this->output->set_content_type("application/json")
			->_display(json_encode(['status' => true, 'data' => $data]));
	}

	public function file_material($name, $type)
	{
		$type = urldecode($type);
		$filepath = APPPATH . "uploads/material/" . $name;
		if (file_exists($filepath)) {
			list(, $ext) = explode(".", $name);
			header('Content-Description: File Transfer');
			header('Content-Type: ' . mime_content_type($filepath));
			header('Content-Disposition: attachment; filename="' . $type . '.' . $ext . '"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filepath));
			flush(); // Flush system output buffer
			readfile($filepath);
			exit;
		} else {
			show_404('File not found on server !', false);
		}
	}

	public function delete_paper()
	{
		if ($this->input->method() !== 'post')
			show_404("Page not found !");
		$data = $this->input->post();
		$this->load->model("Papers_m");
		$status = $this->Papers_m->delete(['id' => $data['id']]);
		if (file_exists(APPPATH . "uploads/papers/" . $data['filename']) && is_file(APPPATH . "uploads/papers/" . $data['filename']))
			unlink(APPPATH . "uploads/papers/" . $data['filename']);
		if (file_exists(APPPATH . "uploads/papers/" . $data['feedback']) && is_file(APPPATH . "uploads/papers/" . $data['feedback']))
			unlink(APPPATH . "uploads/papers/" . $data['feedback']);

		$this->output->set_content_type("application/json")
			->_display(json_encode(['status' => $status]));
	}

	public function get_paper()
	{
		if ($this->input->method() !== 'post')
			show_404("Page not found !");
		$this->load->model(["Papers_m", "Category_paper_m"]);
		$papers = Papers_m::findAll(['member_id' => $this->session->user_session['id']]);
		// $response['abstractType'] = Papers_m::$typeAbstract;
		$response['status'] = Papers_m::$status;
		// $response['typeStudy'] = Papers_m::$typeStudy;
		$response['typePresention'] = Papers_m::$typePresentation;
		$response['deadline'] = [
			'paper_deadline' => Settings_m::getSetting('paper_deadline'),
			'paper_cutoff' => Settings_m::getSetting('paper_cutoff'),
			'fullpaper_deadline' => Settings_m::getSetting('fullpaper_deadline'),
			'fullpaper_cutoff' => Settings_m::getSetting('fullpaper_cutoff'),
			'presentation_deadline' => Settings_m::getSetting('presentation_deadline'),
			'presentation_cutoff' => Settings_m::getSetting('presentation_cutoff'),
		];
		$response['declaration'] = Papers_m::$declaration;
		// NOTE Category Paper
		$categoryPaper = $this->Category_paper_m->find()->order_by("name")->get();
		$categoryPaper = $categoryPaper->result_array();
		$response['categoryPaper'] = Category_paper_m::asList($categoryPaper, "id", "name");
		$treePaper = [];
		foreach ($categoryPaper as $key => $value) {
			$treePaper[$value['name']] = json_decode($value['tree'], true);
		}
		$response['treePaper'] = $treePaper;

		$response['data'] = [];
		$formatId = Settings_m::getSetting("format_id_paper");
		foreach ($papers as $paper) {
			$temp = $paper->toArray();
			$temp['id_paper'] = $formatId . str_pad($temp['id'], 3, 0, STR_PAD_LEFT);
			$methods = explode(":", $temp['methods']);
			if (count($methods) > 1) {
				$temp['methods'] = $methods[0];
				$temp['type_study_other'] = trim($methods[1]);
			}
			$temp['co_author'] = json_decode($temp['co_author'], true);
			$category_paper = $paper->category_paper ? $paper->category_paper->toArray() : [];
			$response['data'][] = array_merge($temp, ['category_paper' => $category_paper]);
		}
		$this->output->set_content_type("application/json")
			->_display(json_encode($response));
	}

	public function add_cart()
	{
		if ($this->input->method() !== 'post')
			show_404("Page not found !");

		$response = ['status' => true];
		$data = $this->input->post();
		$this->load->model(["Transaction_m", "Transaction_detail_m", "Event_m", "Event_pricing_m"]);
		$this->Transaction_m->getDB()->trans_start();
		$transaction = $this->Transaction_m->findOne(['member_id' => $this->session->user_session['id'], 'checkout' => 0]);
		if (!$transaction) {
			$id = $this->Transaction_m->generateInvoiceID();
			$transaction = new Transaction_m();
			$transaction->id = $id;
			$transaction->checkout = 0;
			$transaction->status_payment = Transaction_m::STATUS_WAITING;
			$transaction->member_id = $this->session->user_session['id'];
			$transaction->save();
			$transaction->id = $id;
		}
		$detail = $this->Transaction_detail_m->findOne(['transaction_id' => $transaction->id, 'event_pricing_id' => $data['id']]);
		$fee = $this->Transaction_detail_m->findOne(['transaction_id' => $transaction->id, 'event_pricing_id' => 0]);
		if (!$detail) {
			$detail = new Transaction_detail_m();
		}
		$feeAlready = false;
		if (!$fee) {
			$fee = new Transaction_detail_m();
		} else {
			$feeAlready = true;
		}

		if ($this->Event_m->validateFollowing($data['id'], $this->session->user_session['status_name'])) {

			// NOTE Harga sesuai dengan database
			$price = $this->Event_pricing_m->findOne(['id' => $data['id'], 'condition' => $this->session->user_session['status_name']]);
			if ($price->price != 0) {
				$data['price'] = $price->price;
			} else {
				$kurs_usd = json_decode(Settings_m::getSetting('kurs_usd'), true);
				$data['price'] = ($price->price_in_usd * $kurs_usd['value']);
			}

			$detail->event_pricing_id = $data['id'];
			$detail->transaction_id = $transaction->id;
			$detail->price = $data['price'];
			$detail->price_usd = $price->price_in_usd;
			$detail->member_id = $this->session->user_session['id'];
			$detail->product_name = "$data[event_name] ($data[member_status])";
			$detail->save();
			if ($data['price'] > 0 && $feeAlready == false) {
				$fee->event_pricing_id = 0; //$data['id'];
				$fee->transaction_id = $transaction->id;
				$fee->price = 5000 + rand(100, 500); //"6000";//$data['price'];
				$fee->member_id = $this->session->user_session['id'];
				$fee->product_name = "Admin Fee";
				$fee->save();
			}
		} else {
			$response['status'] = false;
			$response['message'] = "You are prohibited from following !";
		}
		$this->Transaction_m->getDB()->trans_complete();

		$this->output->set_content_type("application/json")
			->_display(json_encode($response));
	}


	public function get_transaction()
	{
		if ($this->input->method() !== 'post')
			show_404("Page not found !");

		$this->load->model(["Transaction_m"]);
		$transactions = $this->Transaction_m->findAll(['member_id' => $this->session->user_session['id']]);
		$response = ['status' => true, 'cart' => null, 'transaction' => null];
		foreach ($transactions as $trans) {
			if ($trans->checkout == 0) {
				$response['current_invoice'] = $trans->id;
				foreach ($trans->details as $row) {
					$response['cart'][] = $row->toArray();
				}
			} else {
				$detail = [];
				foreach ($trans->details as $row) {
					$detail[] = $row->toArray();
				}
				$response['transaction'][] = array_merge($trans->toArray(), ['detail' => $detail]);
			}
		}
		$response['paymentMethod'] = Settings_m::getEnablePayment();
		$this->output->set_content_type("application/json")
			->_display(json_encode($response));
	}

	public function delete_item_cart()
	{
		if ($this->input->method() !== 'post')
			show_404("Page not found !");
		$id = $this->input->post('id');
		$this->load->model(["Transaction_detail_m"]);
		$this->Transaction_detail_m->delete($id);
		$count = $this->Transaction_detail_m->find()->select("SUM(price) as c")
			->where('transaction_id', $this->input->post("transaction_id"))
			->where('event_pricing_id > ', "0")
			->get()->row_array();
		if ($count['c'] == 0) {
			$this->Transaction_detail_m->delete(['event_pricing_id' => 0, 'transaction_id' => $this->input->post("transaction_id")]);
		}
		$this->output->set_content_type("application/json")
			->_display('{"status":true}');
	}

	public function file($name, $id, $type = 'Abstract')
	{
		$name = basename($name);
		$filepath = APPPATH . "uploads/papers/" . $name;
		$this->load->model("Papers_m");
		$paper = $this->Papers_m->findOne($id);

		if ($type == 'Voice Recording') {
			$type = 'Voice';
		} else if (in_array($type, ['Moderated Poster', 'Viewed Poster', 'Oral'])) {
			$type = "Present";
		}

		if (file_exists($filepath)) {

			list(, $ext) = explode(".", $name);

			$dataTitle = explode(" ", $paper->title);
			$title = count($dataTitle) > 3 ? "{$dataTitle[0]} {$dataTitle[1]} {$dataTitle[2]}" : implode(" ", $dataTitle);

			$member = $paper->member;

			// $filename = $type . '-' . $paper->getIdPaper() . '-' . $member->fullname . '.' . $ext;
			$filename = "{$paper->getIdPaper()}-{$type}-{$member->fullname}-{$title}.{$ext}";

			header('Content-Description: File Transfer');
			header('Content-Type: ' . mime_content_type($filepath));
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filepath));
			flush(); // Flush system output buffer
			readfile($filepath);
			exit;
		}
	}

	public function upload_fullpaper()
	{
		$response = [];
		$this->load->library('upload');

		$type = $this->input->post('type_presence');
		$ext = 'mp3|m4a';
		if ($type == 'Oral') {
			$ext = 'ppt|pptx|pdf';
		} else if ($type == 'Moderated Poster' || $type == 'Viewed Poster') {
			$ext = 'jpg|png|jpeg|ppt';
		}

		$configFullpaper = [
			'upload_path' => APPPATH . 'uploads/papers/',
			'allowed_types' => 'doc|docx|ods',
			'max_size' => 20480,
			'file_name' => 'fullpaper_' . date("Ymdhis"),
		];
		$configPresentation = [
			'upload_path' => APPPATH . 'uploads/papers/',
			'allowed_types' => $ext,
			'max_size' => 5000,
			'file_name' => 'presentation_' . date("Ymdhis"),
		];
		$this->upload->initialize($configFullpaper);
		$statusFullpaper = $this->upload->do_upload('fullpaper');
		$dataFullpaper = $this->upload->data();
		$errorFullpaper = $this->upload->display_errors("", "");

		$this->upload->initialize($configPresentation);
		$statusPresentation = $this->upload->do_upload('presentation');
		$dataPresentation = $this->upload->data();
		$errorPresentation = $this->upload->display_errors("", "");

		if ($statusFullpaper || $statusPresentation) {
			$this->load->model("Papers_m");
			$paper = $this->Papers_m->findOne($this->input->post("id"));
			if ($statusFullpaper) {
				$paper->fullpaper = $dataFullpaper['file_name'];
				$paper->time_upload_fullpaper = date("Y-m-d H:i:s");
				$paper->status_fullpaper = 1;
				$response['data']['fullpaper'] = $paper->fullpaper;
			}
			if ($statusPresentation) {
				$paper->poster = $dataPresentation['file_name'];
				$paper->status_presentasi = 1;
				$paper->time_upload_presentasi = date("Y-m-d H:i:s");
				$response['data']['poster'] = $paper->poster;
			}
			$response['status'] = $paper->save(false);
		} else {
			$response['status'] = false;
			$response['message']['fullpaper'] = $errorFullpaper;
			$response['message']['presentation'] = $errorPresentation;
		}
		$this->output->set_content_type("application/json")
			->_display(json_encode($response));
	}

	public function upload_paper()
	{
		if ($this->input->method() !== 'post')
			show_404("Page not found !");

		$config['upload_path']          = APPPATH . 'uploads/papers/';
		$config['allowed_types']        = 'pdf|doc|docx|ods';
		$config['max_size']             = 20480;
		$config['overwrite']             = true;
		$config['file_name']        = 'abstract' . date("Ymdhis"); //$this->session->user_session['id'];

		$this->load->library('upload', $config);
		$this->load->model("Papers_m");
		$upload = $this->upload->do_upload('file');
		$validation = $this->Papers_m->validate($this->input->post());
		if ($upload && $validation) {
			$paper = Papers_m::findOne(['id' => $this->input->post('id')]);
			if (!$paper)
				$paper = new Papers_m();

			$this->load->model(["Category_paper_m"]);
			$category = $this->Category_paper_m->find()->where('name', $this->input->post('category'))->get()->row_array();

			$data = $this->upload->data();
			$paper->member_id = $this->session->user_session['id'];
			$paper->filename = $data['file_name'];
			$paper->status = 1;
			$paper->title = $this->input->post('title');
			$paper->type = $this->input->post('type');
			$paper->introduction = $this->input->post('introduction');
			$paper->methods = $this->input->post('methods');
			$paper->category = $category['id'];
			if ($this->input->post("type_study_other")) {
				$paper->methods = $paper->methods . ": " . $this->input->post("type_study_other");
			}
			//            $paper->result = $this->input->post('result');
			//            $paper->aims = $this->input->post('aims');
			//            $paper->conclusion = $this->input->post('conclusion');
			$paper->type_presence = $this->input->post('type_presence');
			$paper->reviewer = "";
			$paper->message = "";
			$paper->co_author = json_encode($this->input->post('co_author'));
			$paper->created_at = date("Y-m-d H:i:s");
			$paper->save();
			$paper->updated_at = date("Y-m-d H:i:s");
			$response['status'] = true;
			$response['paper'] = $paper->toArray();
		} else {
			$response['status'] = false;
			$response['message'] = array_merge($this->Papers_m->getErrors(), ['file' => $this->upload->display_errors("", "")]);
		}

		$this->output->set_content_type("application/json")
			->_display(json_encode($response));
	}

	public function upload_image()
	{
		$user = Member_m::findOne(['username_account' => $this->session->user_session['username']]);

		$config['upload_path']          = 'themes/uploads/profile/';
		$config['allowed_types']        = 'jpg|png|pdf';
		$config['max_size']             = 2048;
		$config['overwrite']             = true;
		$config['file_name']        = $user->id;

		$this->load->library('upload', $config);
		if ($this->upload->do_upload('file')) {
			$data = $this->upload->data();
			$user->image = $data['file_name'];
			$user->save();
			$response['status'] = true;
			$response['link'] = base_url("themes/uploads/profile/$data[file_name]");
		} else {
			$response['status'] = false;
			$response['message'] = $this->upload->display_errors("", "");
		}

		$this->output->set_content_type("application/json")
			->_display(json_encode($response));
	}

	public function page($name)
	{
		$this->layout->renderAsJavascript($name . '.js');
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('site');
	}

	public function upload_proof()
	{
		if ($this->input->method() != 'post')
			show_404("Page Not Found !");
		$id = $this->input->post("invoice_id");
		$message = $this->input->post("message");
		$config['upload_path']          = APPPATH . 'uploads/proof/';
		$config['allowed_types']        = 'jpg|png|jpeg|pdf';
		$config['max_size']             = 2048;
		$config['overwrite']             = true;
		$config['file_name']        = $id;

		$this->load->library('upload', $config);
		if ($this->upload->do_upload('file_proof')) {
			$data = $this->upload->data();

			$this->load->model(["Transaction_m", "Notification_m"]);
			$tran = $this->Transaction_m->findOne($id);
			$tran->client_message = $message;
			$tran->payment_proof =  $data['file_name'];
			$tran->status_payment = Transaction_m::STATUS_NEED_VERIFY;
			$data['status_payment'] =  Transaction_m::STATUS_NEED_VERIFY;
			$mem = $this->Member_m->findOne($tran->member_id);
			$response['status'] = $tran->save();
			$response['data'] = $data;
			if ($response['status'] && Settings_m::getSetting("email_receive") != "") {
				$email_message = "$mem->fullname has upload a transfer proof with invoice id <b>$tran->id</b>";
				$file[$data['file_name']] = file_get_contents(APPPATH . 'uploads/proof/' . $data['file_name']);
				$emails = explode(",", Settings_m::getSetting("email_receive"));
				foreach ($emails as $email) {
					$this->Notification_m->sendMessageWithAttachment($email, 'Notification Upload Transfer Proof', $email_message, $file);
				}
			}
		} else {
			$response['status'] = false;
			$response['message'] = $this->upload->display_errors("", "");
		}
		$this->output
			->set_content_type("application/json")
			->_display(json_encode($response));
	}

	public function detail_transaction()
	{
		if ($this->input->method() != 'post')
			show_404("Page Not Found !");

		$this->load->model("Transaction_m");
		$id = $this->input->post('id');
		$detail = $this->Transaction_m->findOne($id);
		if ($detail) {
			$response = $detail->toArray();
			$response['member'] = $detail->member->toArray();
			$response['finish'] = $response['status_payment'] == Transaction_m::STATUS_FINISH;
			foreach ($detail->details as $row) {
				$response['details'][] = $row->toArray();
			}
		}
		$response['banks'] = json_decode(Settings_m::getSetting(Settings_m::MANUAL_PAYMENT), true);

		$this->output
			->set_content_type("application/json")
			->_display(json_encode($response));
	}

	public function download($type, $id)
	{
		$this->load->model(['Transaction_m', 'Member_m']);
		$tr = $this->Transaction_m->findOne(['id' => $id]);
		$member = $this->Member_m->findOne(['id' => $tr->member_id]);
		if ($type == "invoice")
			$tr->exportInvoice()->stream($member->fullname . "-Invoice.pdf", array("Attachment" => false));
		elseif ($type == "proof")
			$tr->exportPaymentProof()->stream($member->fullname . "-Bukti_Registrasi.pdf", array("Attachment" => false));
		else
			show_404();
	}

	public function count_followed_events()
	{
		$this->load->model(['Transaction_m', 'Member_m', 'Univ_m', 'Country_m']);
		$c = $this->Member_m->countFollowedEvent($this->session->user_session['id']);
		$univ = $this->Univ_m->find()->order_by("univ_nama")->get();
		$country = $this->Country_m->find()->order_by("name")->get();

		$this->output
			->set_content_type("application/json")
			->_display(json_encode([
				'status' => true,
				'count' => $c,
				'univ' => Univ_m::asList($univ->result_array(), 'univ_id', 'univ_nama'),
				'country' => Country_m::asList($country->result_array(), 'id', 'name')
			]));
	}

	public function redirect_client($name)
	{
		redirect(base_url("member/area#/$name"));
	}
}
