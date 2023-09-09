<?php

namespace App\Controllers\api;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ApiBaseController extends ResourceController
{

	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = [];

	protected $tokenData = null;

	/**
	 * Constructor.
	 */
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);

		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.:
		// $this->session = \Config\Services::session();

		$this->validation = \Config\Services::validation();
		$this->getTokenData();
	}

	private function getTokenData()
	{
		$secret_key = JWT_SECRET_KEY;

		$token = null;

		$authHeader = $this->request->getServer('HTTP_AUTHORIZATION');
		if ($authHeader == null) {
			return;
		}

		$arr = explode(" ", $authHeader);
		$token = $arr[1];

		if ($token) {
			try {
				$decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
				// Access is granted. Add code of the operation here 
				if ($decoded) {
					$this->tokenData =  $decoded->data;
				}
			} catch (\Exception $e) {
				return;
			}
		}
		return;
	}
}
