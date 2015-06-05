var tooltipSettings = {html: true, animation: false};
var summernoteSettings = {
    height: 300,
    minHeight: null,
    maxHeight: null,
    toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['style', ['picture', 'link', 'table', 'hr']],
        ['misc', ['codeview']],
    ]
};
var pusher;
var pusherEnabled = false;

$(document).ready(function()
{
    $('[data-type=tooltip]').tooltip(tooltipSettings);
    $('[data-type=summernote]').summernote(summernoteSettings || {});

    // likes.js
    $('.toggle-like').on('click', function(e) {

        var id = $(this).data('post-id');
        var liked = $(this).data('liked');

        if (liked == '1' || liked == 1) {
            // Dislike

            $.ajax({
                type: 'POST',
                url: '/forum/posts/' + id + '/dislike',
                dataType: 'JSON',
                success: function(data) {
                    if (data.error) {
                        (new Fetch404.Forum.Notifier).notify(data.error, '');
                    }

                    if (data.newLikeNames == '') {
                        $(this).closest('.panel-footer').hide();
                    } else {
                        $(this).closest('#like-names').html(data.newLikeNames);
                    }

                    $(this).attr('data-liked', 0);
                    $(this).find('i').attr('class', 'fa fa-thumbs-o-up');
                    $(this).attr('class', 'btn btn-success btn-sm toggle-like');

                    liked = 0;
                }
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
                if (data.error) {
                    (new Fetch404.Forum.Notifier).notify(data.error, '');
                }

                if (data.newLikeNames == '') {
                    $(this).closest('.panel-footer').hide();
                } else {
                    $(this).closest('#like-names').html(data.newLikeNames);
                }

                $(this).attr('data-liked', 1);
                $(this).find('i').attr('class', 'fa fa-thumbs-o-down');
                $(this).attr('class', 'btn btn-danger btn-sm toggle-like');

                liked = 1;
            }).error(function() {
                (new Fetch404.Forum.Notifier).notify('An internal error occurred.', '');
            });
        }
    });
});

var Fetch404 = {Forum: {}, RealTime: {}};
Fetch404.Forum.Notifier = function() {
    this.notify = function(t, e) {
        this.template = this.template || $("#flash-template").html();
        var n = $(".flash_message"),
            r = this;
        setTimeout(function() {
            function i() {
                n.remove(), $(r.template).find(".flash_message__body").attr("href", e || location.href).text(t).end().hide().appendTo("body").fadeIn(300);
            }
            n[0] ? n.fadeOut(300, i) : i()
        }, 300);

        setTimeout(function() {
            n.fadeOut(300).remove();
        }, 300);

        $(r.template).find('.close').on('click', function(e) {
            $(this).closest('.flash_message').fadeOut(500).remove();
        });
    }
}, Fetch404.Forum.Listeners = {
    whenThreadWasCreated: function(data) {
        (new Fetch404.Forum.Notifier).notify(data.user.name + " just created a new thread.", data.thread.route)
    },
    whenReplyWasLeftForThread: function(data) {
        (new Fetch404.Forum.Notifier).notify(data.user.name + " replied to this thread.");
    },
    whenUserMentionedYou: function(data) {
        (new Fetch404.Forum.Notifier).notify(data.user.name + " just mentioned you.");
    }
};
Fetch404.RealTime.Settings = function() {
    this.setPusherKey = function(key) {
        if (!key) {
            console.warn('No Pusher key provided. Real-time stuff will not work.');
        }

        pusher = new Pusher(key);
        pusherEnabled = true;
    };
};
