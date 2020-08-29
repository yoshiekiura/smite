<?php

/**
 * Class Event
 * @property Event_m $Event_m
 */
class Event extends Admin_Controller
{
	protected $accessRule = [
		'index'=>'view',
		'grid'=>'view',
		'delete'=>'delete',
		'detail'=>'view',
		'save'=>'insert',
		'delete_pricing'=>'delete',
	];
    public function index()
    {
        $this->load->model('Category_member_m');
        $participantsCategory = Category_member_m::asList(Category_member_m::findAll(), 'id', 'kategory');
        $pricingDefault = [];
        foreach ($participantsCategory as $cat) {
            $pricingDefault[] = ['condition' => $cat, 'price' => 0,'show'=>'1'];
        }
        $this->load->helper('form');
        $this->layout->render("event", ['pricingDefault' => $pricingDefault]);
    }

    public function grid()
    {
        $this->load->model('Event_m');

        $grid = $this->Event_m->gridData($this->input->get());
        $this->output
            ->set_content_type("application/json")
            ->_display(json_encode($grid));

    }

    public function delete(){
        if($this->input->is_ajax_request()){
            $id = $this->input->post('id');
            $this->load->model(['Event_m','Event_pricing_m']);
            $this->Event_m->getDB()->trans_start();
            $this->Event_m->delete(['id'=>$id]);
            $this->Event_pricing_m->delete(['event_id'=>$id]);
            $this->Event_m->getDB()->trans_complete();
            if($this->Event_m->getDB()->trans_status() == false)
                $this->output->set_status_header(500);
        }else {
            $this->output->set_status_header(403);
        }
    }

    public function detail(){
        if($this->input->is_ajax_request()){
            $id = $this->input->post('id');
            $this->load->model(['Event_m','Event_pricing_m']);
            $event = $this->Event_m->findOne(['id'=>$id]);
            $data = $event->toArray();
            $data['special_link'] = $data['special_link'] == "" || $data['special_link'] == "null" ? [] : json_decode($data['special_link']);
            $data['event_pricing'] = $this->Event_pricing_m->reverseParseForm($event->event_pricings);
            $this->output
                ->set_content_type("application/json")
                ->_display(json_encode($data));
        }else {
            $this->output->set_status_header(403);
        }
    }

    public function save()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model(['Event_m', 'Event_pricing_m']);

            $data = $this->input->post(null,false);
            $pricing = $this->Event_pricing_m->parseForm($data);

            $cekEv = $this->Event_m->validate($data);
            $cekEvPrice = $this->Event_pricing_m->validate($pricing);

            if ($cekEv && $cekEvPrice) {
                $this->Event_m->getDB()->trans_start();
                $eventData = $data;
                unset($eventData['event_pricing']);
                $event_id = null;
                if(isset($data['id'])){
                    $event = Event_m::findOne(['id'=>$data['id']]);
                    if(!$event) {
                        $event = new Event_m();
                    }else{
                        $event_id = $data['id'];
                    }
                }else{
                    $event = new Event_m();
                }

                $event->setAttributes($eventData);
                $event->special_link = ($this->input->post("special_link") ? json_encode($event->special_link):"[]");
                $event->save(false);
//                $this->Event_m->insert($event, false);
                if(!$event_id)
                    $event_id = $this->Event_m->getLastInsertID();

                $pricing = $this->Event_pricing_m->parseForm($data, $event_id);
                foreach($pricing as $row){
                    if(isset($row['id'])){
                        $event_pricing = Event_pricing_m::findOne(['id'=>$row['id']]);
                        if(!$event_pricing)
                            $event_pricing = new Event_pricing_m();
                    }else{
                        $event_pricing = new Event_pricing_m();
                    }
                    $event_pricing->setAttributes($row);
                    $event_pricing->save(false);
//                    $this->Event_pricing_m->batchInsert($pricing, false);

                }
                $this->Event_m->getDB()->trans_complete();
                $this->output->_display(json_encode(['status' => true, 'msg' => 'Data saved successfully !']));
            } else {
                $error = array_merge($this->Event_m->getErrors(), $this->Event_pricing_m->getErrors());
                $this->output->set_status_header(400)
                    ->set_content_type("application/json")
                    ->_display(json_encode($error));
            }
        } else {
            $this->output->set_status_header(403);
        }
    }

    public function delete_pricing(){
    	if(!$this->input->method() == "post")
    		show_404();
    	$prices = $this->input->post('price');
    	$ids = [];
    	foreach($prices as $row){
    		$ids[] = $row['id'];
		}
		$this->load->model(['Event_pricing_m','Transaction_detail_m']);
    	$c = $this->Transaction_detail_m->find()->select("count(*) as c")->where_in("event_pricing_id",$ids)->get()->row_array();
    	if($c['c'] > 0){
    		$response['status'] = false;
    		$response['message'] = "Cannot delete this pricing, The Pricing has been added in participant transaction !";
		}else{
    		$status = $this->Event_pricing_m->find()->where_in("id",$ids)->delete();
			$response['status'] = $status;
    		if($status == false){
    			$response['message'] = "Failed to delete, error in server !";
			}
		}

		$this->output->set_content_type("application/json")
			->_display(json_encode($response));
	}

}
