import './bootstrap';

import 'vue-multiselect/dist/vue-multiselect.min.css';

import flatPickr from 'vue-flatpickr-component';
import flatPickr_uk from 'flatpickr/dist/l10n/uk.js';
import flatPickr_pl from 'flatpickr/dist/l10n/pl.js';

//import VueQuillEditor from 'vue-quill-editor';
import Notifications from 'vue-notification';
import Multiselect from 'vue-multiselect';

import VeeValidate, { Validator } from 'vee-validate';
import { localize, ValidationProvider } from "vee-validate";


import VuePhoneNumberInput from 'vue-phone-number-input';
import 'vue-phone-number-input/dist/vue-phone-number-input.css';

Vue.component('vue-phone-number-input', VuePhoneNumberInput);

import validationMessages_uk from 'vee-validate/dist/locale/uk.js';
import validationMessages_ru from 'vee-validate/dist/locale/ru.js';
import validationMessages_pl from 'vee-validate/dist/locale/pl.js';
import validationMessages_en from 'vee-validate/dist/locale/en.js';

import 'flatpickr/dist/flatpickr.css';
import VueCookie from 'vue-cookie';
//import {Admin} from 'craftable';

import VModal from 'vue-js-modal'
import Vue from 'vue';
import VueI18n from "vue-i18n";
import Locale_attributes_uk from './ua.attribute.json';
import Locale_attributes_pl from './pl.attribute.json';
import Locale_attributes_en from './en.attribute.json';
import './app-components/bootstrap';
import './index';



import '@fullcalendar/core/vdom' ;
import FullCalendar from '@fullcalendar/vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
 Vue.component('fullcalendar', FullCalendar);

Vue.use(VueI18n);

const i18n = new VueI18n({
    locale: "uk", // set default locale
    messages: {
        en: { // object with messages
             },
         ru: { // another locale messages
             },
        uk: { // another locale messages
        }

    },
        });
const langAttribute = document.documentElement.getAttribute('lang');
i18n.locale=langAttribute;

Vue.component('multiselect', Multiselect);


Validator.localize('uk', validationMessages_uk);
flatpickr.localize('uk',flatPickr_uk);

Validator.localize('pl', validationMessages_pl);
flatpickr.localize('pl',flatPickr_pl);
flatpickr.l10ns.default.firstDayOfWeek = 1;
Vue.use(VeeValidate, {
    i18nRootKey: 'validations', // customize the root path for validation messages.
    i18n,
    dictionary: {
        pl: {
            messages: validationMessages_pl.messages,
            attributes: Locale_attributes_pl,
        },
        ru: validationMessages_ru,
        en: {
            messages: validationMessages_en.messages,
            attributes: Locale_attributes_en,
        },
        uk: {
            messages: validationMessages_uk.messages,
            attributes: Locale_attributes_uk,
        }
    }
});

Vue.component('datetime', flatPickr);

Vue.use(VModal, { dialog: true, dynamic: true, injectModalsContainer: true });
Vue.use(VueQuillEditor);
Vue.use(Notifications);
Vue.use(VueCookie);
Vue.component("ValidationProvider", ValidationProvider);

// Enable Vue devtools
Vue.config.devtools = true;
Vue.config.performance = true;

new Vue({
    mixins: [Admin],
});



