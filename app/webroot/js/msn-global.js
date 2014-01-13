(function($){
/*-------------------------------------------------------------
Function: appdat
--------------------------------------------------------------*/
    $.fn.appdat = function() {
        return this.each(function(){
            var jsonText = $(this).text();
            var jsonData; 
            try {
                jsonData = JSON.parse(jsonText);
                $(this).data('appdat', jsonData);
            } catch (e) {
                return null;
            }
            return jsonData;
        });
    };
})(jQuery);