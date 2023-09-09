<?php

namespace App\Controllers\api;

use App\Controllers\api\ApiBaseController;
use App\Models\UserModel;
use Firebase\JWT\JWT;

class AuthController extends ApiBaseController
{
    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login()
    {
        //let's do the validation here
        $rules = [
            'username' => 'required|max_length[50]',
            'password' => 'required|max_length[255]|validateUser[username,password]',
        ];

        $error = [
            'password' => [
                'validateUser' => 'Username atau Password tidak sesuai.'
            ]
        ];

        if (!$this->validate($rules, $error)) {
            $message = $this->validation->getErrors();
            $output = [
                'status' => false,
                'message' => reset($message),
            ];
            return $this->respond($output, 400);
        } else {
            $username = $this->request->getVar('username', FILTER_SANITIZE_EMAIL);
            $user = $this->userModel->where('username', $username)->first();
            if ($user != null) {
                $user = $user->getClearedData();
                $token = $this->generateToken($user);
                $data['user'] = $user;
                $data['token'] = $token['token'];
                // $data['expireAt'] = date('Y-m-d H:i:s', $token['expireAt']);
                $output = [
                    'status' => true,
                    'message' => 'Berhasil login',
                    'data' => $data
                ];
                return $this->respond($output, 200);
            } else {
                $output = [
                    'status' => false,
                    'message' => "User tidak ditemukan",
                ];
                return $this->respond($output, 404);
            }
        }
    }

    private function generateToken($user)
    {
        $secret_key = JWT_SECRET_KEY;
        $issuer_claim = base_url(); // this can be the servername. Example: https://domain.com
        $audience_claim = "ALFAGO_EGALABLE";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim; //not before in seconds
        // $expire_claim = $issuedat_claim + (2 * 30 * 24 * 60 * 60); // expire time in seconds
        $token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            // "exp" => $expire_claim,
            "data" => array(
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "role" => $user->role_id,
            )
        );

        $token = JWT::encode($token, $secret_key, 'HS256');

        return array(
            'token' => $token,
            // 'expireAt' => $expire_claim
        );
    }
}
