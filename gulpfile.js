var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
     mix.styles([
         "themes/1.css",
         "main.css",
         "select2.min.css",
         "summernote.css"
     ], "public/assets/css/1-compiled.css", "public/assets/css");

     mix.styles([
        "themes/2.css",
        "main.css",
        "select2.min.css",
        "summernote.css"
     ], "public/assets/css/2-compiled.css", "public/assets/css");

    mix.styles([
       "themes/3.css",
       "main.css",
       "select2.min.css",
       "summernote.css"
    ], "public/assets/css/3-compiled.css", "public/assets/css");



    // Scripts
    mix.scripts(
        [
          'public/assets/js/jquery-1.11.2.min.js', 'public/assets/js/main.js',
          'public/assets/js/bootstrap.min.js', 'public/assets/js/main.js',
        ],
        'public/assets/js/compiled.js'
    );
});




