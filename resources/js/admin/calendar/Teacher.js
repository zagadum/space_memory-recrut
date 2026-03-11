import Vue from "vue";
import FullCalendar from '@fullcalendar/vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import momentPlugin from '@fullcalendar/moment';
import timeGridPlugin from '@fullcalendar/timegrid';
import scrollgridPlugin from '@fullcalendar/scrollgrid';
import ukLocale from "@fullcalendar/core/locales/uk";
import plLocale from "@fullcalendar/core/locales/pl";
import enLocale from "@fullcalendar/core/locales/en-gb";
import AppForm from "../app-components/Form/AppForm";

Vue.component('caledar-teachers-view', {
    mixins: [FullCalendar],
    data: function () {
        return {
            form: {
                franchisee: '',
                franchisee_id: '',

            },
            calendarOptions: {
                schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
                plugins: [dayGridPlugin, interactionPlugin, momentPlugin, scrollgridPlugin, timeGridPlugin],
                initialView: 'dayGridMonth',
                defaultView: 'dayGridMonth',
                locale: ukLocale,
                contentHeight: 660,
                headerToolbar: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'

                },
                editable: false,
                selectable: false,
                selectMirror: false,
                eventClick: this.handleEventClick,
                dayMaxEvents: true,
                weekends: true,
                events: {
                    url: '/admin/calendar/teacher/get-event',
                    method: 'POST',
                    failure: function() {
                        alert('there was an error while fetching events!');
                    },
                    extraParams: function() {
                        return {
                            teacher_id: $("#teacher_id").val(),
                        };
                    }
                }

            },
        }
    },
    methods: {
        handleEventClick(clickInfo) {
            this.$emit(clickInfo, this.event);
            console.log(' handleEventClick(clickInfo)');
            if (info.clickInfo.url) {
                window.open(info.clickInfo.url);
            }
        },
        handleEvents(events) {
            this.currentEvents = events
        }
    },
    mounted: function () {
        var localeValue = $('#locale').val();
        if (localeValue === 'pl') {
            this.calendarOptions.locale = plLocale;
        }  else if (localeValue === 'en') {
            this.calendarOptions.locale = enLocale;
        } else if (localeValue === 'uk') {
            this.calendarOptions.locale = ukLocale;
        } else {
            this.calendarOptions.locale = ukLocale;
        }
    }
});

