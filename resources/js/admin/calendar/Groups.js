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
jQuery('body').on('click', '.add_eventGroup', function(){
     var group_id=jQuery("#group_id").val();
     var dateRet=jQuery( this ).attr( "data-params1");
     // if (dateParams){
     //     var d = new Date(dateParams);
     //     var day = d.getDate();
     //     var month = d.getMonth() + 1;
     //     var year = d.getFullYear();
     //     if (day < 10) {
     //         day = "0" + day;
     //     }
     //     if (month < 10) {
     //         month = "0" + month;
     //     }
     //     var dateRet = day + "-" + month + "-" + year;
     // }
    document.location='/admin/homework/set/'+dateRet+'/group_id/'+group_id;

});

function parseYMDLocal(ymd) {
    if (!ymd) return null;

    if (ymd instanceof Date) {
        return new Date(ymd.getFullYear(), ymd.getMonth(), ymd.getDate());
    }

    if (typeof ymd === 'string') {
        const [y, m, d] = ymd.split('-').map(Number);
        return new Date(y, m - 1, d);
    }

    return null;
}

$('body').on('click', '.EditGroups', function(){
    var group_id=$("#group_id").val();
    var dateRet=$( this ).attr( "data-params1");
    // if (dateParams){
    //     var d = new Date(dateParams);
    //     var day = d.getDate();
    //     var month = d.getMonth() + 1;
    //     var year = d.getFullYear();
    //     if (day < 10) {
    //         day = "0" + day;
    //     }
    //     if (month < 10) {
    //         month = "0" + month;
    //     }
    //     var dateRet = day + "-" + month + "-" + year;
    // }
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
                timeZone: 'Europe/Kiev',
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
                            const dateParse = new Date(info.event.extendedProps.dateStart);


                            const y = dateParse.getFullYear();
                            const m = String(dateParse.getMonth() + 1).padStart(2, '0');
                            const dd = String(dateParse.getDate()).padStart(2, '0');


                            var keySet=`${dd}-${m}-${y}`;

                            if (info.event.extendedProps.allow_edit===1 ) {
                                $('#btn_menu_' + keySet).removeClass('d-none').show();
                                $('#btn_add_' + keySet).addClass('d-none').hide();
                            }else{
                                $('#btn_menu_' + keySet).addClass('d-none').hide();
                            }
                            if (info.event.extendedProps.allow_add===1) {
                                $('#btn_add_' + keySet).removeClass('d-none').show();

                            }else{
                                //$('#btn_add_' + keySet).addClass('d-none').hide();

                               // $('#btn_add_' + keySet).remove();

                            }

                        }
                    } catch (error) {
                        console.log(error);
                    }
                },
                dayCellContent: function (item) {
                    const calendar = item.view.calendar;

                    const serverTodayStr = (window.SERVER_TODAY && String(window.SERVER_TODAY).trim())
                        ? String(window.SERVER_TODAY).trim()
                        : null;
                    const currentDay = parseYMDLocal(serverTodayStr) || new Date(
                        item.date.getFullYear(), item.date.getMonth(), item.date.getDate()
                    ); // fallback: хотя бы не падаем


                    var CurrDay=currentDay.getDate();
                    var CurrMonth=currentDay.getMonth();
                    var CurrYears=currentDay.getFullYear();

                    const d = new Date(item.date.getFullYear(), item.date.getMonth(), item.date.getDate());
                    const day = d.getDate();
                    const month = d.getMonth();
                    const years = d.getFullYear();

                    const y = item.date.getFullYear();
                    const m = String(item.date.getMonth() + 1).padStart(2, '0');
                    const dd = String(item.date.getDate()).padStart(2, '0');
                    const ymd = `${y}-${m}-${dd}`;

                    var keySet=`${dd}-${m}-${y}`;
                    console.log('keySetView=',keySet);

                    if( (day>=(CurrDay-1) && CurrMonth===month && years===CurrYears)  || (month>CurrMonth && years===CurrYears)  )
                    {

                        return {
                            html:
                                `<div class="cell-top"><p>${dd}</p><button class="add_event add_eventGroup" id="btn_add_${keySet}"    data-params1="${keySet}"><img src="/images/add_btn.svg" alt="Add"/></button>
<div class="menu-wrap d-none"  id="btn_menu_${keySet}" >
                                            <ul class="menu">
                                                <li class="menu-item">
                                                    <a href="#"  class="EditGroups"  data-params1="${keySet}" onclick="return false;"><img src="/images/menu_list.svg"  alt="Select" data-params1="${keySet}"/></a>
                                                </li>
                                            </ul>
                                        </div>
                                        </div>`
                        }
                    }else{
                        return {html:`<div class="cell-top"><p>${dd}</p><div class="menu-wrap d-none"  id="btn_menu_${keySet}" >
                                            <ul class="menu">
                                                <li class="menu-item">
                                                    <a href="#"  class="EditGroups"  data-params1="${keySet}" onclick="return false;"><img src="/images/menu_list.svg"  alt="Select" data-params1="${keySet}"/></a>
                                                </li>
                                            </ul>
                                        </div> </div>`}
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
        },

        // onclick="document.location='/admin/homework/${item.date}/${item.group_id}';"
    }, mounted: function () {

        var localeValue = $('#locale').val();
        if (localeValue === 'pl') {
            this.calendarOptions.locale = plLocale;
        } else if (localeValue === 'uk') {
            this.calendarOptions.locale = ukLocale;
        } else if (localeValue === 'en') {
            this.calendarOptions.locale = enLocale;
        } else {
            this.calendarOptions.locale = ukLocale;
        }
    }
});

