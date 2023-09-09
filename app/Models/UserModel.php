<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Services;
use Ramsey\Uuid\Uuid;

class UserModel extends Model
{
	protected $table = 'users';
	protected $returnType = 'App\Entities\User';

	protected $useSoftDeletes = true;

	protected $allowedFields = [
		'name', 'username', 'password'
	];

	protected $useTimestamps = true;
	protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';

	protected $beforeInsert = ['beforeInsert'];
	protected $beforeUpdate = ['beforeUpdate'];

	protected function beforeInsert(array $data)
	{
		$uuid4 = Uuid::uuid4();
		$data = $this->passwordHash($data);
		$data['data']['id'] = $uuid4->toString();
		return $data;
	}

	protected function beforeUpdate(array $data)
	{
		$data = $this->passwordHash($data);
		return $data;
	}

	protected function passwordHash(array $data)
	{
		if (isset($data['data']['password']))
			$data['data']['password'] = password_hash($data['data']['password'], PASSWORD_BCRYPT);

		return $data;
	}
}
