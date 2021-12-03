<?php

class MY_Controller extends CI_Controller {
	protected $authUrl;
	protected $access_token;
	protected $google_user_data;
	protected $google_client;
	public $game_online;

	public function __construct() {
		parent::__construct();

		//user role constants
		defined('USER_ROLE_PLAYER')  OR define('USER_ROLE_PLAYER', 1);
		defined('USER_ROLE_ADMIN')  OR define('USER_ROLE_ADMIN', 4);
		defined('USER_ROLE_GOD')  OR define('USER_ROLE_GOD', 5);

		//load all models in all controllers
		$this->load->model('Stocksmodel');
		$this->load->model('Adminmodel');
		$this->load->model('Portfoliomodel');
		$this->load->model('Historymodel');


		$adminmodel = new Adminmodel();
		$this->authUrl = '';

		$this->game_online = boolval( $adminmodel->get_setting('game_online') );

		include_once APPPATH . "libraries/google-api-client/vendor/autoload.php";

		// Create Client Request to access Google API
		$this->google_client = new Google_Client();
		$this->google_client->setAuthConfig( FCPATH . 'client_secret.json');
		$this->google_client->setApplicationName("The Stock Market Game");
		$this->google_client->setRedirectUri( base_url() );
		$this->google_client->addScope("https://www.googleapis.com/auth/userinfo.profile");
		$this->google_client->addScope("https://www.googleapis.com/auth/userinfo.email");

		$this->google_client->setAccessType('offline');        // offline access
		//$this->google_client->setApprovalPrompt('force');

		// process auth code if doing a new scope auth
		if ( isset( $_GET['code'] ) ) {
			$this->google_client->authenticate($_GET['code']);
			$_SESSION['access_token'] = $this->google_client->getAccessToken();
			$_SESSION['google_refresh_token'] = $this->google_client -> getRefreshToken();

			header('Location: ' . filter_var(base_url(), FILTER_SANITIZE_URL));
		}



		// Set Access Token in Google Client from session in order to make API requests
		if ( isset( $_SESSION['access_token'] ) && $_SESSION['access_token'] ) {

			$this->google_client->setAccessToken( $_SESSION['access_token'] );

			if ($this->google_client->isAccessTokenExpired()) {
				$regenerated_access_token = $this->google_client->fetchAccessTokenWithRefreshToken( $_SESSION['user']['google_refresh_token'] );

				$this->google_client->setAccessToken( $regenerated_access_token );

				$_SESSION['access_token'] = $this->google_client->getAccessToken();

  			}

			if( !isset( $_SESSION['user'] ) ){
				// user only might be registered though, need to try logging them in to find out

				// Get User Data from Google and check for 'new user' case
				$OAuthService = new Google_Service_Oauth2( $this->google_client );
				$this->google_user_data = $OAuthService->userinfo->get();

				$_SESSION['user'] = $this->Adminmodel->get_user( $this->google_user_data['email'] );

				//does user exits yet?
				if( empty( $_SESSION['user'] ) ){
					// no
					// create and proceed
					$game_userdata = $adminmodel -> add_user(
																$this->google_user_data['email'],
																$this->google_user_data['name'],
																USER_ROLE_PLAYER,
																$this->google_user_data['picture'],
																$_SESSION['google_refresh_token']
															);
					$_SESSION['user'] = $game_userdata;
				}

				// record login time
				$adminmodel -> record_login( $_SESSION['user']['id'] );
			}

		}else {
			// user has no session so create a login link
			$this->authUrl = $this->google_client->createAuthUrl();

			//if calling a page wihtout a session, just render the template
			 if( !empty( $this->uri->segment(1) ) ){
			 	//header( 'Location: ' . $this->authUrl );

				$template_data = [
							'title'	=> ucwords($this->uri->segment(1)),
							'active_nav' => $this->uri->segment(1),
							'login_url' => $this->authUrl,
							'game_online' => $this->game_online,
							'page' 	=> null
						];


				die($this->load->view('template', $template_data, true));
			 }
		}



	}

	public function index() {
	}

	public function debug( $inspect = null ){

		if( $inspect == null){
			$inspect = $_SESSION;
		}

		echo "<pre>".print_r( $inspect, true)."</pre>";

		die();
	}
}
