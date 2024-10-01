
            /**
             *  Make the <embed> tag have a nice height on dashboard application pages.
             */
            function detectIE() {
                var ua = window.navigator.userAgent;
                var msie = ua.indexOf('MSIE ');
                var trident = ua.indexOf('Trident/');

                if (msie > 0) {
                    // IE 10 or older => return version number
                    return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
                }

                if (trident > 0) {
                    // IE 11 (or newer) => return version number
                    var rv = ua.indexOf('rv:');
                    return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
                }

                // other browser
                return false;
            }

            function resizeDashboardApplyEmbed() {
                // calc the height needed for the <embed>
                var openAsHTMLbutton = $('.block-lc-application-widget .dashboard-apply-taskbar .inner-apply a');
                var embedHeight = $(window).innerHeight() - ($('.navbar-fixed-bottom').first().innerHeight() + parseInt(openAsHTMLbutton.first().css('margin-top')));
                embedHeight = embedHeight - 38; // why? I don't know..
                $('.block-lc-application-widget embed').first().css('height', embedHeight + 'px');
            }

            // apply the resize if greater than IE 8 or not IE and not on an HTML5 form page but rather, a PDF/LC page
            if ($('body').hasClass('page-dashboard') && (detectIE() == false || detectIE() > 7) && !$('.lc-no-conflict')[0]) {
                // fire on page load
                resizeDashboardApplyEmbed();
                $('body').css('overflow-y', 'hidden');
                // add event listener - fire <embed> resize on window resize
                $(window).smartresize(function() {
                    resizeDashboardApplyEmbed();
                });
            }
