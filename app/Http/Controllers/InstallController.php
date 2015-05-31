<?php namespace App\Http\Controllers;

use Fetch404\Core\Models\Category;
use Fetch404\Core\Models\CategoryPermission;
use Fetch404\Core\Models\Channel;
use Fetch404\Core\Models\ChannelPermission;
use Fetch404\Core\Models\Post;
use Fetch404\Core\Models\Role;
use Fetch404\Core\Models\Topic;
use Fetch404\Core\Models\User;
use Fetch404\Core\Repositories\SettingsRepository;
use Fetch404\Core\Services\Purifier;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

use App\Http\Requests\Installer\InstallRequest;

use Cmgmyr\Messenger\Models\Thread as Conversation;
use Cmgmyr\Messenger\Models\Message as ConversationMessage;

use Illuminate\Support\Facades\Schema;

class InstallController extends Controller
{
    private $user;

    private $channel;

    private $category;

    private $topic;

    private $post;

    private $conversation;

    private $conversationMessage;

    private $role;

    private $auth;

    private $settings;

    /**
     * Initializer.
     *
     * @param User $user
     * @param Channel $channel
     * @param Category $category
     * @param Topic $topic
     * @param Post $post
     * @param Conversation $conversation
     * @param ConversationMessage $conversationMessage
     * @param Role $role
     * @param Guard $auth
     * @param SettingsRepository $settingsRepository
     */
    public function __construct(
        User $user, Channel $channel, Category $category,
        Topic $topic, Post $post,
        Conversation $conversation,
        ConversationMessage $conversationMessage,
        Role $role, Guard $auth, SettingsRepository $settingsRepository
    )
    {
        $this->user = $user;
        $this->channel = $channel;
        $this->topic = $topic;
        $this->post = $post;
        $this->conversation = $conversation;
        $this->conversationMessage = $conversationMessage;
        $this->role = $role;
        $this->category = $category;
        $this->auth = $auth;
        $this->settings = $settingsRepository;
    }

    public function show()
    {
        if (Schema::hasTable('migrations'))
        {
            return redirect(route('home.show'));
        }

        return view('core.installer.index');
    }

    public function showDBError()
    {
        return view('core.installer.errors.configuredb');
    }

    public function showPDOException()
    {
        return view('core.installer.errors.pdoexception');
    }

    public function install(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|min:5|max:13|regex:/[A-Za-z0-9\-_!\.\s]/',
            'email' => 'required|email',
            'password' => 'required|min:8|max:30|confirmed|regex:/[A-Za-z0-9\-_!\$\^\@\#]/',

            'outgoing_email' => 'required|email'
        ], [
            'username.required' => 'A username is required.',
            'username.min' => 'Usernames must be at least 5 characters long.',
            'username.max' => 'Usernames can be up to 13 characters long.',
            'username.regex' => 'You are using characters that are not allowed. Allowed characters: A through Z, a through z, 0 through 9, -, _, !, and . (period)',
            'email.required' => 'An email address is required.',
            'email.email' => 'Please enter a valid email.',
            'password.required' => 'A password is required.',
            'password.min' => 'Passwords must be at least 8 characters long.',
            'password.max' => 'Passwords can be up to 30 characters long.',
            'password.confirmed' => 'Your passwords do not match. Please verify that the confirmation matches the original.',
            'password.regex' => 'You are using characters that are not allowed. Allowed characters: A through Z, a through z, 0 through 9, !, -, _, $, ^, @, #',

            'outgoing_email.required' => 'An outgoing email address is required.',
            'outgoing_email.email' => 'Please enter a valid email address.'
        ]);

        // Surround everything with try/catch, in case something weird happens
        try
        {
            // STEP 1: Commands

            // composer dump-autoload
            exec("cd " . base_path());
            exec("composer dump-autoload");

            Artisan::call('migrate', ['--quiet']);
            Artisan::call('vendor:publish', ['--quiet']);
            Artisan::call('clear-compiled', ['--quiet']);
            Artisan::call('cache:clear', ['--quiet']);

            $username = $request->input('username');
            $password = $request->input('password');

            // STEP 2: Create the admin user (confirmed by default)
            $adminUser = $this->user->create(array(
                'name' => $username,
                'email' => $request->input('email'),
                'password' => Hash::make($password),
                'slug' => str_slug($request->input('username')),
                'confirmed' => 1
            ));

            // Step 2b: Set up default roles and permissions
            Artisan::call('db:seed', ['--class' => 'RolesTableSeeder']);
            Artisan::call('db:seed', ['--class' => 'PermissionsTableSeeder']);

            // Step 2c: Add the admin role to the admin user
            $role = $this->role->where(
                'name', '=', 'Administrator'
            )->first();

            if ($role)
            {
                $adminUser->attachRole($role);
            }

            // Step 3: Create categories and channels
            $exampleCategory1 = $this->category->create(array(
               'name' =>  'Example Category #1',
               'description' => 'An example category.',
               'weight' => 1,
               'slug' => 'example-category-1'
            ));

            $exampleCategory2 = $this->category->create(array(
                'name' =>  'Example Category #2',
                'description' => 'Another example category.',
                'weight' => 2,
                'slug' => 'example-category-2'
            ));

            $exampleChannel1 = $this->channel->create(array(
               'name' => 'Example Channel #1',
               'description' => 'An example channel.',
               'weight' => 1,
               'category_id' => 1,
               'slug' => 'example-channel-1'
            ));

            $exampleChannel2 = $this->channel->create(array(
                'name' => 'Example Channel #2',
                'description' => 'Another example channel.',
                'weight' => 2,
                'category_id' => 1,
                'slug' => 'example-channel-2'
            ));

            $exampleChannel3 = $this->channel->create(array(
                'name' => 'Example Channel #3',
                'description' => 'Yet another example channel.',
                'weight' => 1,
                'category_id' => 2,
                'slug' => 'example-channel-3'
            ));

            $staffSection = $this->category->create(array(
                'name' =>  'Staff Section',
                'description' => 'Staff only section',
                'weight' => 3,
                'slug' => 'staff-section'
            ));

            $staffChannel = $this->channel->create(array(
                'name' => 'Staff Channel',
                'description' => 'A channel for staff members',
                'weight' => 1,
                'category_id' => 3,
                'slug' => 'staff-channel'
            ));

            $accessStaffSection = [1, 4];
            $createThreads = [1, 4];

            $accessStaffChannel = [1, 4];
            $createThreadsInStaffChannel = [1, 4];

            $postInStaffSection = [1, 4];
            $postInStaffChannel = [1, 4];

            foreach($accessStaffSection as $id)
            {
                CategoryPermission::create(array(
                    'category_id' => $staffSection->id,
                    'role_id' => $id,
                    'permission_id' => 20
                ));
            }

            foreach($createThreads as $id)
            {
                CategoryPermission::create(array(
                    'category_id' => $staffSection->id,
                    'role_id' => $id,
                    'permission_id' => 1
                ));
            }

            foreach($accessStaffChannel as $id)
            {
                ChannelPermission::create(array(
                    'channel_id' => $staffChannel->id,
                    'role_id' => $id,
                    'permission_id' => 21
                ));
            }

            foreach($createThreadsInStaffChannel as $id)
            {
                ChannelPermission::create(array(
                    'channel_id' => $staffChannel->id,
                    'role_id' => $id,
                    'permission_id' => 1
                ));
            }

            foreach($postInStaffSection as $id)
            {
                CategoryPermission::create(array(
                    'category_id' => $staffSection->id,
                    'role_id' => $id,
                    'permission_id' => 6
                ));
            }

            foreach($postInStaffChannel as $id)
            {
                ChannelPermission::create(array(
                    'channel_id' => $staffChannel->id,
                    'role_id' => $id,
                    'permission_id' => 6
                ));
            }

            // Step 4: Create settings
            $data = array(
                0 => array(
                    'name' => 'site.name',
                    'value' => htmlspecialchars($request->has('forumTitle') ? $request->input('forumTitle') : 'A Fetch404 Site')
                ),
                1 => array(
                    'name' => 'site.desc',
                    'value' => htmlspecialchars($request->has('forumDesc') ? $request->input('forumDesc') : 'This site uses Fetch404.')
                ),
                2 => array(
                    'name' => 'social.twitter',
                    'value' => null
                ),
                3 => array(
                    'name' => 'social.gplus',
                    'value' => null
                ),
                4 => array(
                    'name' => 'social.fb',
                    'value' => null
                ),
                5 => array(
                    'name' => 'auth.captcha',
                    'value' => 'false'
                ),
                6 => array(
                    'name' => 'auth.captcha.key',
                    'value' => null
                ),
                7 => array(
                    'name' => 'social.twitter.id',
                    'value' => null
                ),
                8 => array(
                    'name' => 'theme.bootstrap',
                    'value' => $request->has('bootswatch_theme') ? $request->get('bootswatch_theme') : 6
                ),
                9 => array(
                    'name' => 'theme.nav.style',
                    'value' => ($request->has('inverse_navbar') ? 1 : 0)
                ),
                10 => array(
                    'name' => 'users.infractions',
                    'value' => ($request->has('enable_infractions') ? 'true' : 'false')
                ),
                11 => array(
                    'name' => 'site.outgoing',
                    'value' => $request->input('outgoing_email')
                )
            );
            try
            {
                foreach($data as $setting)
                {
                    $this->settings->setSetting($setting["name"], $setting["value"]);
                }

                $adminUser->setSetting("privacy.show_activity", true);
                $adminUser->setSetting("privacy.show_online", true);
                $adminUser->setSetting("privacy.bots", true);
                $adminUser->setSetting("notifications.on_reply", true);
                $adminUser->setSetting("notifications.on_lock", true);
                $adminUser->setSetting("notifications.on_pin", true);
                $adminUser->setSetting("notifications.on_move", true);
                $adminUser->setSetting("notifications.new_follower", true);
                $adminUser->setSetting("notifications.content_liked", true);
                $adminUser->setSetting("notifications.followed_user_posted", true);
                $adminUser->setSetting("notifications.post_on_your_profile", true);
                $adminUser->setSetting("profile.posts.signature", sprintf("Hi! My name is <strong>%s</strong>, and I'm the administrator of this forum.", $adminUser->getName()));
            }
            catch(Exception $ex)
            {
                if ($ex instanceof \PDOException) // Is it PDOException? If yes, show the "pdoexception" view.
                {
                    return view('core.installer.errors.pdoexception', array(
                        'error' => $ex
                    ));
                }
                else
                {
                    return view('core.installer.errors.exception', array(
                        'error' => $ex
                    ));
                }
            }

            // Step 5: Send the administrator a "welcome" message
            // This is the final step

            $conversation = $adminUser->threads()->create(array(
                'subject' => 'Welcome to your new Fetch404 installation'
            ));

            $messageBody = 'Hey there, <strong>' . $adminUser->name . '</strong>! Thanks for using Fetch404. Here are a few tips to help you get started.';
            $messageBody .= '<h1>Managing your Forum</h1><hr>';
            $messageBody .= '<p>Managing a large forum can be hard. Luckily, Fetch404\'s admin panel allows you to easily customize almost every part of your forum, including categories, channels, and much more. Just go to the "Forum" section of your admin panel and start setting up your forum!</p><hr>';
            $messageBody .= '<h1>Customizing your Site</h1><hr>';
            $messageBody .= '<p>Bored of the same old bland look? Want some color? You can do that! Go to the "General" section of your admin panel, and from there you can change the theme, and switch the navigation bar color.</p><hr>';
            $messageBody .= '<h1>Configuring your Site</h1><hr>';
            $messageBody .= '<p>Want to prevent spambots? Want to change your site\'s name? Need to disable the login or register feature? You can do all of that from the "General" section of your admin panel.</p><br><small>* Note: You will need to have a <a href="https://www.google.com/recaptcha/intro/index.html">reCAPTCHA</a> key in order to enable the captcha.</small><hr>';
            $messageBody .= '<h1>I need help!</h1><hr>';
            $messageBody .= '<p>Don\'t worry! You can go to our <a href="http://fetch404.com">support forum</a> and receive help with various things.</p><hr><p>We hope you enjoy using Fetch404. Please note that there is a lot more than what is listed here. You may want to turn off registering for a bit until you are sure that your website is ready. Once again, enjoy!</p>';

            $message = $adminUser->messages()->create(array(
               'thread_id' => $conversation->id,
                'user_id' => $adminUser->id,
                'body' => Purifier::clean($messageBody)
            ));

            $conversation->addParticipants(array($adminUser->id));

            if ($this->auth->attempt(['name' => $username, 'password' => $password]))
            {
            }

            return view('core.installer.success');
        }
        catch (\Exception $ex)
        {
            if ($ex instanceof \PDOException) // Is it PDOException? If yes, show the "pdoexception" view.
            {
                return view('core.installer.errors.pdoexception', array(
                    'error' => $ex
                ));
            }
            else
            {
                return view('core.installer.errors.exception', array(
                    'error' => $ex
                ));
            }
        }
    }
}