<?php namespace App\Http\Controllers\Searching;

use App\Http\Controllers\Controller;
use App\Http\Requests\Searching\SearchRequest;
use Fetch404\Core\Models\Post;
use Fetch404\Core\Models\Report;
use Fetch404\Core\Models\Topic;
use Fetch404\Core\Models\User;
use Illuminate\Cache\CacheManager;
use Illuminate\Cache\Repository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Zizaco\Entrust\EntrustFacade as Entrust;

use Cmgmyr\Messenger\Models\Thread as Conversation;

class SearchController extends Controller
{
    /*
     * The User model instance
     * @var User
     */
    protected $user;

    /*
     * The Post model instance
     * @var Post
     */
    protected $post;

    /*
     * The Topic model instance
     * @var Post
     */
    protected $topic;

    /*
     * The Report model instance
     * @var Report
     */
    protected $report;

    /*
     * The Conversation model instance
     * @var Conversation
     */
    protected $conversation;

    /**
     * The Cache repository instance.
     * @var Repository
     */
    protected $store;

    /**
     * Create a new search controller instance.
     *
     * @param User $user
     * @param Post $post
     * @param Topic $topic
     * @param Report $report
     * @param Conversation $conversation
     * @param Repository $repository
     */
    public function __construct(User $user, Post $post, Topic $topic, Report $report, Conversation $conversation, Repository $repository)
    {
        $this->user = $user;
        $this->post = $post;
        $this->topic = $topic;
        $this->report = $report;
        $this->conversation = $conversation;
        $this->store = $repository;
    }

    /**
     * Show the search page.
     *
     * @return void
     */
//    public function showIndex()
//    {
//        return view('core.search.search');
//    }

    /**
     * Search in the database for certain items
     *
     * @param SearchRequest $request
     * @return mixed
     */
    public function search(SearchRequest $request)
    {
//        if (!$request->has('query')) return redirect()->to('/search');
//
//        $searchQuery = $request->input('query');

        $searchQuery = ($this->store->has('searchQuery') ? $this->store->get('searchQuery') : $request->input('query'));

        $resultsArray = [];

        $searchQuery = $this->store->remember('searchQuery', 60, function() use ($searchQuery) {
            return $searchQuery;
        });

        if (($request->input('query') != null) && $searchQuery != $request->input('query'))
        {
            Cache::forget('searchQuery');

            $searchQuery = $this->store->remember('searchQuery', 60, function() use ($request) {
                return $request->input('query');
            });
        }

        if ($searchQuery == null) return view('core.search.search');

        // Search in the topics table
        $topics = $this->topic->where('title', 'like', '%' . $searchQuery . '%')->get();
        foreach($topics as $topicResult)
        {
            $resultsArray[] = $topicResult;
        }

        // Search in the users table
        $users = $this->user->where('name', 'like', '%' . $searchQuery . '%')->get();
        foreach($users as $userResult)
        {
            $resultsArray[] = $userResult;
        }

        // Search in the posts table
        $posts = $this->post->where('content', 'like', '%' . $searchQuery . '%')->get();
        foreach($posts as $postResult)
        {
            $resultsArray[] = $postResult;
        }

        // Search for reports
        $reports = $this->report->where('reason', 'like', '%' . $searchQuery . '%')->get();
        foreach($reports as $reportResult)
        {
            $resultsArray[] = $reportResult;
        }

        // Search for conversations
        $conversations = $this->conversation->where('subject', 'like', '%' . $searchQuery . '%')->get();
        foreach($conversations as $conv)
        {
            $resultsArray[] = $conv;
        }

        Cache::forget('results');
        $results = Collection::make($resultsArray);

        $results = $results->filter(function($item)
        {
            if ($item instanceof User)
            {
                return !$item->isBanned() and $item->isConfirmed();
            }

            if ($item instanceof Topic)
            {
                return $item->canView;
            }

            if ($item instanceof Post)
            {
                return $item->topic != null && $item->topic->canView;
            }

            if ($item instanceof Report)
            {
                return Entrust::can('viewReports');
            }

            if ($item instanceof Conversation)
            {
                if (!Auth::check()) return false;

                try {
                    $item->getParticipantFromUser(Auth::id());
                    return true;
                }
                catch (ModelNotFoundException $ex)
                {
                    return false;
                }
            }
        });

        $results = $results->sortBy(function($item)
        {
            if ($item instanceof Conversation)
            {
                return 'conversation_' . mb_strtolower($item->subject);
            }

            if ($item instanceof User)
            {
                return 'user_' . mb_strtolower($item->name);
            }

            if ($item instanceof Topic)
            {
                return sprintf('topic_%-12s%s', mb_strtolower($item->title), $item->posts()->count());
            }

            if ($item instanceof Post)
            {
                if (($item->updated_at == null || $item->updated_at->toDateTimeString() <= $item->created_at->toDateTimeString()))
                {
                    return sprintf('post_%s%-12s', mb_strtolower($item->content), $item->created_at);
                }

                return sprintf('post_%s%-12s', mb_strtolower($item->content), $item->updated_at);
            }

            if ($item instanceof Report)
            {
                return sprintf('report_%-12s', mb_strtolower($item->reason));
            }

        });

        $collection = $results;

        $results = $this->store->remember('results', 60, function() use ($results, $request) {
            return ['query' => $request->input('query'), 'results' => $results];
        });

        if ($this->store->has('results') && $this->store->get('results') != $results)
        {
            $results = $this->store->remember('results', 60, function() use ($results, $request) {
                return ['query' => $request->input('query'), 'results' => $results];
            });
        }

//        $results = ($this->store->has('results') ? $this->store->get('results') : $collection);

        if ($this->store->has('results'))
        {
            $arr = $this->store->get('results');

            if ($arr['query'] == $request->input('query'))
            {
                $results = $arr['results'];
            }
        }

        $page = Input::get('page') ?: 1;
        $perPage = 10;
        $pagination = new LengthAwarePaginator(
            $results->forPage($page, $perPage),
            $results->count(),
            $perPage,
            $page
        );

        if ($page > $pagination->lastPage())
        {
            $pagination = new LengthAwarePaginator(
                $results->forPage($page, $perPage),
                $results->count(),
                $perPage,
                1
            );
        }

        $pagination->setPath('search')->appends('query', $searchQuery);

        return view('core.search.search', compact('results', 'pagination', 'searchQuery'));
    }
}