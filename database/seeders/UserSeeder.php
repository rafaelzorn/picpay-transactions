<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Repositories\User\Contracts\UserRepositoryInterface;

class UserSeeder extends Seeder
{
    /**
     * @var $userRepository
     */
    private $userRepository;

    /**
     * @param UserRepositoryInterface $userRepository
     *
     * @return void
     */
    public function __construct(UserRepositoryInterface $userRepository)
	{
        $this->userRepository = $userRepository;
	}

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $user = $this->userRepository->find(1);

        if (is_null($user)) {
            $this->userRepository->create([
                'id'        => 1,
                'full_name' => 'Rafael Zorn',
                'document'  => '48267677062',
                'email'     => 'rafael@gmail.com.br',
                'password'  => Hash::make(123456),
                'type'      => User::TYPE_USER,
            ]);
        }

        $user = $this->userRepository->find(2);

        if (is_null($user)) {
            $this->userRepository->create([
                'id'        => 2,
                'full_name' => 'Bruna Caroline Cardozo',
                'document'  => '81347820000',
                'email'     => 'bruna@gmail.com.br',
                'password'  => Hash::make(654321),
                'type'      => User::TYPE_USER,
            ]);
        }

        $user = $this->userRepository->find(3);

        if (is_null($user)) {
            $this->userRepository->create([
                'id'        => 3,
                'full_name' => 'Lojista S/A',
                'document'  => '70915585000155',
                'email'     => 'lojista-contato@gmail.com.br',
                'password'  => Hash::make(1234567),
                'type'      => User::TYPE_SHOPKEEPER,
            ]);
        }
    }
}
