<template>
    <div class="">
        <input @focus="showBox" @blur="hideBox" @input="checkPassword" v-model="password" type="password" class="form-control" :class="validation != undefined && validation.password != undefined ? 'is-invalid' : ''" :id="eleId" :name="eleName" size="60" maxlength="128" :required="req" autocomplete="off">

        <div v-show="show_box == true" class="input_container">
            <ul>
                <li :class="{ is_valid: contains_eight_characters }">Password must be between 8 and 20 Characters</li>
                <li :class="{ is_valid: contains_number }">Contains Number (0-9)</li>
                <li :class="{ is_valid: contains_uppercase }">Contains English upper case characters (A-Z)</li>
                <li :class="{ is_valid: contains_lowercase }">Contains English lower case characters (a-z)</li>
                <li :class="{ is_valid: contains_special_character }">Contains special characters/symbols (~!@#$%^&amp;*_-+=`|(){}[]:;&quot;\'&lt;&gt;,.?/)</li>
            </ul>

            <div class="checkmark_container" :class="{ show_checkmark: valid_password }">
                <svg width="50%" height="50%" viewBox="0 0 140 100">
                    <path class="checkmark" :class="{ checked: valid_password }" d="M10,50 l25,40 l95,-70" />
                </svg>
            </div>
        </div>
    </div>
</template>
<style scoped>

    ul {
        padding-left: 20px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    li {
        margin-bottom: 8px;
        color: #525f7f;
        position: relative;
    }

    li:before {
        content: "";
        width: 0%; height: 2px;
        background: #2ecc71;
        position: absolute;
        left: 0; top: 50%;
        display: block;
        transition: all .6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .input_container {
        position: absolute;
        padding: 30px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);
        border-radius: 6px;
        background: #FFF;
        z-index: 999;
        width: 98%;
        display: block;
        top: 77px;
    }

    input[type="password"] {
        line-height: 1.5;
        display: block;
        color: rgba(136, 152, 170, 1);
        font-weight: 300;
        width: 100%;
        height: calc(2.75rem + 2px);
        padding: .625rem .75rem;
        border-radius: .25rem;
        background-color: #fff;
        transition: border-color .4s ease;
        border: 1px solid #cad1d7;
        outline: 0;
    }

    input[type="password"]:focus {
        border-color: rgba(50, 151, 211, .45);
    }


    /* Checkmark & Strikethrough --------- */

    .is_valid { color: rgba(136, 152, 170, 0.8); }
    .is_valid:before { width: 100%; }

    .checkmark_container {
        border-radius: 50%;
        position: absolute;
        top: -15px; right: -15px;
        background: #2ecc71;
        width: 50px; height: 50px;
        visibility: hidden;
        opacity: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        transition: opacity .4s ease;
    }

    .show_checkmark {
        visibility: visible;
        opacity: 1;
    }

    .checkmark {
        width: 100%;
        height: 100%;
        fill: none;
        stroke: white;
        stroke-width: 15;
        stroke-linecap: round;
        stroke-dasharray: 180;
        stroke-dashoffset: 180;
    }

    .checked { animation: draw 0.5s ease forwards; }

    @keyframes draw {
        to { stroke-dashoffset: 0; }
    }
</style>
<script>
    import axios from 'axios';

    export default {
        data: () => ({
            password: null,
            password_length: 0,
            contains_eight_characters: false,
            contains_number: false,
            contains_uppercase: false,
            contains_lowercase: false,
            contains_special_character: false,
            valid_password: false,
            show_box: false,
        }),
        props: ['validation', 'eleId', 'eleName', 'req'],
        methods: {
            showBox(){
                this.show_box = true;
            },
            hideBox(){
                this.show_box = false;
            },

            checkPassword() {
                this.password_length = this.password.length;
                const format = /[ !@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;

                if (this.password_length > 7 && this.password_length < 21) {
                    this.contains_eight_characters = true;
                } else {
                    this.contains_eight_characters = false;
                }

                this.contains_number = /\d/.test(this.password);
                this.contains_uppercase = /[A-Z]/.test(this.password);
                this.contains_lowercase = /[a-z]/.test(this.password);
                this.contains_special_character = format.test(this.password);

                if (this.contains_eight_characters === true &&
                    this.contains_special_character === true &&
                    this.contains_uppercase === true &&
                    this.contains_lowercase === true &&
                    this.contains_number === true) {
                    this.valid_password = true;
                } else {
                    this.valid_password = false;
                }
            }
        },
        created() {

        },
        mounted: function () {
            // document.title = "StudentAidBC - Dashboard";
        },
        computed: {

            },
        watch: {

        }

    }

</script>
