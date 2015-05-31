<?php namespace App\Http\Controllers;

use Carbon\Carbon;
use Fetch404\Core\Models\User;
use Fetch404\Core\Repositories\UsersRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Input;

class UsersController extends Controller {

    private $users;

    /**
     * Show a list of members.
     *
     * @return mixed
     */
    public function showMembers()
    {
        $users = $this->users->getModel()->get();
        $now = Carbon::now();
        $now->subMonth(1);

        $users = $users->sortBy(function($item) {
            return mb_strtolower($item->name);
        });

//        $users = $users->filter(function(User $item) use ($now) {
//            return $item->last_active != null && $item->last_active > $now->toDateTimeString();
//        });

        $page = Input::get('page') ?: 1;
        $perPage = 12;
        $pagination = new LengthAwarePaginator(
            $users->forPage($page, $perPage),
            $users->count(),
            $perPage,
            $page
        );

        if ($page > $pagination->lastPage())
        {
            $pagination = new LengthAwarePaginator(
                $users->forPage(($page = 1), $perPage),
                $users->count(),
                $perPage,
                ($page = 1)
            );
        }

        $pagination->setPath('members');

        return view('core.forum.members', compact('users', 'pagination'));
    }

    /**
     * Create a new users controller instance.
     *
     * @param UsersRepository $usersRepository
     */
    public function __construct(UsersRepository $usersRepository)
    {
        $this->users = $usersRepository;
    }

}
