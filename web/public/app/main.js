requirejs.config({
    waitSeconds: 15,
    paths: {
        jquery: 'bower_components/jquery/dist/jquery.min'
    },
    baseUrl: '/public',
    urlArgs: 'v=0.1'
});

if(typeof (index_page) !== 'undefined') {
    requirejs(['app/pages/index/main'], function (main) {
        main.init();
    });
}
