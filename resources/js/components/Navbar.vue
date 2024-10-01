<template>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <template v-if="auth === true">
            <a v-if="$route.path != '/dashboard/'" class="navbar-brand" href="/dashboard">Back to Dashboard</a>
            <a v-else class="navbar-brand" href="/dashboard"></a>
<!--            <button @click="mobileToggle()" class="navbar-toggler" type="button">-->
<!--                <span class="navbar-toggler-icon"></span>-->
<!--            </button>-->
            <span class="navbar-toggler">{{profileName}}</span>
            <div class="collapse navbar-collapse" :class="mobileDropDown" id="navbarNavDropdown">
                <ul class="navbar-nav ml-auto pt-1">
                    <li class="nav-item pl-3">
                        <a href="/dashboard/file-uploads" class="nav-link pt-0 pb-0">
                            <span class="icon-filemanager icon-2x float-left" aria-hidden="true">&nbsp;</span>
                            <span class="d-none d-lg-block file-uploads float-right pt-1">&nbsp;File Uploads</span>
                        </a>
                    </li>

                    <li class="nav-item pl-3">
                        <a href="/dashboard/notifications" class="nav-link pt-0 pb-0">
                            <span aria-hidden="true" class="icon-draft2 icon-2x float-left"></span>
                            <span v-if="newMsgs > 0" class="badge badge-danger counter float-right">{{newMsgs}}</span>
                        </a>
                    </li>

                    <li @mouseover="showDropDown" @mouseout="hideDropDown" class="nav-item dropdown pl-3">
                        <a class="nav-link dropdown-toggle pt-0 pb-0" id="navbarDropdownMenuLink1"  title="My Account" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <div class="username clearfix">
                                <span class="float-left pt-1">{{profileName}}</span>
                                <span aria-hidden="true" class="icon-cog icon-2x float-right mt-n1"></span>
                            </div>
                        </a>
                        <div @click="toggleDropDown()" @mouseover="showDropDown()" @mouseout="hideDropDown()" class="dropdown-menu dropdown-menu-right" :class="dropDownToggle" aria-labelledby="navbarDropdownMenuLink1">
                            <div class="dropdown-header">My Account</div>
                            <a class="dropdown-item p-3" href="/dashboard" tabindex="-1"><i class="icon-uniF006 text-muted mr-2"></i>My Dashboard</a>
                            <a class="dropdown-item p-3" href="/dashboard/profile" tabindex="-1"><i class="icon-webmail text-muted mr-2"></i>Update Profile</a>
                            <a class="dropdown-item p-3" href="/help-centre" target="_blank" tabindex="-1"><i class="icon-uniF014 text-muted mr-2"></i>Help Centre</a>
                            <p class="dropdown-divider"></p>
                            <a class="dropdown-item p-3" href="/dashboard/logout" tabindex="-1"><i class="icon-power text-muted mr-2"></i>Logout</a>
                        </div>
                    </li>

                </ul>
            </div>
        </template>
        <template v-else>
            <a class="navbar-brand" href="/">Back to main page</a>
            <button @click="mobileToggle()" class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" :class="mobileDropDown" id="navbarNavDropdown">
                <ul class="navbar-nav ml-auto pt-1">

                    <li @mouseover="showDropDown" @mouseout="hideDropDown" class="nav-item pl-3">
                        <a class="nav-link pt-0 pb-0 dropdown-toggle" title="My Account" href="/dashboard/login" role="button" >
                            <div class="username">Login/Register
                                <span aria-hidden="true" class="icon-cog icon-2x float-right mt-n1"></span>
                            </div>
                        </a>
                    </li>

                </ul>
            </div>
        </template>

    </nav>
</template>
<style scoped>
    span.counter{
        border-radius: 50px;
        line-height: 10px;
        padding: 6px;
        margin-right: 5px;
    }
    .icon-cog:before {
        line-height: 36px;
    }
    span.file-uploads{
        line-height: 25px;
    }
    nav{
        background: #234175;
        height: 55px;
        margin: 0;
        padding: 0 0px;
        width: 100%;
        border-bottom: 3px solid #fcb929;
    }
    ul.navbar-nav{
        background: #234175;
    }
    nav a.navbar-brand {
        background: url(/dashboard/img/logo-dt-icon.png) no-repeat scroll 0 0;
        height: 45px;
        width: 120px;
        display: block;
        white-space: normal;
        line-height: 1;
        margin: 10.66666667px 0 0 3px;
        outline: none;
        border: 0;
        color: rgba(255, 255, 255, 0.9);
        padding: 4px 0 0 55px;
        font-size: 11.9px;
    }
    nav a.nav-link{
        position: relative;
    }
    nav span.counter{
        position: absolute;
        right: 0;
    }
    .dropdown-toggle::after{
        display: none !important;
    }
    .dropdown-menu {
        z-index: 1040;
        min-width: 260px;
        padding: 6px 0;
        left: 32px;
        margin-top: 1px;
        list-style: none;
        background-color: #ffffff;
        border: 1px solid #dae2ed;
        border-top: 0;
        -webkit-border-radius: 4px;
        -moz-border-radius: 4px;
        border-radius: 4px;
        -webkit-border-top-left-radius: 0;
        -moz-border-radius-topleft: 0;
        border-top-left-radius: 0;
        -webkit-border-top-right-radius: 0;
        -moz-border-radius-topright: 0;
        border-top-right-radius: 0;
        -webkit-box-shadow: 0 3px 0 0 rgba(218, 226, 237, 0.4);
        -moz-box-shadow: 0 3px 0 0 rgba(218, 226, 237, 0.4);
        box-shadow: 0 3px 0 0 rgba(218, 226, 237, 0.4);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
    }
    .dropdown-menu > a, .dropdown-menu > a:visited {
        display: block;
        text-align: left;
        height: auto;
        padding: 8px 12px;
        clear: both;
        font-weight: normal;
        color: #004b8d;
        text-decoration: none;
        border-top: 1px solid transparent;
        border-bottom: 1px solid transparent;
        line-height: 1rem;
    }
    /*.dropdown-menu::before {*/
    /*    right: 15px;*/
    /*    left: auto;*/
    /*}*/
    /*.dropdown-menu::before {*/
    /*    position: absolute;*/
    /*    top: -10px;*/
    /*    left: 15px;*/
    /*    display: inline-block;*/
    /*    border-right: 10px solid transparent;*/
    /*    border-bottom: 10px solid #dae2ed;*/
    /*    border-left: 10px solid transparent;*/
    /*    border-bottom-color: rgba(0, 75, 141, 0.1);*/
    /*    content: '';*/
    /*}*/
    .dropdown-menu::after {
        right: 15px;
        left: auto;
    }
    .dropdown-menu::after {
        position: absolute;
        top: -9px;
        z-index: -1;
        display: inline-block;
        border-right: 10px solid transparent;
        border-bottom: 10px solid #ffffff;
        border-left: 10px solid transparent;
        content: '';
    }
    nav .dropdown-menu .dropdown-header{
        border: 1px solid #004b8d;
        border-left: 0;
        border-right: 0;
        margin: 0;
        padding: 6px 0 6px 15px;
        text-align: left;
        color: #ffffff;
        text-shadow: 0 -1px 0 #004b8d;
        webkit-box-shadow: inset 0 1px 0 #7bbbf7;
        -moz-box-shadow: inset 0 1px 0 #7bbbf7;
        box-shadow: inset 0 1px 0 #7bbbf7;
        background: #0f93db;
    }

    @media screen and (max-width: 480px) {
        .dropdown-menu{
            position: relative;
            left: 48%;
        }
    }
</style>
<script>
    import axios from 'axios';

    export default {
        data: () => ({
            dashboard: "",
            dropDownToggle: "hide",
            mobileDropDown: null,
            profileName: "",
            newMsgs: 0,
        }),
        props: ['auth'],
        methods: {
            mobileToggle: function(){
                this.mobileDropDown = this.mobileDropDown === null || this.mobileDropDown === 'hide' ? "show" : 'hide';
            },
            toggleDropDown: function(){
                this.dropDownToggle = this.dropDownToggle === "show" ? "hide" : "show";
            },
            showDropDown: function(){
                this.dropDownToggle = "show";
            },
            hideDropDown: function(){
                this.dropDownToggle = "hide";
            },
            fetchData: function(){
                this.loading = true;
                var vm = this;
                axios({
                    url: '/dashboard/fetch-navbar',
                    //data: formData,
                    method: 'get',
                    //headers: {'Accept': 'application/json', 'Content-Type': 'multipart/form-data'}
                    headers: {'Accept': 'application/json'}
                })

                    //axios.get( '/fetch-dashboard' )
                    .then(function (response) {
                        //console.log(response.status);
                        //vm.loading = false;
                        if(response.status == 204){
                            //no content BIG ISSUE

                        }if(response.status == 200){
                            vm.newMsgs = response.data.new_messages;
                            vm.profileName = response.data.name;
                        }

                    })
                    .catch(function (error) {
                        //vm.loading = false;
                        //vm.loadingError = true;
                        //console.log(error);
                    });
            }


        },
        mounted: function () {
            if(this.auth === true)
                this.fetchData();
        },


    }

</script>
