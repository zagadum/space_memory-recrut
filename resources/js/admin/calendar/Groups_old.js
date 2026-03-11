import Vue from "vue";
import FullCalendar from '@fullcalendar/vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import momentPlugin from '@fullcalendar/moment';
import timeGridPlugin from '@fullcalendar/timegrid';
import scrollgridPlugin from '@fullcalendar/scrollgrid';
import ukLocale from "@fullcalendar/core/locales/uk";

$('body').on('click', '.add_eventGroup', function(){
     var group_id=$("#group_id").val();
     var dateParams=$( this ).attr( "data-params1");
     if (dateParams){
         var d = new Date(dateParams);
         var day = d.getDate();
         var month = d.getMonth() + 1;
         var year = d.getFullYear();
         if (day < 10) {
             day = "0" + day;
         }
         if (month < 10) {
             month = "0" + month;
         }
         var dateRet = day + "-" + month + "-" + year;
     }
    document.location='/admin/homework/set/'+dateRet+'/group_id/'+group_id;

});
$('body').on('click', '.edit_eventGroup', function(){
    var group_id=$("#group_id").val();
    var dateParams=$( this ).attr( "data-params1");
    if (dateParams){
        var d = new Date(dateParams);
        var day = d.getDate();
        var month = d.getMonth() + 1;
        var year = d.getFullYear();
        if (day < 10) {
            day = "0" + day;
        }
        if (month < 10) {
            month = "0" + month;
        }
        var dateRet = day + "-" + month + "-" + year;
    }
    document.location='/admin/homework/list/'+dateRet+'/group_id/'+group_id;

});


Vue.component('caledar-groups-view', {
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
                headerToolbar: {left: 'prev', center: 'title', right: 'next'},
                views: {
                    dayGridMonth: {
                        displayEventTime: false,
                        // dayCellContent(item) {
                        //     console.log(item);
                        //
                        // }
                    }
                },
                editable: false,
                selectable: false,
                selectMirror: false,
                eventClick: this.handleEventClick,
                dayMaxEvents: true,
                weekends: true,
                events: {
                    url: '/admin/calendar/groups/get-event',
                    method: 'POST',
                    failure: function() {
                        alert('there was an error while fetching events!');
                    },
                    extraParams: function() {
                        return {
                            group_id: $("#group_id").val(),
                        };
                    }
                },
                eventDidMount: function(info) {
                    try {
                        if (info) {
                            if (info.event.extendedProps.allow_add === 0) {
                                const d = new Date(info.event.extendedProps.dateStart);
                                let day = d.getDate();  let month = d.getMonth();
                                var keySet=day+'_'+month;

                                $('#btn_add_' + keySet).addClass('d-none').hide();
                                $('#btn_menu_' + keySet).removeClass('d-none').show();

                            }

                        }
                    } catch (error) {
                        console.log(error);
                    }
                },
                dayCellContent: function (item) {

                    let currentDay = new Date().toISOString().replace('-', '/').split('T')[0].replace('-', '/');
                    let dayFromCell = (item.date).toISOString().replace('-', '/').split('T')[0].replace('-', '/');

                    const d = new Date(item.date);
                    let day = d.getDate();  let month = d.getMonth();
                    var keySet=day+'_'+month;

                    if(dayFromCell>= currentDay) {
                        return {
                            html:
                                `<div class="cell-top"><p>${(item.date).getDate()}</p><button class="add_event add_eventGroup" id="btn_add_${keySet}"    data-params1="${item.date}"><img src="/images/add_btn.svg" alt="Add"/></button>
<div class="menu-wrap d-none"  id="btn_menu_${keySet}" >
                                            <ul class="menu">
                                                <li class="menu-item">
                                                    <a href="#"><img src="/images/menu_list.svg" alt="Select" data-params1="${item.date}"/></a>
                                                    <ul class="drop-menu">
                                                        <li class="drop-menu-item"> <a href="#"><img src="/images/menu_remove.svg" alt="Remove" data-params1="${item.date}"/>Удалить</a></li>
                                                        <li class="drop-menu-item"><a href="#" class="edit_eventGroup"  data-params1="${item.date}"><img src="/images/menu_edit.svg" alt="edit" data-params1="${item.date}"/>Редактировать</a></li>

                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                        </div>`
                        }
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
        // onclick="document.location='/admin/homework/${item.date}/${item.group_id}';"
    }
});

