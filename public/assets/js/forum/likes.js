/*
 * This file is part of Fetch404.
 *
 * Copyright 2015 Fetch404
 * License: MIT
 */

$(document).ready(function() {
    $('.toggle-like').on('click', function(e) {
        var id = $(this).data('post-id');
        var liked = $(this).data('liked');

        if (liked == '1' || liked == 1) {
            // Dislike

            $.ajax({
                type: 'POST',
                url: '/forum/posts/' + id + '/dislike',
                dataType: 'JSON'
            }).success(function(data) {
                if (data.newLikeNames == '') {
                    $(this).closest('.panel-footer').hide();
                } else {
                    $(this).closest('#like-names').html(data.newLikeNames);
                }

                $(this).attr('data-liked', 0);
                $(this).find('i').attr('class', 'fa fa-thumbs-o-up');
                $(this).attr('class', 'btn btn-success btn-sm toggle-like');
            }).error(function() {
                (new Fetch404.Forum.Notifier).notify('An internal error occurred.', '');
            });
        } else if (liked == '0' || liked == 0) {
            // Like

            $.ajax({
                type: 'POST',
                url: '/forum/posts/' + id + '/like',
                dataType: 'JSON'
            }).success(function(data) {
                if (data.newLikeNames == '') {
                    $(this).closest('.panel-footer').hide();
                } else {
                    $(this).closest('#like-names').html(data.newLikeNames);
                }

                $(this).attr('data-liked', 1);
                $(this).find('i').attr('class', 'fa fa-thumbs-o-down');
                $(this).attr('class', 'btn btn-danger btn-sm toggle-like');
            }).error(function() {
                (new Fetch404.Forum.Notifier).notify('An internal error occurred.', '');
            });
        }
    });
});