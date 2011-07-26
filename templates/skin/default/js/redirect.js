window.addEvent('domready', function() {
  $$('.baneroid').addEvent('click', function(){
    var sId = this.get('title');

        JsHttpRequest.query(
            aRouter['banneroid']+'ajaxclick/',
            {
                sId:sId,
                security_ls_key: LIVESTREET_SECURITY_KEY
            },
            function(result, errors) {

            },
            true
            );
  });
});
