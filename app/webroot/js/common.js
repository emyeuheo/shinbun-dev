//=======================googletag=======================================
var googletag = googletag || {};
googletag.cmd = googletag.cmd || [];
(function() {
    var gads = document.createElement('script');
    gads.async = true;
    gads.type = 'text/javascript';
    var useSSL = 'https:' == document.location.protocol;
    gads.src = (useSSL ? 'https:' : 'http:') +
            '//www.googletagservices.com/tag/js/gpt.js';
    var node = document.getElementsByTagName('script')[0];
    node.parentNode.insertBefore(gads, node);
})();

//====================overlay==================

hwindow = window.innerHeight;
$(window).scroll(function()
{
     $(".overlay_js").css({top: "initial", bottom: "0"});
    scrollPosition = $(window).scrollTop();
    scrollBottom = $(window).scrollTop() + hwindow;
    if(scrollPosition == 0){
        $(".overlay_js").css({top: "initial", bottom: "0"});
    }
    
    if($(window).scrollTop() + window.innerHeight == $(document).height()){
        $(".overlay_js").css({top: "0", bottom: "initial"});
    }

});



//=============== insert Ads ==========
