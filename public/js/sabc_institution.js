
            /*
            * Alphabet Filter
            */
            if ( typeof $.fn.dataTable == "function" &&
                typeof $.fn.dataTableExt.fnVersionCheck == "function" &&
                $.fn.dataTableExt.fnVersionCheck('1.9.4') ){
                $.fn.dataTableExt.aoFeatures.push( {
                    "fnInit": function( oDTSettings ) {

                        var alphaFilter = new AlphabetFilter( oDTSettings );
                        return alphaFilter.getContainer();
                    },

                    "cFeature": "A",
                    "sFeature": "AlphaFilter"

                } );
            }
            else
            {
                alert( "Warning: PagingControl requires DataTables 1.9.4 or greater - www.datatables.net/download");
            }

            var AlphabetFilter = function ( oDTSettings ){
                var me = this;
                me.$FilterContainer = $( '<div class="alphabet-filter-widgets btn-group clearfix">'+
                    '<a class="btn btn-light alphabet-filter mobile-one columns" id="filter_all"><span>View All</span></a>');//open container

                //loop through alphabet and build filter buttons
                var first = "a", last = "z";
                for(var i = first.charCodeAt(0); i <= last.charCodeAt(0); i++) {
                    me.$FilterContainer.append('<a class="btn btn-light alphabet-filter mobile-one columns" id="filter_'+ String.fromCharCode(i) +'"><span>' + String.fromCharCode(i) + '</span></a>');
                }
                me.$FilterContainer.append('</div>');

                oDTSettings.aoDrawCallback.push( {
                    "fn": function () {	},
                    "sName": "AlphaFilter"
                } );

                return me;

            };


            /**
             * Get the container node of the column filter widgets.
             *
             * @method
             * @return {Node} The container node.
             */
            AlphabetFilter.prototype.getContainer = function() {
                return this.$FilterContainer.get( 0 );
            }

            /*
            * Custom Pagination
            */

            $.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
            {
                return {
                    "iStart":         oSettings._iDisplayStart,
                    "iEnd":           oSettings.fnDisplayEnd(),
                    "iLength":        oSettings._iDisplayLength,
                    "iTotal":         oSettings.fnRecordsTotal(),
                    "iFilteredTotal": oSettings.fnRecordsDisplay(),
                    "iPage":          oSettings._iDisplayLength === -1 ?
                        0 : Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
                    "iTotalPages":    oSettings._iDisplayLength === -1 ?
                        0 : Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
                };
            }

            /*Custom classes for datatable*/
            $.extend($.fn.dataTableExt.oStdClasses, {
                /* Full numbers paging buttons */
                "sPageButton": "btn btn-light paginate_button",
                "sPageButtonActive": "btn btn-light paginate_active active",
                "sPageButtonStaticDisabled": "btn btn-light paginate_button paginate_button_disabled disabled ",
                "sPageFirst": "first",
                "sPagePrevious": "previous",
                "sPageNext": "next",
                "sPageLast": "last",
                "sSortAsc": "tableSortDown",
                "sSortDesc": "tableSortUp",
                "sSortable": "tableSort",
                "sWrapper": "dataTables_wrapper clearfix"
            });

            //hide table until the results have loaded
            $('#institution tbody').hide();


            var oTable1 = $('#Grants_Grads').dataTable({
                "fnInitComplete": function() {
                    $('#Grants_Grads.dataTable tbody').show();
                    return true
                },
                "oLanguage": {
                    "sLengthMenu": "_MENU_",
                    "sEmptyTable": '<strong>Empty Results Found.</strong> ' +
                        '<strong class="no-results">.</strong></a>',
                    "sZeroRecords": '<strong>0 Results Found.</strong> ',
                    "sSearch": ' List of eligible schools.' ,
                    "sProcessing": "<h3>Please wait while the institutions load...</h3>",
                },
                "sDom": '<"datatable-header"fA>rt<"datatable-footer"<"four columns"i><"eight mobile-four columns"p>>',
                "sPaginationType": "full_numbers",
                "ajax": "/institution-data-opener",
                "aoColumns": [{ "mDataProp": "name" },],
                "columns": [{"data": "name" }],
            });

            $('.dataTables_filter input').on( 'keyup change', function () {
                $('#Grants_Grads.dataTable thead').show();
                $('#Grants_Grads.dataTable tbody').show();
                $('#Grants_Grads_info.dataTables_info').show();
                $('#Grants_Grads_paginate.dataTables_paginate').show();
            });
            $('div.dataTables_filter input').attr('placeholder', 'Search...');
            /*
            * Back to results/search
            * Resets the table and removes institution details
            */
            $("#Grants_Grads_wrapper").on('click', 'a.back-to-search', function(event) {
                event.preventDefault();
                removeGrantDetails();
            });

            /*
            * Institution Alphabet Event Handler
            * Filters the results based on letter
            */
            $('#Grants_Grads_wrapper').on('click','.alphabet-filter', function() {
                oTable1.fnFilter("", 0);//reset existing filter

                removeGrantDetails();

                if($(this).hasClass('active')) {

                    $('#Grants_Grads.dataTable thead').show();
                    $('#Grants_Grads.dataTable tbody').show();
                    $('#Grants_Grads_info.dataTables_info').show();
                    $('#Grants_Grads_paginate.dataTables_paginate').show();

                    $('a.active').removeClass('active');//remove any active classes from letters
                    oTable1.fnFilter("", 0);//reset filter if already active
                } else {
                    if($(this).is('#filter_all')){

                        $('#Grants_Grads.dataTable thead').show();
                        $('#Grants_Grads.dataTable tbody').show();
                        $('#Grants_Grads_info.dataTables_info').show();
                        $('#Grants_Grads_paginate.dataTables_paginate').show();

                        //reset all reset if view all is clicked
                        oTable1.fnFilter("", 0);
                        $('a.active').removeClass('active');
                    } else {
                        //filter results based on letter clicked
                        $('a.active').removeClass('active');
                        $(this).addClass('active');
                        var alpha_character = $(this).find("span").html();
                        oTable1.fnFilter("^"+ alpha_character, 0, 1 );//datable regular expression

                        $('#Grants_Grads.dataTable thead').show();
                        $('#Grants_Grads.dataTable tbody').show();
                        $('#Grants_Grads_info.dataTables_info').show();
                        $('#Grants_Grads_paginate.dataTables_paginate').show();


                    }
                }
            });

            $('.dataTables_filter input').after('<i class="icon-uniF470 clear-input"></i>');
            $('.dataTables_filter i.clear-input').on('click', function() {
                $('.dataTables_filter input').val(""); //reset search filter
                removeGrantDetails();// reset institution details
                oTable1.fnFilter("");//reset alphabet filter
                $(this).parent().removeClass('active');//remove active class from buttons
            });



            $('#Grants_Grads tbody').on("click", 'tr', function(){

                $('#Grants_Grads.dataTable').toggle();
                $('.datatable-footer').toggle();

                //show loading gif
                $('#institution.dataTable').before('<div class="ajax-loading"></div>');

                var tablez = $('#Grants_Grads').DataTable();
                var rowz = tablez.row( 0 ).data();
                var $schoolName = tablez.row(this).data();
                var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                var bodyFormData = new FormData();
                bodyFormData.append('_token', 'csrf');
                axios({
                    url: "/dashboard/school-details/"+$schoolName.name,
                    data: bodyFormData,
                    type: 'POST',
                        headers: {'Accept': 'application/json'}
                    })

                        .then(function (response) {
                            $('.ajax-loading').remove();
                            $('#Grants_Grads.dataTable').before('<a href="" class="back-to-search">Back to results</a>');
                            $('#Grants_Grads.dataTable').before(response);
                            // $('#Grants_Grads_wrapper').scrollView();

                            $('#grants_detailed_view').dataTable({
                                // "scrollY":"400px",
                                "paging":false,
                                "order": [[ 0, "desc" ]],
                            });
                            //
                            // vm.loading = false;
                            // vm.profile = response.data.profile;

                        })
                        .catch(function (error) {
                            alert("Institution Lookup failed, please try again later");
                            removeGrantDetails();
                        });
                // $.ajax({
                //     url: "/school-details/"+$schoolName.name,
                //     type: 'POST',
                //     success: function(data) {
                //         $('.ajax-loading').remove();
                //         $('#Grants_Grads.dataTable').before('<a href="" class="back-to-search">Back to results</a>');
                //         $('#Grants_Grads.dataTable').before(data);
                //         $('#Grants_Grads_wrapper').scrollView();
                //
                //         $('#grants_detailed_view').dataTable({
                //             // "scrollY":"400px",
                //             "paging":false,
                //             "order": [[ 0, "desc" ]],
                //         });
                //     },
                //     error: function( req, status, err ) {
                //         alert("Institution Lookup failed, please try again later");
                //         removeGrantDetails();
                //     }
                // });
            });

            $('#grants_detailed_view').dataTable();


            var oTable = $('#institution').dataTable({
                "fnInitComplete": function() {
                    $('#institution.dataTable tbody').show();
                    return true;
                },
                "sDom": '<"datatable-header"fA>rt<"datatable-footer"<"four columns"i><"eight mobile-four columns"p>>',
                "sPaginationType": "full_numbers",
                "oLanguage": {
                    "sLengthMenu": "_MENU_",
                    "sEmptyTable": '<a class="no-results" href="/apply/designated#not-designated"><strong>No Results Found</strong>: In order to eligible for student financial assistance your school must be designated. ' +
                        '<strong class="no-results">Find out what to do if your institution is not designated.</strong></a>',
                    "sZeroRecords": '<a class="no-results" href="/apply/designated#not-designated"><strong>No Results Found</strong>: In order to eligible for student financial assistance your school must be designated. ' +
                        '<strong class="no-results">Find out what to do if your institution is not designated.</strong></a>',
                    "sSearch": 'Search for an institution <a href="#" data-toggle="popover" class="institution_search_tips">' +
                        'Search Tips <i class="icon-supportrequest"></i></a>',
                    "sProcessing": "<h3>Please wait while the institutions load...</h3>"
                },
                "processing": true,
                "ajax": "/dashboard/institution-data-load",
                "columns": [
                    { "data": "SchoolName",
                        "defaultContent": "" },
                    { "data": "City",
                        "defaultContent": "" },
                    { "data": "ProvinceDesc",
                        "defaultContent": "" },
                    { "data": "CountryDesc",
                        "defaultContent": "" },
                    {"data": "DesignationStatus",
                        "defaultContent": "",
                        "render": function ( data, type ) {
                            var style ='';
                            if (data == 'Designated'){
                                style = 'success';}
                            if (data == 'Pending'){
                                style = 'warning'; }
                            if (data == 'Denied'){
                                style = 'danger';
                            }
                            if (data == 'Under Review'){
                                style = 'info';
                            }
                            return '<span class="badge badge-'+style+'">'+data+'</span>';
                        }
                    },
                ],
                "deferRender": true
            });

            $('.dataTables_filter input').after('<i class="icon-uniF470 clear-input"></i>');


            /* Add event handlers */

            /*
             * Back to results/search
             * Resets the table and removes institution details
             */
            $("#institution_wrapper").on('click', 'a.back-to-search', function(event) {
                event.preventDefault();
                removeInstitutionDetails();//table reset
            });

            /*
         * Institution Details Ajax Webservice Call
         * Get the institution details of the institution row that was clicked on
         */
            $('#institution tbody').on("click", 'tr', function(){

                //get institution code from tr id
                var $institution_code = $(this).attr('id');
                if($institution_code) {
                    // hide table and table footer
                    $('#institution.dataTable').toggle();
                    $('.datatable-footer').toggle();

                    //show loading gif
                    $('#institution.dataTable').before('<div class="ajax-loading"></div>');

                    //add in check for null ids
                    axios({
                        url: "/dashboard/institution-details/" + $institution_code,
                        type: 'GET',
                        headers: {'Accept': 'application/json'}
                    })

                        .then(function (response) {
                            // console.log(response);
                            // console.log(response.data);
                            // console.log(response.data.output);

                            $('.ajax-loading').remove();
                            //display institution details
                            $('#institution.dataTable').before('<a href="" class="back-to-search">Back to results</a>');
                            $('#institution.dataTable').before(response.data.output);
                            // $('#institution_wrapper').scrollView();

                        })
                        .catch(function (error) {
                            console.log(error);

                            alert("Institution Lookup failed, please try again later");
                            removeInstitutionDetails();
                        });
                    // $.ajax({
                    //     url: "/institution-details/" + $institution_code,
                    //     dataType: "JSON",
                    //     type: 'GET',
                    //     success: function(data) {
                    //         $('.ajax-loading').remove();
                    //         //display institution details
                    //         $('#institution.dataTable').before('<a href="" class="back-to-search">Back to results</a>');
                    //         $('#institution.dataTable').before(data);
                    //         $('#institution_wrapper').scrollView();
                    //
                    //     },
                    //     error: function( req, status, err ) {
                    //         //console.log( 'Institution Lookup failed', status, err );
                    //         alert("Institution Lookup failed, please try again later");
                    //         removeInstitutionDetails();
                    //     }
                    // });
                } else {
                }

                return false;

            });

            /*
             * Institution Alphabet Event Handler
             * Filters the results based on letter
             */
            $('#institution_wrapper').on('click','.alphabet-filter', function() {

                oTable.fnFilter("", 0);//reset existing filter

                removeInstitutionDetails();

                if($(this).hasClass('active')) {
                    $('a.active').removeClass('active');//remove any active classes from letters
                    oTable.fnFilter("", 0);//reset filter if already active
                } else {
                    if($(this).is('#filter_all')){
                        //reset all reset if view all is clicked
                        oTable.fnFilter("", 0);
                        $('a.active').removeClass('active');
                    } else {
                        //filter results based on letter clicked
                        $('a.active').removeClass('active');
                        $(this).addClass('active');
                        var alpha_character = $(this).find("span").html();
                        oTable.fnFilter("^"+ alpha_character, 0, 1 );//datable regular expression
                    }
                }
            } );

            $('#school-year input').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });

            //When pagination buttons are clicked scroll to top of the table
            $('.datatable-footer').on('click', '.btn.paginate_button', function(event) {
                event.preventDefault();
                // $('#institution_wrapper').scrollView();
            });


            /*
             * Institution Search Input Filter Handler
             * Resets the alphabet filters on typing and adds clear button to input if not empty
             */

            // Isn't it awesome when Microsoft makes their own version of Javascript?
            // http://msdn.microsoft.com/en-ca/library/ie/ms536956(v=vs.85).aspx
            $('.dataTables_filter input').on('input propertychange', function() {
                oTable.fnFilter("", 0);
                removeInstitutionDetails();
                $('.alphabet-filter-widgets .active').removeClass('active');
                if($(this).val() == ""){
                    $(this).parent().removeClass('active');
                } else {
                    if(!($(this).parent().hasClass('active'))){
                        $(this).parent().addClass('active');
                    }
                }
            });

            var searchString = ' ', oldSearchString = '';
            $('#institution_filter input').on('blur', function() {
                searchString = $(this).val();
                if (searchString.length > 2 && searchString != oldSearchString) {
                    //console.log('the search string is: ' + searchString);
                    ga('send', 'event', 'Instatution_Lookup_Search', 'Institution_Lookup_Combined', searchString);
                    oldSearchString = searchString;
                }
            });

            /*
             * Institution Search ClearEvent Handler
             * Reset the the input filters/results
             */

            $('.dataTables_filter i.clear-input').on('click', function() {
                $('.dataTables_filter input').val(""); //reset search filter
                removeInstitutionDetails();// reset institution details
                oTable.fnFilter("");//reset alphabet filter
                $(this).parent().removeClass('active');//remove active class from buttons
            });


            //Remove the institution details and reset the table
            function removeInstitutionDetails(){
                if($('.institution-details').is(':visible') || $('.ajax-loading').is(':visible')) {
                    $('.ajax-loading').hide();
                    $('.institution-details').remove();
                    $('a.back-to-search').remove();//remove back to results link
                    $('#institution.dataTable').toggle();
                    $('.datatable-footer').toggle();
                }
            }

            function removeGrantDetails() {
                if($('.grants-details').is(':visible') || $('.ajax-loading').is(':visible')) {
                    $('.ajax-loading').hide();
                    $('.grants-details').remove();
                    $('a.back-to-search').remove();//remove back to results link
                    $('#Grants_Grads.dataTable').toggle();
                    $('.datatable-footer').toggle();
                }

            }


