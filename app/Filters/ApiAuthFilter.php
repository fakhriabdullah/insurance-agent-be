<?php

namespace App\Filters;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ApiAuthFilter implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
        $response = service('response');
        $response->setHeader('Content-type', 'application/json');
        $response->noCache();

        $token = null;
        $headers = array_map(function ($header) {
            return $header->getValueLine();
        }, $request->getHeaders());

        $authHeader = @$headers['Authorization'];

        if ($authHeader == null) {
            $output = [
                'message' => 'Unauthorization'
            ];

            $response->setStatusCode(401);
            $response->setBody(json_encode($output));
            return $response;
        }

        $arr = explode(" ", $authHeader);
        if (count($arr) > 1) {

            $token = $arr[1];

            if ($token) {
                try {
                    $decoded = JWT::decode($token, new Key(JWT_SECRET_KEY, 'HS256'));
                    // Access is granted. Add code of the operation here 
                    if (!$decoded) {
                        $output = [
                            'message' => 'Unauthorization'
                        ];

                        $response->setStatusCode(401);
                        $response->setBody(json_encode($output));
                        return $response;
                    }
                } catch (\Exception $e) {
                    $output = [
                        'message' => 'Unauthorization'
                    ];

                    $response->setStatusCode(401);
                    $response->setBody(json_encode($output));
                    return $response;
                }
            } else {
                $output = [
                    'message' => 'Unauthorization'
                ];
                $response->setStatusCode(401);
                $response->setBody(json_encode($output));
                return $response;
            }
        } else {
            $output = [
                'message' => 'Unauthorization'
            ];

            $response->setStatusCode(401);
            $response->setBody(json_encode($output));
            return $response;
        }

        // return $this->failUnauthorized("Unauthorized. Please login");
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
