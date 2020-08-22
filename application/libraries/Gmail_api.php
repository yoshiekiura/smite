<?php
class Gmail_api implements iNotification
{
    protected $primaryKey = "name";
    protected $table = "settings";
    protected $client = null;

    const NAME_SETTINGS = "gmail_api";
    const EMAIL_ADMIN_SETTINGS = "email_admin";

    protected $sender;
    protected $token;
    protected $email;

    public function __construct($params = [])
    {
        $this->sender = (isset($params['sender']) ? $params['sender']:'');
        $this->token = (isset($params['token']) ? $params['token']:'');
        $this->email = (isset($params['email']) ? $params['email']:'');
    }

    /**
     * @return array|null
     */
    public function getToken(){
        
        return $this->token;
    }

    /**
     * @return string
     */
    public function getEmail(){
        return $this->email;
    }

    /**
     * @return string
     */
    public function getSender(){
        return $this->sender;
    }


    /**
     * @return Google_Client
     * @throws Google_Exception
     */
    public function getClient(){
        if($this->client == null) {
            $this->client = new Google_Client();
            $this->client->setScopes([
                Google_Service_Gmail::GMAIL_SEND,
                Google_Service_Gmail::MAIL_GOOGLE_COM
            ]);
            $this->client->setAuthConfig(APPPATH . 'config/google.client.oauth.json');
            $this->client->setAccessType('offline');
            $this->client->setPrompt('select_account consent');
            $this->client->setRedirectUri(base_url() . "admin/setting/token_auth");
            $token = $this->getToken();
            if ($token) {
                $this->client->setAccessToken($token);
                if ($this->client->isAccessTokenExpired()) {
                    $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                }
            }
        }
        return $this->client;
    }

    public function sendMessage($to,$subject,$message){
		$message.="<br/><br/><br/><hr/><small>This is an automated email, Please do not reply to this message.</small>";

		$from = $this->getEmail();
        $sender = $this->getSender();

        $service = new Google_Service_Gmail($this->getClient());
        $strSubject = $subject;
        $strRawMessage = "From:  $sender<".$from.">\r\n";
        $strRawMessage .= "To:  <".$to.">\r\n";
        $strRawMessage .= 'Subject: =?utf-8?B?' . base64_encode($strSubject) . "?=\r\n";
        $strRawMessage .= "MIME-Version: 1.0\r\n";
        $strRawMessage .= "Content-Type: text/html; charset=utf-8\r\n";
        $strRawMessage .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
        $strRawMessage .= $message."\r\n";
        // The message needs to be encoded in Base64URL
        $mime = rtrim(strtr(base64_encode($strRawMessage), '+/', '-_'), '=');
        try{
            $msg = new Google_Service_Gmail_Message();
            $msg->setRaw($mime);
            $status = $service->users_messages->send("me", $msg);
            $response['status'] = $status;
        }catch(Exception $e){
            $response['status'] = false;
            $response['code'] = $e->getCode();
            $response['message'] = $e->getMessage();

        }
        return $response;
    }

	public function sendMessageWithAttachment($to,$subject,$message,$attachment,$fname = ""){
    	$message.="<br/><br/><br/><hr/><small>This is an automated email, Please do not reply to this message.</small>";
    	$files = [];
    	if(!is_array($attachment)){
    		$files[$fname] = $attachment;
		}else{
    		$files = $attachment;
		}
		$from = $this->getEmail();
		$sender = $this->getSender();
		$finfo = new finfo(FILEINFO_MIME);
		$boundary = uniqid(rand(), true);
		$service = new Google_Service_Gmail($this->getClient());
		$strSubject = $subject;
		$strRawMessage = "From:  $sender<".$from.">\r\n";
		$strRawMessage .= "To:  <".$to.">\r\n";
		$strRawMessage .= 'Subject: =?utf-8?B?' . base64_encode($strSubject) . "?=\r\n";
		$strRawMessage .= "MIME-Version: 1.0\r\n";
		$strRawMessage .= 'Content-type: Multipart/Mixed; boundary="' . $boundary . '"' . "\r\n";
		$strRawMessage .= "\r\n--{$boundary}\r\n";
		$strRawMessage .= "Content-Type: text/html; charset=utf-8\r\n";
		$strRawMessage .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
		$strRawMessage .= $message."\r\n";
		foreach($files as $filename => $attc){
			$mimeType = $finfo->buffer($attc);
			$strRawMessage .= "--{$boundary}\r\n";
			$strRawMessage .= 'Content-Type: ' . $mimeType . '; name="' . $filename . '";' . "\r\n";
			$strRawMessage .= 'Content-Transfer-Encoding: base64' . "\r\n\r\n";
			$strRawMessage .= base64_encode($attc) . "\r\n";
		}

		// The message needs to be encoded in Base64URL
		$mime = rtrim(strtr(base64_encode($strRawMessage), '+/', '-_'), '=');
		$msg = new Google_Service_Gmail_Message();
		$msg->setRaw($mime);
		$status = $service->users_messages->send("me", $msg);
		return $status;
	}
}