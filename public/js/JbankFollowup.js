window.document.domain = 'jbank.dk';
    
var JbankFollowup = {

hideInParent: function(){
    window.parent.hideLegacyIframe();
    return true;
},    
drawInParent: function(){
    window.parent.showLegacyIframe(1);
    var docHeight = $(document).height();
    window.parent.showLegacyIframe(docHeight);
    return true;
},
behandel: function (){
    try{
        window.parent.do_behandle();
    }catch(e){
        // Ignore ..
    }
    return true;
}

}
