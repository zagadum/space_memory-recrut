import flatPickr from 'vue-flatpickr-component';

export default {
    name: 'pickerComponent',
    data () {
        return {
            msg: 'Welcome to Your Vue.js App',
            selectedDate: null,
            pickerConfig: {
                wrap: false, // set wrap to true only when using 'input-group'
                altFormat: 'j. M. Y.',
                altInput: true,
                dateFormat: 'd-m-Y',

            }
        }
    },

    components: {
        flatPickr
    }
}
