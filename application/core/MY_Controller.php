<?php

class MY_Controller extends CI_Controller {
		protected $authUrl;
		protected $access_token;
		protected $google_user_data;
		protected $google_client;
		public $game_online;

	public function __construct() {
		parent::__construct();

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

		// Set Access Token from session for requests
		if ( isset($_SESSION['access_token']) && $_SESSION['access_token'] ) {

			$this->google_client->setAccessToken( $_SESSION['access_token'] );

			if ($this->google_client->isAccessTokenExpired()) {
				$regenerated_access_token = $this->google_client->fetchAccessTokenWithRefreshToken( $_SESSION['user']['google_refresh_token'] );

				$this->google_client->setAccessToken( $regenerated_access_token );

				$_SESSION['access_token'] = $this->google_client->getAccessToken();

  			}

			if( isset( $_SESSION['user'] ) ){
				// user has an active session, db data already in sesson
				// no need to do anything but get on with the requested controller

			}else{
					// user only might exist, need to log them in to find out

					// Get User Data from Google and check for 'new user' case
					$OAuthService = new Google_Service_Oauth2( $this->google_client );
					$this->google_user_data = $OAuthService->userinfo->get();

					//is admin user or an authorized user?
					$_SESSION['user_role'] = 'unauthorized';

					if( $this->google_user_data['email'] == "evan.sharp@coastmountainacademy.ca"){
						$_SESSION['user_role'] = 'admin';
					}else{
						$_SESSION['user_role'] = 'player';
					}

					// at this point, if user_role not updated above, let them fall-through as 'unauthorized'
					if( $_SESSION['user_role'] != 'unauthorized' ){

						$_SESSION['user'] = $this->Adminmodel->get_user( $this->google_user_data['email'] );

						//does user exits yet?
						if( empty( $_SESSION['user'] ) ){
							// no
							// create and proceed
							$game_userdata = $adminmodel -> add_user(
																		$this->google_user_data['email'],
																		$this->google_user_data['name'],
																		$this->google_user_data['picture'],
																		$_SESSION['google_refresh_token']
																	);
							$_SESSION['user'] = $game_userdata;
						}

						// record login time
						$adminmodel -> record_login( $_SESSION['user']['id'] );


					}
			}
		}else {
			// user has no session so create a login link
			$this->authUrl = $this->google_client->createAuthUrl();

			//die('here');

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

		die("<pre>".print_r( $inspect, true)."</pre>");
	}
}
