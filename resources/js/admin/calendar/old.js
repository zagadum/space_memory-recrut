import Vue from "vue";
import FullCalendar from '@fullcalendar/vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import momentPlugin from '@fullcalendar/moment';
import timeGridPlugin from '@fullcalendar/timegrid';
import scrollgridPlugin from '@fullcalendar/scrollgrid';
import ukLocale from "@fullcalendar/core/locales/uk";

$(document).contextmenu({
    delegate: ".hasmenu",
    preventContextMenuForPopup: true,
    preventSelect: true,
    menu: [
        {title: "Cut", cmd: "cut", uiIcon: "ui-icon-scissors"},
        {title: "Copy", cmd: "copy", uiIcon: "ui-icon-copy"},
        {title: "Paste", cmd: "paste", uiIcon: "ui-icon-clipboard", disabled: true},
    ],
    select: function (event, ui) {
        // Logic for handing the selected option
    },
    beforeOpen: function (event, ui) {
        ui.menu.zIndex($(event.target).zIndex() + 1);
    }
});

Vue.component('caledar-franchisees-view', {
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

                views: {
                    dayGridMonth: {
                        displayEventTime: false,
                        dayCellContent(item) {
                            return {
                                html:
                                    `<div class="cell-top"  >
                                        <p>${(item.date).getDate()}</p>
                                        <button class="add_event"   onclick="alert('aaa')">
                                            <svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <g clip-path="url(#clip0_538_7274)">
                                                                <path d="M1.41406 6H12.4151" stroke="#00B0CB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                <path d="M6.91406 1.00039V11.0004" stroke="#00B0CB" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </g>
                                                                <defs>
                                                                <clipPath id="clip0_538_7274">
                                                                <rect width="13.2012" height="12" fill="white" transform="translate(0.3125)"/>
                                                                </clipPath>
                                                                </defs>
                                                             </svg>
                                        </button>
                                        <div class="menu-wrap">
                                            <ul class="menu">
                                                <li class="menu-item">
                                                    <a href="#">
                                                        <svg width="3" height="10" viewBox="0 0 3 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <circle cx="1.24219" cy="1" r="1" fill="#333333"/>
                                                            <circle cx="1.24219" cy="5" r="1" fill="#333333"/>
                                                            <circle cx="1.24219" cy="9" r="1" fill="#333333"/>
                                                        </svg>
                                                    </a>
                                                    <ul class="drop-menu">
                                                        <li class="drop-menu-item">
                                                            <a href="#">
                                                                <svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M2.41016 3.5H3.69361H13.9612" stroke="#858585" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <path d="M5.62049 3.5V2.33333C5.62049 2.02391 5.75571 1.72717 5.9964 1.50837C6.2371 1.28958 6.56355 1.16666 6.90394 1.16666H9.47084C9.81123 1.16666 10.1377 1.28958 10.3784 1.50837C10.6191 1.72717 10.7543 2.02391 10.7543 2.33333V3.5M12.6795 3.5V11.6667C12.6795 11.9761 12.5442 12.2728 12.3035 12.4916C12.0629 12.7104 11.7364 12.8333 11.396 12.8333H4.97876C4.63837 12.8333 4.31192 12.7104 4.07123 12.4916C3.83053 12.2728 3.69531 11.9761 3.69531 11.6667V3.5H12.6795Z" stroke="#858585" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>
                                                               Удалить
                                                            </a>
                                                        </li>
                                                        <li class="drop-menu-item">
                                                            <a href="#">
                                                                <svg width="16" height="14" viewBox="0 0 16 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M8.18359 11.6667H13.9591" stroke="#858585" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <path d="M11.0734 2.04163C11.3287 1.80956 11.675 1.67919 12.036 1.67919C12.2148 1.67919 12.3918 1.7112 12.557 1.77339C12.7221 1.83557 12.8722 1.92672 12.9986 2.04163C13.125 2.15653 13.2253 2.29295 13.2937 2.44308C13.3621 2.59321 13.3973 2.75413 13.3973 2.91663C13.3973 3.07913 13.3621 3.24004 13.2937 3.39017C13.2253 3.54031 13.125 3.67672 12.9986 3.79163L4.97706 11.0833L2.41016 11.6666L3.05188 9.33329L11.0734 2.04163Z" stroke="#858585" stroke-linecap="round" stroke-linejoin="round"/>
                                                                </svg>
                                                                Редактировать
                                                            </a>
                                                        </li>
                                                        <li class="drop-menu-item">
                                                            <a href="#">
                                                                <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
\t viewBox="0 0 210.107 210.107" style="enable-background:new 0 0 210.107 210.107;" xml:space="preserve">
<g>
\t<path style="fill:#858585;" d="M168.506,0H80.235C67.413,0,56.981,10.432,56.981,23.254v2.854h-15.38
\t\tc-12.822,0-23.254,10.432-23.254,23.254v137.492c0,12.822,10.432,23.254,23.254,23.254h88.271
\t\tc12.822,0,23.253-10.432,23.253-23.254V184h15.38c12.822,0,23.254-10.432,23.254-23.254V23.254C191.76,10.432,181.328,0,168.506,0z
\t\t M138.126,186.854c0,4.551-3.703,8.254-8.253,8.254H41.601c-4.551,0-8.254-3.703-8.254-8.254V49.361
\t\tc0-4.551,3.703-8.254,8.254-8.254h88.271c4.551,0,8.253,3.703,8.253,8.254V186.854z M176.76,160.746
\t\tc0,4.551-3.703,8.254-8.254,8.254h-15.38V49.361c0-12.822-10.432-23.254-23.253-23.254H71.981v-2.854
\t\tc0-4.551,3.703-8.254,8.254-8.254h88.271c4.551,0,8.254,3.703,8.254,8.254V160.746z"/>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
</svg>
                                                            Копировать
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                     </div>`
                            }
                        }
                    }
                },

                headerToolbar: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                    // left: 'prev,next today',
                    // center: 'title',
                    // right: 'dayGridMonth'//,timeGridWeek,timeGridDay
                },

                customButtons: {
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
                 dateClick: this.handleDateClick,
                //select: this.handleDateSelect,
                eventClick: this.handleEventClick,
               // eventsSet: this.handleEvents,
                dayMaxEvents: true,
                weekends: true,
                //    dayRender:this.handleDayRender,
                /* you can update a remote database when these fire:
                        eventAdd:
                        eventChange:
                        eventRemove:
                        */
                 events:'/admin/calendar/get-event',
                //  events : function(start, end, timezone, callback){
                //      console.log(start, end, timezone, callback);
                //      var self = this;
                //      axios.get("/admin/calendar/get-event").then(response => {
                //          callback(response.data.data);
                //      });
                //  },
                // eventRender: function (event, element) {
                //     var originalClass = element[0].className;
                //     console.log(originalClass);
                //     element[0].className = originalClass + ' hasmenu';
                // },

            },


        }
    },

    methods: {
        // handleDateSelect(selectInfo) {
        //     console.log(this.$refs.fullCalendar.getApi());
        //     console.log('handleDateSelect(selectInfo)');
        //     let title = prompt('Please enter a new title for your event')
        //     let calendarApi = selectInfo.view.calendar
        //     calendarApi.unselect() // clear date selection
        //     if (title) {
        //         calendarApi.addEvent({
        //             id: createEventId(),
        //             title,
        //             start: selectInfo.startStr,
        //             end: selectInfo.endStr,
        //             allDay: selectInfo.allDay
        //         })
        //     }
        // },

        handleDateClick: function (arg) {
            console.log(' handleDateClick');
            // alert('date click! ' + arg.dateStr)
        },

        handleMonthClick: function (calendarApi) {
            // let date = calendarApi.getDate();
            // let month = date.getMonth();
            // let year = date.getFullYear();
            // const ajaxParam = {month: month, year: year, franchiseeId: this.form.franchisee.id};
            // axios.post('/admin/calendar/get-event', ajaxParam)
            //     .then(response => console.log(response),
            //         // console.log(date),
            //         // console.log(month),
            //         // console.log(year)
            //     );
        },

        handleEventClick(clickInfo) {
            this.$emit(clickInfo, this.event);
            console.log(' handleEventClick(clickInfo)');
            //let vm = this;
            // setTimeout(() => {
            //     var color = "blue";
            //     vm.calendarEvents.push({
            //         id: "add",
            //         start: selectionInfo.start,
            //         end: selectionInfo.end,
            //         allDay: selectionInfo.allDay,
            //         type: "add",
            //         title: "custom",
            //         editable: true
            //     });
            // }, 200);
            // if (confirm(`Are you sure you want to delete the event '${clickInfo.event.title}'`)) {
            //     clickInfo.event.remove()
            // }
        },
        handleEvents(events) {
            this.currentEvents = events
        },

        nameFranchisee: function ({first_name, surname}) {
            return `${surname} ${first_name}`
        },
        onChangeFranchisee: function (franchiseeID) {
            let calendarApi = this.$refs.fullCalendar.getApi();
            this.handleMonthClick(calendarApi);
            //console.log(this.form.franchisee.id);
        },

    }


});

Vue.component('caledar-groups-view', {
    mixins: [FullCalendar],
    data: function () {
        return {
            calendarOptions: {
                schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
                plugins: [dayGridPlugin, interactionPlugin, momentPlugin, scrollgridPlugin],
                initialView: 'dayGridMonth',
                defaultView: 'dayGridMonth',
                locale: ukLocale,
                views: {
                    dayGridMonth: { // name of view
                        //titleFormat: { year: 'numeric', month: '2-digit', day: '2-digit' }
                        // other view-specific options here
                    }
                },
                headerToolbar: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                    // left: 'prev,next today',
                    // center: 'title',
                    // right: 'dayGridMonth'//,timeGridWeek,timeGridDay
                },
                //  initialEvents: INITIAL_EVENTS, // alternatively, use the `events` setting to fetch from a feed
                editable: false,
                selectable: false,
                selectMirror: true,
                dateClick: this.handleDateClick,
                select: this.handleDateSelect,
                eventClick: this.handleEventClick,
                eventsSet: this.handleEvents,
                weekends: true,
                /* you can update a remote database when these fire:
                        eventAdd:
                        eventChange:
                        eventRemove:
                        */
                events: '/admin/calendar/get-event',
                eventRender: function (event, element) {
                    var originalClass = element[0].className;
                    element[0].className = originalClass + ' hasmenu';
                },
                dayRender: function (day, cell) {
                    var originalClass = cell[0].className;
                    cell[0].className = originalClass + ' hasmenu';
                }
            }
        }
    },

    methods: {
        handleDateSelect(selectInfo) {
            let title = prompt('Please enter a new title for your event')
            let calendarApi = selectInfo.view.calendar
            calendarApi.unselect() // clear date selection
            if (title) {
                calendarApi.addEvent({
                    id: createEventId(),
                    title,
                    start: selectInfo.startStr,
                    end: selectInfo.endStr,
                    allDay: selectInfo.allDay
                })
            }
        },
        handleDateClick: function (arg) {
            // alert('date click! ' + arg.dateStr)
        },
        //handleEventClick(clickInfo) {
        //    if (confirm(`Are you sure you want to delete the event '${clickInfo.event.title}'`)) {
        //        clickInfo.event.remove()
        //     }
        //},
        handleEvents(events) {
            this.currentEvents = events
        }

    }

});


Vue.component('caledar-teachers-view', {
    mixins: [FullCalendar],
    data: function () {
        return {
            calendarOptions: {
                schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
                plugins: [dayGridPlugin, interactionPlugin, momentPlugin, scrollgridPlugin],
                initialView: 'dayGridMonth',
                defaultView: 'dayGridMonth',
                locale: ukLocale,
                views: {
                    dayGridMonth: { // name of view
                        //titleFormat: { year: 'numeric', month: '2-digit', day: '2-digit' }
                        // other view-specific options here
                    }
                },
                headerToolbar: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                    // left: 'prev,next today',
                    // center: 'title',
                    // right: 'dayGridMonth'//,timeGridWeek,timeGridDay
                },
                //  initialEvents: INITIAL_EVENTS, // alternatively, use the `events` setting to fetch from a feed
                editable: true,
                selectable: true,
                selectMirror: true,
                dateClick: this.handleDateClick,
                select: this.handleDateSelect,
                eventClick: this.handleEventClick,
                eventsSet: this.handleEvents,
                weekends: true,
                /* you can update a remote database when these fire:
                        eventAdd:
                        eventChange:
                        eventRemove:
                        */
                events: '/admin/calendar/get-event',
                eventRender: function (event, element) {
                    var originalClass = element[0].className;
                    element[0].className = originalClass + ' hasmenu';
                },
                dayRender: function (day, cell) {
                    var originalClass = cell[0].className;
                    cell[0].className = originalClass + ' hasmenu';
                }
            }
        }
    },

    methods: {
        handleDateSelect(selectInfo) {
            let title = prompt('Please enter a new title for your event')
            let calendarApi = selectInfo.view.calendar
            calendarApi.unselect() // clear date selection
            if (title) {
                calendarApi.addEvent({
                    id: createEventId(),
                    title,
                    start: selectInfo.startStr,
                    end: selectInfo.endStr,
                    allDay: selectInfo.allDay
                })
            }
        },
        handleDateClick: function (arg) {
            // alert('date click! ' + arg.dateStr)
        },
        //handleEventClick(clickInfo) {
        //    if (confirm(`Are you sure you want to delete the event '${clickInfo.event.title}'`)) {
        //        clickInfo.event.remove()
        //     }
        //},
        handleEvents(events) {
            this.currentEvents = events
        }

    }

});

Vue.component('caledar-students-view', {
    mixins: [FullCalendar],
    data: function () {
        return {
            calendarOptions: {
                schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
                plugins: [dayGridPlugin, interactionPlugin, momentPlugin, scrollgridPlugin],
                initialView: 'dayGridMonth',
                defaultView: 'dayGridMonth',
                locale: ukLocale,
                views: {
                    dayGridMonth: { // name of view
                        //titleFormat: { year: 'numeric', month: '2-digit', day: '2-digit' }
                        // other view-specific options here
                    }
                },
                headerToolbar: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                    // left: 'prev,next today',
                    // center: 'title',
                    // right: 'dayGridMonth'//,timeGridWeek,timeGridDay
                },
                //  initialEvents: INITIAL_EVENTS, // alternatively, use the `events` setting to fetch from a feed
                editable: true,
                selectable: true,
                selectMirror: true,
                dateClick: this.handleDateClick,
                select: this.handleDateSelect,
                eventClick: this.handleEventClick,
                eventsSet: this.handleEvents,
                weekends: true,
                /* you can update a remote database when these fire:
                        eventAdd:
                        eventChange:
                        eventRemove:
                        */
                events: '/admin/calendar/get-event',
                eventRender: function (event, element) {
                    var originalClass = element[0].className;
                    element[0].className = originalClass + ' hasmenu';
                },
                dayRender: function (day, cell) {
                    var originalClass = cell[0].className;
                    cell[0].className = originalClass + ' hasmenu';
                }
            }
        }
    },

    methods: {
        handleDateSelect(selectInfo) {
            let title = prompt('Please enter a new title for your event')
            let calendarApi = selectInfo.view.calendar
            calendarApi.unselect() // clear date selection
            if (title) {
                calendarApi.addEvent({
                    id: createEventId(),
                    title,
                    start: selectInfo.startStr,
                    end: selectInfo.endStr,
                    allDay: selectInfo.allDay
                })
            }
        },
        handleDateClick: function (arg) {
            // alert('date click! ' + arg.dateStr)
        },
        //handleEventClick(clickInfo) {
        //    if (confirm(`Are you sure you want to delete the event '${clickInfo.event.title}'`)) {
        //        clickInfo.event.remove()
        //     }
        //},
        handleEvents(events) {
            this.currentEvents = events
        }

    }

});
