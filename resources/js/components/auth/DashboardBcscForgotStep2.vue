<template>
    <div>
        <fieldset>
            <legend><span class="fieldset-legend">Answer your challenge question</span></legend>
            <div v-if="isRescuable === false" class="mt-3">

                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-contextual alert-warning" role="alert">
                            <svg class="alert-icon icon-lg" aria-hidden="true" focusable="false"><use xlink:href="/dashboard/assets/sprite/icons.svg#warning"></use></svg>
                            <p>Sorry, we were unable to find a temporary access code for your account. Please call 1-800-561-1818 for assistance.</p>
                        </div><!-- /.alert -->
                    </div><!-- /.block -->
                </div>

            </div>

            <div v-if="isRescuable === true" class="row">
                <input type="hidden" name="step" value="2">
                <input type="hidden" name="uid" :value="data.guid">
                <div class="col-12">
                    <div class="form-row pt-3">
                        <div class="form-group col-12">
                            <label for="edit-answer">Enter the number given to you over the phone (1-100) <span class="form-required" title="This field is required.">*</span></label>
                            <input type="number" class="form-control" :class="errors.answer != undefined ? 'is-invalid' : ''" id="edit-answer" name="answer" size="60" maxlength="128" required>
                        </div>
                    </div>
                    <div class="form-row pt-3 pb-3">
                        <div class="col-md-6">
                            <a href="/dashboard" class="btn btn-link text-left">Cancel</a>
                        </div>
                        <div class="col-md-6">
                            <button type="button" @click="goNext" class="btn btn-block btn-success">Next</button>
                        </div>
                    </div>

                </div>
            </div>
        </fieldset>

    </div>
</template>
<style scoped>

span.form-required {
    color: red;
}
legend{
    display: block;
    padding: 0 15px;
    background: #ffffff;
    margin: 0;
    font-size: 21px;
    font-family: "Questrial", "Century Gothic", Arial, Helvetica, sans-serif;
    line-height: 24px;
    color: #869198;
    width: auto;
}
fieldset{
    border: solid 2px #e6e9ea;
    -webkit-border-radius: 3px;
    -moz-border-radius: 3px;
    border-radius: 3px;
    padding: 0 25px;
    margin: 24px 0;
}
</style>
<script>
//import axios from 'axios

export default {
    filters: {

    },
    props: ['errors', 'old', 'data'],
    data: () => ({
        isRescuable: '',
    }),
    methods: {
        goNext: function(){
            this.$emit('submitform');
        },
        validRescue: function (){
            this.isRescuable = false;
            if ( this.data.rescue == 'Y' ) {
                this.isRescuable = true;
            }
        }

    },

    mounted: function () {
        let vm = this;
        setTimeout(function (){
            vm.validRescue();
        }, 1000);
    },

}

</script>
