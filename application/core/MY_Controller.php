<?php

class MY_Controller extends CI_Controller {
	protected $authorized = false;
	protected $logged_in = false;
	protected $is_admin = false;
	protected $authUrl;
	protected $googleUserData;
	protected $googleClassroomList;
	protected $access_token;

	public function __construct() {
		parent::__construct();

		$this->load->model('Stocksmodel');
		$this->load->model('Adminmodel');
		$this->load->model('Portfoliomodel');
		$this->load->model('Historymodel');

		if( isset( $_SESSION['time'] ) && time() - $_SESSION['time']  > 60 * 30 ){
			redirect('logout', 'refresh');
		}

		include_once APPPATH . "libraries/google-api-php-client-2.2.0/vendor/autoload.php";


		// Store values in variables from project created in Google Developer Console
		$redirect_uri = base_url();

		// Create Client Request to access Google API
		$client = new Google_Client();
		$client->setApplicationName("The Stock Market Game");
		$client->setRedirectUri($redirect_uri);
		$client->addScope("https://www.googleapis.com/auth/userinfo.profile");
		$client->addScope("https://www.googleapis.com/auth/userinfo.email");
		$client->addScope("https://www.googleapis.com/auth/classroom.courses.readonly");

		// Send Client Request
		$OAuthService = new Google_Service_Oauth2( $client );
		$ClassroomService = new Google_Service_Classroom( $client );


		// Add Access Token to Session
		if (isset($_GET['code'])) {
			$client->authenticate($_GET['code']);
			$_SESSION['access_token'] = $client->getAccessToken();
			header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
			$_SESSION['time'] = time();
		}



		// Set Access Token to make Request


		if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
			$client->setAccessToken( $_SESSION['access_token'] );

			// Get User Data from Google and store them in scope vars
			// or
			// create the auth url and store it for rendering by the next controller
			if ( $client->getAccessToken() ) {
				$adminmodel = new Adminmodel();

				$this->googleUserData = $OAuthService->userinfo->get();
				$this->googleClassroomList = $ClassroomService->courses->listCourses();

				//is admin user?
				if($this->googleUserData['email'] == "evan.sharp@coastmountainacademy.ca"){
					$this->is_admin = true;
					$this->authorized = true;

				}else{
					// if not an admin...
					// check for classroom membership and abort if not member

					foreach($this->googleClassroomList['courses'] as $course){
						if( $course['id'] == $adminmodel->get_setting('classroom') ){
							//$this->authorized = true;
						}
					}
				}

				//if an authorized player, set some flags for the requested controller
				if( $this->authorized ){
					$this->access_token = $client->getAccessToken();
					$this->authUrl = '';
					$this->logged_in = true;

					$userdata = $adminmodel->get_user( $this->googleUserData['email'] );
					if( !empty( $userdata ) ){
						$_SESSION['user'] = $userdata;
					}else{
						$adminmodel -> add_user($this->googleUserData['email'], $this->googleUserData['name']);
					}

				}else{
					$this->authUrl = $client->createAuthUrl();
				}
			}

		}else {
			$this->authUrl = $client->createAuthUrl();
		}

	}

	public function index() {
	}
}
