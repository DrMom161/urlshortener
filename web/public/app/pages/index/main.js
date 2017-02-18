define(['jquery'], function ($) {
    var main = {
        /**
         * Input point to file
         */
        init: function () {
            main.subscribe();
        },
        /**
         * Subscribe to page events
         */
        subscribe: function(){
            $('#jsUrlShotenerForm').submit(main.formUrlShotenerSubmit);
        },
        /**
         * Callback for form url shotener submit
         * @param event
         */
        formUrlShotenerSubmit: function (event) {
            event.preventDefault();
            var $form = $(this);
            $('#jsSuccessMessage, jsErrorMessage', $form).addClass('hidden');
            //disable submit button while ajax work
            $form.find('[type=submit]').attr('disabled', true);
            $.post('/create_short_url', $form.serialize(), function (response) {
                var message = 'Your short url: ' + response;
                $('#jsSuccessMessage', $form).html(message).removeClass('hidden');
            })
                .fail(function (error) {
                    $('#jsErrorMessage', $form).html(error.responseText).removeClass('hidden');
                })
                .always(function () {
                    $form.find('[type=submit]').attr('disabled', false);
                });
        }
    };
    return main;
});