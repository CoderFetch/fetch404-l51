<?php

use Fetch404\Core\Models\Permission;
use Fetch404\Core\Models\Role;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder {

    public function run()
    {
        $startDiscussion = new Permission();
        $startDiscussion->name = 'startDiscussion';
        $startDiscussion->display_name = 'Start discussions';
        $startDiscussion->save();

        $editOwnDiscussion = new Permission();
        $editOwnDiscussion->name = 'editOwnDiscussion';
        $editOwnDiscussion->display_name = 'Edit own discussions';
        $editOwnDiscussion->save();

        $editAllDiscussions = new Permission();
        $editAllDiscussions->name = 'editAllDiscussions';
        $editAllDiscussions->display_name = 'Edit all discussions';
        $editAllDiscussions->save();

        $deleteOwnDiscussion = new Permission();
        $deleteOwnDiscussion->name = 'deleteOwnDiscussion';
        $deleteOwnDiscussion->display_name = 'Delete own discussions';
        $deleteOwnDiscussion->save();

        $deleteAllDiscussions = new Permission();
        $deleteAllDiscussions->name = 'deleteAllDiscussions';
        $deleteAllDiscussions->display_name = 'Delete any discussion';
        $deleteAllDiscussions->save();

        $reply = new Permission();
        $reply->name = 'reply';
        $reply->display_name = 'Reply to discussions';
        $reply->save();

        $editOwnPost = new Permission();
        $editOwnPost->name = 'editOwnPost';
        $editOwnPost->display_name = 'Edit own posts';
        $editOwnPost->save();

        $deleteOwnPost = new Permission();
        $deleteOwnPost->name = 'deleteOwnPost';
        $deleteOwnPost->display_name = 'Delete own posts';
        $deleteOwnPost->save();

        $editAllPosts = new Permission();
        $editAllPosts->name = 'editAllPosts';
        $editAllPosts->display_name = 'Edit any post';
        $editAllPosts->save();

        $deleteAllPosts = new Permission();
        $deleteAllPosts->name = 'deleteAllPosts';
        $deleteAllPosts->display_name = 'Delete any post';
        $deleteAllPosts->save();

        $login = new Permission();
        $login->name = 'login';
        $login->display_name = 'Log in';
        $login->save();

        $register = new Permission();
        $register->name = 'register';
        $register->display_name = 'Register';
        $register->save();

        $accessAdminPanel = new Permission();
        $accessAdminPanel->name = 'accessAdminPanel';
        $accessAdminPanel->display_name = 'Access admin panel';
        $accessAdminPanel->save();

        $banUser = new Permission();
        $banUser->name = 'banUser';
        $banUser->display_name = 'Ban users';
        $banUser->save();

        $deleteUser = new Permission();
        $deleteUser->name = 'deleteUser';
        $deleteUser->display_name = 'Delete users';
        $deleteUser->save();

        $editUser = new Permission();
        $editUser->name = 'editUser';
        $editUser->display_name = 'Edit users';
        $editUser->save();

        $viewDiscussion = new Permission();
        $viewDiscussion->name = 'viewDiscussion';
        $viewDiscussion->display_name = 'View discussions';
        $viewDiscussion->save();

        $lockDiscussion = new Permission();
        $lockDiscussion->name = 'lockDiscussion';
        $lockDiscussion->display_name = 'Lock discussions';
        $lockDiscussion->save();

        $pinDiscussion = new Permission();
        $pinDiscussion->name = 'pinDiscussion';
        $pinDiscussion->display_name = 'Pin discussions';
        $pinDiscussion->save();

        $viewCategory = new Permission();
        $viewCategory->name = 'viewCategory';
        $viewCategory->display_name = 'View categories';
        $viewCategory->save();
        
        $viewChannel = new Permission();
        $viewChannel->name = 'viewChannel';
        $viewChannel->display_name = 'View channels';
        $viewChannel->save();
        
        $adminPerms = array(
            $startDiscussion,
            $editOwnDiscussion,
            $editAllDiscussions,
            $editAllPosts,
            $editOwnPost,
            $deleteAllDiscussions,
            $deleteAllPosts,
            $deleteOwnDiscussion,
            $deleteOwnPost,
            $reply,
            $login,
            $register,
            $accessAdminPanel,
            $banUser,
            $deleteUser,
            $editUser,
            $viewDiscussion,
            $lockDiscussion,
            $pinDiscussion,
            $viewChannel,
            $viewCategory
        );
        $moderatorPerms = array(
            $startDiscussion,
            $editOwnDiscussion,
            $editAllDiscussions,
            $deleteOwnDiscussion,
            $reply,
            $editOwnPost,
            $editAllPosts,
            $deleteOwnPost,
            $login,
            $register,
            $banUser,
            $pinDiscussion,
            $viewDiscussion,
            $lockDiscussion,
            $viewChannel,
            $viewCategory
        );
        $memberPerms = array(
            $startDiscussion,
            $reply,
            $editOwnPost,
            $editOwnDiscussion,
            $deleteOwnPost,
            $deleteOwnDiscussion,
            $login,
            $register,
            $viewDiscussion,
            $viewCategory,
            $viewChannel
        );
        $guestPerms = array(
            $login,
            $register,
            $viewDiscussion,
            $viewChannel,
            
        );
        $groups = ['Guest', 'Member', 'Moderator', 'Administrator'];
        foreach($groups as $group)
        {
            $role = Role::where('name', '=', $group)->first();
            if ($role)
            {
                switch($role->name)
                {
                    case 'Administrator':
                        $role->attachPermissions($adminPerms);
                        break;
                    case 'Guest':
                        $role->attachPermissions($guestPerms);
                        break;
                    case 'Member':
                        $role->attachPermissions($memberPerms);
                        break;
                    case 'Moderator':
                        $role->attachPermissions($moderatorPerms);
                        break;
                    default:
                        $role->attachPermissions([]);
                        break;
                }
            }
        }
    }

}