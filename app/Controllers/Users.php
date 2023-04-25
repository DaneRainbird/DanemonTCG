<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Libraries\OktaService;
use Exception;

class Users extends BaseController {
    // Okta service
    private $okta;

    /**
     * Constructor
     */
    public function __construct() {
        $this->okta = new OktaService();
    }

        /**
     * Login function
     * 
     * Begins the Okta login process
     * 
     * @return CodeIgniter\HTTP\RedirectResponse::to redirect to the home page 
     */
    public function login() {
        // If the user isn't already logged in, then generate an authorization url
        if (!session()->get('username')) {
            $state = bin2hex(random_bytes(5));
            $authorizeUrl = $this->okta->buildAuthorizeUrl($state);
            session()->set('state', $state);

            // Redirect to the authorisation URL on Okta's servers
            return redirect()->to($authorizeUrl);
        }

        // If user is already logged in, redirect to return URL or home page
        $returnUrl = session()->get('returnUrl') ?? session()->get('_ci_previous_url');

        // Remove the session data
        session()->remove('returnUrl');
        session()->remove('_ci_previous_url');

        // If the return URL is not set, redirect to the home page
        if (!$returnUrl) {
            return redirect()->to('/');
        }

        // If the return URL is the login page, redirect to the home page
        if ($returnUrl == '/login') {
            return redirect()->to('/');
        }

        // Redirect to the return URL
        return redirect()->to($returnUrl);
    }

    /**
     * Callback function
     * 
     * Handles the callback from the Okta login process and redirects accordingly
     * 
     * @return CodeIgniter\HTTP\RedirectResponse::to redirect to the home page
     */
    public function callback() {
        $result = []; // Variable to store the result of the authorisation

        // If the callback URL contains an error, then redirect to an error page
        if ($this->request->getUri()->getQuery(['only' => ['error']])) {
            return view('errors/auth', [
                'state' => urldecode($this->request->getUri()->getQuery(['only' => ['state']])),
                'error' => urldecode($this->request->getUri()->getQuery(['only' => ['error']])),
                'error_description' => urldecode($this->request->getUri()->getQuery(['only' => ['error_description']]))
            ]); 
        }

        // If the callback URL contains an Okta code, then proceed with authorisation
        if ($this->request->getUri()->getQuery(['only' => ['code']])) {
            $result = $this->okta->authorizeUser(session()->get('state'));
        } else {
            // If the callback URL doesn't contain an Okta code, then redirect to an error page
            return view('errors/auth', [
                'state' => urldecode($this->request->getUri()->getQuery(['only' => ['state']])),
                'error' => 'No code provided',
                'error_description' => 'No code was provided in the callback URL'
            ]);
        }

        // If successful login then set session values
        session()->set('username', $result['username']);
        session()->set('uid', $result['sub']);
        session()->setFlashdata('success', 'You have successfully logged in!');

        // Create user in database if they don't already exist
        if (!$this->db->doesUserExist($result['sub'])) {
            try {
                $this->db->createUser($result['sub'], $result['username']);
            } catch (Exception $e) {
                // If the user creation fails, then log the error and redirect to an error page
                return view('errors/auth', [
                    'state' => urldecode($this->request->getUri()->getQuery(['only' => ['state']])),
                    'error' => 'User creation failed',
                    'error_description' => 'The user could not be created in the database. Please try again later.'
                ]);
            }
        }

        // Retrieve the return URL from the session data
        $returnUrl = session()->get('returnUrl') ?? session()->get('_ci_previous_url');

        // Remove the session data
        session()->remove('returnUrl');
        session()->remove('_ci_previous_url');

        // If the return URL is not set, redirect to the home page
        if (!$returnUrl) {
            return redirect()->to('/');
        }

        // If the return URL is the login page, redirect to the home page
        if ($returnUrl == '/login') {
            return redirect()->to('/');
        }

        // Redirect to the return URL
        return redirect()->to($returnUrl);
    }

    /**
     * Logout function
     * 
     * Logs the user out of the application by removing stored session values
     * 
     * @return CodeIgniter\HTTP\RedirectResponse::to redirect to homepage
     */
    public function logout() {
        // If set, get the _ci_previous_url session value
        if (session()->get('_ci_previous_url')) {
            $returnUrl = session()->get('_ci_previous_url');
            session()->remove('_ci_previous_url');
        }

        // Destroy the session on logout
        session()->destroy();

        // If the _ci_previous_url session value is set, then redirect to that URL
        if (isset($returnUrl)) {
            return redirect()->to($returnUrl);
        }

        return redirect()->to('/');
    }

    /**
     * Profile function
     * 
     * Displays the user's profile page
     * 
     * @return mixed CodeIgniter\HTTP\RedirectResponse::to redirect to login page or view::load profile page 
     */
    public function profile() {
        // If the user is logged in, then display the profile page
        if (session()->get('username')) {
            echo view('fragments/html_head', [
                'title' => 'Sets',
                'styles' => [
                    '/assets/css/main.css'
                ]
            ]);
            echo view('fragments/header');
            echo view('profile', [
                'username' => session()->get('username'),
                'uid' => session()->get('uid'),
                'collections' => $this->db->getUserCollectionsWithCards(session()->get('uid'))
            ]);
            return view('fragments/footer');
        }

        // If the user is not logged in, then redirect to the login page
        session()->set('returnUrl', '/profile');
        session()->setFlashdata('error', 'You must be logged in to view your profile.');

        return redirect()->to('/login');
    }
}