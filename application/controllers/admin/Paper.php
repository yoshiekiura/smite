<?php


class Paper extends Admin_Controller
{

	public function index()
	{
		$this->load->model(["Papers_m", "User_account_m"]);
		$adminPaper = [];
		foreach ($this->User_account_m->findAll(['role' => User_account_m::ROLE_ADMIN_PAPER]) as $row) {
			$t = $row->toArray();
			unset($t['password'],$t['role'],$t['token_reset']);
			$adminPaper[] = $t;
		}
		$this->layout->render('paper', [
			'admin_paper' => $adminPaper
		]);
	}


	public function grid()
	{
		$this->load->model(['User_account_m','Papers_m']);
		if($this->session->user_session['role'] == User_account_m::ROLE_ADMIN_PAPER) {
			$gridConfig = $this->Papers_m->gridConfig(['filter' => ['reviewer' => $this->session->user_session['username']]]);
		}else{
			$gridConfig = $this->Papers_m->gridConfig();
		}
		$grid = $this->Papers_m->gridData($this->input->get(),$gridConfig);
		$this->output
			->set_content_type("application/json")
			->_display(json_encode($grid));
	}

	public function save()
	{
		$this->load->library('form_validation');
		$this->load->model('Papers_m');
		$data = $this->input->post();
		$response = [];

		$this->form_validation->set_rules("status", "Status", "required");
		$model = $this->Papers_m->findOne($data['t_id']);
		if (isset($data['status']) && $data['status'] == 0 && $model->reviewer != "")
			$this->form_validation->set_rules("message", "Message", "required");

		if ($this->form_validation->run()) {
			if(isset($_POST['feedback_file']) && isset($_POST['filename_feedback'])) {
				$dataFile = $_POST['feedback_file'];
				list(, $dataFile) = explode(',', $dataFile);
				$dataFile = base64_decode($dataFile);
				list(, $ext) = explode(".", $this->input->post('filename_feedback'));
				$filename = "feedback_".date("Ymdhis").".".$ext;
				file_put_contents(APPPATH . "uploads/papers/$filename", $dataFile);
				$model->feedback = $filename;
			}
			$model->status = $data['status'];
			$model->type_presence = $data['type_presence'];
			$model->reviewer = (isset($data['reviewer']) ? $data['reviewer'] : "");
			if(isset($data['message']))
				$model->message = $data['message'];

			$response['status'] = $model->save();
		} else {
			$response['status'] = false;
			$response['message'] = $this->form_validation->error_string();
		}

		$this->output
			->set_content_type("application/json")
			->_display(json_encode($response));
	}

	public function file($name,$member)
	{
		$filepath = APPPATH . "uploads/papers/" . $name;
		if (file_exists($filepath)) {
			list(,$ext) = explode(".",$name);
			header('Content-Description: File Transfer');
			header('Content-Type: ' . mime_content_type($filepath));
			header('Content-Disposition: attachment; filename="Paper-' . $member . '.'.$ext.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filepath));
			flush(); // Flush system output buffer
			readfile($filepath);
			exit;
		}
	}
}
