<?php
session_start();

require_once 'library/codebird-php-develop/src/codebird.php';
	
class twitterUi{
	private $cb;
	private $me = null;
	
	public function __construct(){
		\Codebird\Codebird::setConsumerKey('API-KEY', 'API-SECRET'); // static, see 'Using multiple Codebird instances'
		$this->cb = \Codebird\Codebird::getInstance();

		if(! isset($_SESSION['oauth_token'])) {
			$reply = $this->cb->oauth_requestToken(array('oauth_callback' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
		
			$this->cb->setToken($reply->oauth_token, $reply->oauth_token_secret);
			$_SESSION['oauth_token'] = $reply->oauth_token;
			$_SESSION['oauth_token_secret'] = $reply->oauth_token_secret;
			$_SESSION['oauth_verify'] = true;
		
			$auth_url = $this->cb->oauth_authorize();
			header('Location: ' . $auth_url);
			die();
		
		} elseif (isset($_GET['oauth_verifier']) && isset($_SESSION['oauth_verify'])) {
			$this->cb->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
			unset($_SESSION['oauth_verify']);
		
			$reply = $this->cb->oauth_accessToken(array('oauth_verifier' => $_GET['oauth_verifier']));
		
			$_SESSION['oauth_token'] = $reply->oauth_token;
			$_SESSION['oauth_token_secret'] = $reply->oauth_token_secret;
		
			header('Location: ' . 'index.php');
			die();
		}	
		
		$this->cb->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
	}
	
	public function getUserData($username=null){
		if($username){
			$reply = $this->cb->users_show("screen_name=$username");
		}else{
			$reply = $this->cb->account_verifyCredentials();
		}
		return $reply;
	}
	
	public function getUserStatus($userId=null, $username=null){
		if($userId){
			$reply = $this->cb->statuses_userTimeline("screen_name={$userId}&count=10");
		}elseif($username){
			$reply = $this->cb->statuses_userTimeline("screen_name={$username}&count=10");
		}else{
			if(!$this->me){$this->me = $this->getUserData();}
			$reply = $this->cb->statuses_userTimeline("screen_name={$this->me->screen_name}&count=10");
		}
		return $reply;
	}
	
	public function postTweet($tweet){
		return $reply = $this->cb->statuses_update('status='.$tweet);
	}
}
		
$twitterUi = new twitterUi();
switch($_POST['req']){
	case 'tweets':
		$who = ($_POST['who']=='me')?"":$_POST['who'];
		$userData = $twitterUi->getUserStatus($who);
		echo json_encode($userData);
		break;
		
	case 'send':
		$sent = $twitterUi->postTweet($_POST['tweet']);
		echo json_encode(array('sent'=>true));
		break;
}
/*$userData = $twitterUi->getUserStatus(null, 'pastoremetal');
print_r($userData);*/

?>
