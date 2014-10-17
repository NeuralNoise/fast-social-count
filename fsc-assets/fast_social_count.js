jQuery( document ).ready(function( $ ) {
    socialclick(this);
    function socialclick() {
        var that = this, // needed in click handler below
            facebookclick = $(".share_facebook"),
            twitterclick = $(".share_twitter"),
            googleplusclick = $(".share_googleplus"),
            linkedinclick = $(".share_linkedin"),
            pinterestclick = $(".share_pinterest"),
            pagedescription = $('meta[property="og:description"]').attr("content"),
            sitelang = $('html').attr('lang'),
            twitterlang = sitelang,
            pagetitle = $('meta[property="og:title"]').attr("content"),
            pageurl = $(location).attr('href'),
            facebook_appid = $('meta[property="fb:app_id"]').attr("content"),
            domain = window.location.host,
            facebookpicture = "",
            facebookurl = "",
            selectedimage = $(".fast-social-count").data("selected-image");

        //special social meta propertys could be lacking
        if ("" === pagetitle || undefined === pagetitle ) {
            pagetitle = document.title;
        }
        // modify variables to suite our needs
        if ("" === pagedescription || undefined === pagedescription) {
            pagedescription = $('meta[name="description"]').attr("content");
        }
        if ("" === pagedescription || undefined === pagedescription) {
            pagedescription = "-";
        } else {
            pagedescription = encodeURIComponent(pagedescription);
        }

        pagetitle = encodeURIComponent(pagetitle);
        pageurl = encodeURIComponent(pageurl);
         if ("" === twitterlang || undefined === twitterlang) {
            twitterlang = "en";
            sitelang = "en";
        } else {
            twitterlang = twitterlang.substr(0, 2);
        }



        //check to see if we have an featured image or og:image
        if ("" === selectedimage) {
            selectedimage = $('meta[property="og:image"]').attr("content");
        }
        if ("" !== selectedimage && undefined !== selectedimage) {
            facebookpicture = "&picture=" + selectedimage;
            selectedimageexists = true;
        } else {
             facebookpicture = "";
             selectedimageexists = false;
        }


        $(facebookclick).click(function (e) {
            //if we don't have facebook_appid belonging to this site, we share without the app
            if ("" !== facebook_appid && undefined !== facebook_appid) {
                var redirect = "http://" + domain + "/wp-content/plugins/fast-social-count/assets/close-popup.html";
                facebookurl = "https://www.facebook.com/dialog/feed?app_id=" + facebook_appid + "&link=" + pageurl + "&name=" + pagetitle + "&description=" + pagedescription + "&display=popup&redirect_uri=" + redirect + facebookpicture;
            } else {
                facebookurl = "http://www.facebook.com/sharer.php?u=" + pageurl;
            }
            popupwindow(facebookurl, 'Facebook', '580', '400');
            return false;
        });


        $(twitterclick).click(function (e) {
           var twitterurl = "https://twitter.com/intent/tweet?lang=" + twitterlang + "&text=" + pagetitle + "&url=" + pageurl;
            popupwindow(twitterurl, 'Twitter', '550', '260');
            return false;
        });

        $(googleplusclick).click(function (e) {
            var googleplusurl = "https://plus.google.com/share?url=" + pageurl + "&hl=" + sitelang;
            popupwindow(googleplusurl, 'GooglePlus', '600', '600');
            return false;
        });

        $(linkedinclick).click(function (e) {
            var linkedinurl = "http://www.linkedin.com/shareArticle?mini=true&url=" + pageurl +"&title=" + pagetitle;
            popupwindow(linkedinurl, 'LinkedIn', '600', '600');
            return false;
        });

        //in case we don't have an image for Pinterest, it does not work. Then hide button
        if (selectedimageexists) {
            var pinteresturl = "http://pinterest.com/pin/create/button/?url=" + pageurl + "&media=" + selectedimage + "&description=" + pagedescription;
            $(pinterestclick).on('click', function (e) {
                popupwindow(pinteresturl, 'Pinterest', '600', '600');
            });
        } else {
              $(pinterestclick).css("display", "none");
        }
        function popupwindow(url, title, w, h) {
          var left = (screen.width/2)-(w/2),
                top = (screen.height/2)-(h/2);
          return window.open(url, title, 'scrollbars=yes, resizable=yes, width='+w+', height='+h+', top='+top+', left='+left+'');
        }
    }

});
