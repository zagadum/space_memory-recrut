
//import  BaseListing  from 'craftable';
import BaseListing  from '../../BaseListing.js';
import Vue from "vue";

Vue.component('delete-modal', {
    mixins: [BaseListing],
    name: 'deleteModal',
    props: [ 'url', 'url2'],

    methods: {
        click_to_close() {
            this.$modal.hide(this.$options.name);
        },
        deleteBtnOK() {
            this.delete_btn_ok();
            this.deleteItemMeReload();

        }
    }
});

Vue.component('delete-modal', {
    mixins: [BaseListing],
    name: 'deleteModal',
    props: ['url'],
    methods: {
        click_to_close() {
            this.$modal.hide(this.$options.name);
        },
        deleteBtnOK() {
            this.deleteItemMeReload();

        }
    }
});
export default {
	mixins: [BaseListing]
};



