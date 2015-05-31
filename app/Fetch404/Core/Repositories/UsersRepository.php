<?php namespace Fetch404\Core\Repositories;

use Fetch404\Core\Models\User;

class UsersRepository extends BaseRepository {

    public function __construct(User $user)
    {
        $this->model = $user;
        $this->itemsPerPage = 10;
    }

}