import Vue from "vue";
import AppForm from '../app-components/Form/AppForm';
import FullCalendar from '@fullcalendar/vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import momentPlugin from '@fullcalendar/moment';
import timeGridPlugin from '@fullcalendar/timegrid';
import scrollgridPlugin from '@fullcalendar/scrollgrid';
import ukLocale from "@fullcalendar/core/locales/uk";
import plLocale from "@fullcalendar/core/locales/pl";
import enLocale from "@fullcalendar/core/locales/en-gb";
$('body').on('click', '.add_eventStudent', function(){
    var student_id=$("#student_id").val();
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

    document.location='/admin/homework/set/'+dateRet+'/student_id/'+student_id;

});


$('body').on('click', '.EditStudent ', function () {

    var dateParams = $(this).attr("data-params1");
    if (dateParams) {
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
    }
    var dateRet = day + "-" + month + "-" + year;
    $('#date_set').val(dateRet);
    $('#DialogCalendarCall').click();
});

$('body').on('click', '.EditListPrivate', function(){

    var student_id=$("#student_id").val();
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

    document.location='/admin/homework/list/'+dateRet+'/student_id/'+student_id;

});

Vue.component('calendar-students-views', {
    mixins: [FullCalendar],
    data: function () {
        return {
            form: {
                franchisee: '',
                franchisee_id: '',

            },

            calendarStudentOptions: {
                schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
                plugins: [dayGridPlugin, interactionPlugin, momentPlugin, scrollgridPlugin, timeGridPlugin],
                initialView: 'dayGridMonth',
                locale: ukLocale,
                timeZone: 'Europe/Kiev',
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
                eventOrder: 'sort_me',
                events: {
                    url: '/admin/calendar/students/get-event',
                    method: 'POST',
                    failure: function () {
                        alert('there was an error while fetching events!');
                    },
                    extraParams: function () {
                        return {
                            student_id: $("#student_id").val(),
                        };
                    }
                },

                eventDidMount: function (info) {

                    const d = new Date(info.event.extendedProps.dateStart);
                    let day = d.getDate();
                    let month = d.getMonth();
                    var keySet = day + '_' + month;
                    if (info.event.extendedProps.is_fail == 1) {
                        $('#btn_fail_' + keySet).removeClass('d-none').show();
                    }
                    if (info.event.extendedProps.is_done == 1) {
                        $('#btn_done_' + keySet).removeClass('d-none');
                        $('#btn_done' + keySet).show();
                    }
                    if (info.event.extendedProps.is_process == 1) {
                        $('#btn_process_' + keySet).removeClass('d-none');
                        $('#btn_process_' + keySet).show();
                    }
                    if (info.event.extendedProps.allow_add === 1) {
                        $('#btn_edit_' + keySet).removeClass('d-none').show();
                        $('#btn_add_' + keySet).removeClass('d-none');
                        $('#btn_menu_' + keySet).removeClass('d-none').show();
                    }else{


                    }


                    //console.log(info.event.title);
                    // info.event.extendedProps.description

                    let poperDialod={
                        placement: 'top',
                        trigger: 'hover',
                        container: 'body'
                    };


                    let helpText='';
                    if (info.event.extendedProps.desc!=undefined){
                        poperDialod.content=info.event.extendedProps.desc;
                    }
                    if (info.event.extendedProps.title_dialog!=undefined){
                        poperDialod.title=info.event.extendedProps.title_dialog;
                    }

                   // $(info.el).popover(poperDialod);
                },
                dayCellContent: function (item) {
                    let currentDay = new Date();
                    var CurrDay=currentDay.getDate();
                    var CurrMonth=currentDay.getMonth();
                    var CurrYears=currentDay.getFullYear();
                    const d = new Date(item.date);
                    let day = d.getDate();  let month = d.getMonth(); let years = d.getFullYear();
                    var keySet=day+'_'+month;
                    let hiddeAddClass = 'd-none';
                    if( (parseInt(day)>=parseInt(CurrDay) && CurrMonth==month && years==CurrYears)  || (month>CurrMonth && years==CurrYears) ||   years!=CurrYears ) {
                        hiddeAddClass='';
                    }
                    //d-none
                    //<button class="add_event ${item.classHide}"  id="btn_add_${keySet}" data-params1="${item.date}"><img src="/images/add_btn.svg" alt="Add"/></button>
                    // <li class="drop-menu-item"><a href="#"><img src="/images/menu_copy.svg" alt="copy" data-params1="${item.date}"/>Копировать</a></li>
                    // <div class="menu-wrap ${item.classHide}" id="btn_menu_${keySet}">
                    //     <ul class="menu">
                    //         <li class="menu-item">
                    //             <a href="#"><img src="/images/menu_list.svg" alt="Select" data-params1="${item.date}"/></a>
                    //             <ul class="drop-menu">
                    //                 <li class="drop-menu-item"> <a href="#"><img src="/images/menu_remove.svg" alt="Remove" data-params1="${item.date}"/>Удалить</a></li>
                    //                 <li class="drop-menu-item"><a href="#"><img src="/images/menu_edit.svg" alt="edit" data-params1="${item.date}"/>Редактировать</a></li>
                    //
                    //             </ul>
                    //         </li>
                    //     </ul>
                    // </div></div>
                    return {

                        html: `<div class="cell-top"  >
                                        <p>${(item.date).getDate()}</p>
                                        <div class="menu-wrap d-none "  id="btn_menu_${keySet}" >
                                            <ul class="menu">
                                                <li class="menu-item">
                                                    <a href="#"  class="EditListPrivate"  data-params1="${item.date}" onclick="return false;"><img src="/images/menu_list.svg"  alt="Select" data-params1="${item.date}"/></a>
                                                </li>
                                            </ul>
                                        </div>
                                       <button class="add_event add_eventStudent ${hiddeAddClass}" id="btn_add_${keySet}"    data-params1="${item.date}" title="Add Private Task" ><img src="/images/add_btn.svg" alt="Add" title="Add Private Task" /></button>
                                      <button class="add_event EditStudent d-none"  id="btn_edit_${keySet}" data-params1="${item.date}" ><img src="/images/menu_edit.svg" alt="Edit"/></button>

                                     <button class="calendar-btn-ico d-none" id="btn_done_${keySet}"  ><img src="/images/btn_star.svg" alt="star"/></button>
                                    <button class="calendar-btn-ico d-none"  id="btn_process_${keySet}"><img src="/images/btn_chart.svg" alt="chart"/></button>
                                    <button class="calendar-btn-ico d-none"   id="btn_fail_${keySet}"><img src="/images/btn_bookmark.svg" alt="bookmark"/></button>
                                    </div>
                                   `


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
            this.calendarStudentOptions.locale = plLocale;
        } else if (localeValue === 'en') {
            this.calendarStudentOptions.locale = enLocale;
        } else if (localeValue === 'uk') {
            this.calendarStudentOptions.locale = ukLocale;
        } else {
            this.calendarStudentOptions.locale = ukLocale;
        }
    }
});

var ModalCalendar = Vue.component('modal-confirm-calendar', {
    name: 'confirmModalCalendar',
    data() {
        return {
            modalWidth: 220,
        }
    },
    methods: {
        handleSubmit() {
            this.$modal.hide(this.$options.name);
            var date_set = $('#date_set').val();
            var student_id = $("#student_id").val();
            var _this7 = this;
            axios.post('/admin/calendar/students/reset-task-group', {
                'student_id': student_id,
                'date_set': date_set
            }).then(function (response) {
                _this7.$notify({
                    type: 'success',
                    title: 'Success!',
                    text: response.data.message ? response.data.message : _this.trans[dialogType].success
                });
                document.location.href = '/admin/homework';
            }, function (error) {
                _this7.$notify({
                    type: 'error',
                    title: 'Error!',
                    text: error.response.data.message ? error.response.data.message : _this.trans[dialogType].error
                });
            });
        },
        click_to_close() {

            this.$modal.hide(this.$options.name);
        },
    }

});
