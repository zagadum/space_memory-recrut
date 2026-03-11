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

Vue.component('calendar-franchisees-form', {
    mixins: [AppForm,FullCalendar],
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
                customButtonsFranchise: {
                    prev: { // this overrides the prev button
                        text: "prev",
                        click: () => {
                            console.log("eventPrev");
                            let calendarApi = this.$refs.fullCalendar.getApi();
                            calendarApi.prev();
                            this.handleMonthClick(calendarApi);
                        }
                    },
                    next: { // this overrides the next button
                        text: "next",
                        click: () => {
                            console.log("eventNext");
                            let calendarApi = this.$refs.fullCalendar.getApi();
                            calendarApi.next();
                            this.handleMonthClick(calendarApi);
                        }
                    }
                },

                //  initialEvents: INITIAL_EVENTS, // alternatively, use the `events` setting to fetch from a feed
                editable: false,
                selectable: false,
                selectMirror: true,
                 //dateClick: this.handleDateClick,
                //select: this.handleDateSelect,
                eventClick: this.handleEventClick,
               // eventsSet: this.handleEvents,
                dayMaxEvents: true,
                weekends: true,
                events: {
                    url: '/admin/calendar/franchisees/'+$("#franchisee_id").val()+'/get-event',
                    method: 'POST',
                    failure: function() {
                        alert('there was an error while fetching events!');
                    },
                    extraParams: function() {
                        return {
                            franchisee_id: $("#franchisee_id").val(),
                        };

                    }
                }
            },
        }
    },
    methods: {
        handleDateClick: function (arg) {
            //console.log(' handleDateClick');
        },

        handleMonthClick: function (calendarApi) {
            let date = calendarApi.getDate();
            let month = date.getMonth();
            let year = date.getFullYear();

            const ajaxParam = {month: month, year: year, franchisee_id: this.form.franchisee.id};
            axios.post('/admin/calendar/franchisees/get-event', ajaxParam)
                .then(response => console.log(response),

                );
        },
        handleEventClick(clickInfo) {
            this.$emit(clickInfo, this.event);
            //console.log(' handleEventClick(clickInfo)');
            if (info.clickInfo.url) {
                window.open(info.clickInfo.url);
            }
        },
        handleEvents(events) {
            this.currentEvents = events
        },

        nameFranchisee: function ({first_name, surname}) {
            return `${surname} ${first_name}`
        },
        onChangeFranchisee: function (franchiseeID) {
            window.location.href ='/admin/calendar/franchisees/'+ this.form.franchisee.id;
        },

    },
    mounted: function () {
    var localeValue = $('#locale').val();
    if (localeValue === 'pl') {
        this.calendarOptions.locale = plLocale;
    } else if (localeValue === 'en') {
        this.calendarOptions.locale = enLocale;
    }  else if (localeValue === 'uk') {
        this.calendarOptions.locale = ukLocale;
    } else {
        this.calendarOptions.locale = ukLocale;
    }
}
});

