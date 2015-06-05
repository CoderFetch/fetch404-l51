<?php

# Search
$router->group(['prefix' => 'search', 'middleware' => ['bancheck', 'update_last_activity']], function() use ($router)
{
    $router->any('/', ['as' => 'search', 'uses' => 'Searching\SearchController@search']);
});