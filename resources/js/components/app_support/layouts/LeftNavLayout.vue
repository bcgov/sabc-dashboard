<template>
<!-- Left Sidenav -->

        <!-- end left-sidenav-->

</template>
<style scoped>
    ul{
        height: 97%;
    }
    div p{
        text-align: center;border-radius: 0;
    }
</style>
<script>
    export default {
        methods:{
            toggleMenu: function () {
                $(".left-sidenav li.expandable ul").toggleClass("mm-show");
            }
        },
        mounted() {

        }
    }
</script>
