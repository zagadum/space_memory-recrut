import AppForm from '../app-components/Form/AppForm';
import Vue from "vue";



Vue.component('homework-form', {
    mixins: [AppForm],
    data: function() {
        return {
            number_min: 1,
            number_max: 1000,
             localeValue: 'uk',
            intervalMin: 0.1,
            intervalMax: 60,
            intervalStep: 0.1,
            form: {
                student_id:'',
                params_id:'',
                max_digit_number:0,
                training_type_id:1,
                capacity_list:'',
                interval_delta:1,

                сategory_list:'',
                procent_level_list:{},
                category_binary:'',
                categoryBinaryFlag:1,
                category_maths_list:'',
                capacity_maths_list:'',
                capacity_maths_list2:'',
                div_capacity_list:'',
                div_action_list:'',
                div_fraction_list:'',
                capability_faces_list:{},
                gender_list:{'label':'','value':'0'},
                capability_faces:0,
                category_faces:0,
                category_history:2,
                category_faces_list:{},
                div_action:'all',
                category:'noun-noun',
                is_present:'',
                div_comma:'0',
                evaluation:'practice',

                digit_number_list:'10',
                titleDigit_number:'',
                comma_number_list:'',
                comma_number:'',
                digit_number:'10',
                range_even_list:'',
                range_increment_list:'',
                interval_list:'',
                languages_list:'',
                repeat_number_list:{'value':'1'},
                cnt_operation_list:'',
                cnt_operation:1,
                level: 'learn',
                range_type: 'even',
                category_maths:'random',
                category_abacus_list:{},
                capacity_abacus_list:{'value':'1'}

            }
        }
    },
    methods: {
        trans(attributeKey) {
            return this.currentAttributes[attributeKey] || attributeKey;
        },

        categoryBinary() {

            this.form.categoryBinaryFlag=this.form.category_binary.value;
        },
        CalcTotalNumber() {
            this.number_min = 1;
            this.number_max= 1000;
            if (this.form.category_history_list==2){
                this.number_min = 1;
                this.number_max= 1000;
                return;
            }
            var this232_=this;
            axios.post('/student/traning/history-total-number', this.form)
                .then(response => {

                    if (response.data.max!=undefined) {
                        console.log('MAX Number :',  response.data.max);
                        this232_.number_max = response.data.max;
                        this232_.form.titleDigit_number='Max: '+response.data.max;
                        if (this232_.form.digit_number_list >=  this232_.number_max) {
                            this232_.form.digit_number_list =  this232_.number_max;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        },
        btnMinusPress: function (event) {
            this.form.digit_number_list= parseInt(this.form.digit_number_list)-1;
            if (this.form.digit_number_list<=1){
                this.form.digit_number_list=1;
            }
        },
        btnPlusPress: function (event) {
            this.form.digit_number_list= parseInt(this.form.digit_number_list)+1;
            if ( this.form.digit_number_list>=this.number_max){
                this.form.digit_number_list=this.number_max;

                this.$notify({ type: 'warning', title: this.trans('warning'), text:  this.trans('max_num')+this.number_max });//Ав
            }
        },
        _normalizeNumber(value) {
            const n = Number(value);
            if (isNaN(n)) return n;
            const rounded = Math.round(n * 10) / 10; // округление до 1 знака
            // если целое — вернуть без дробной части, иначе — с одним знаком
            return Number.isInteger(rounded) ? rounded : Number(rounded.toFixed(1));
        },
        btnIntervalMinusPress() {
            const step = Number(this.intervalStep) || 1;
            const min = Number(this.intervalMin);
            let val = Number(this.form.interval_delta) || min;
            val -= step;
            if (val < min) {
                val = min;
            }

            this.form.interval_delta = this._normalizeNumber(val);
        },
        btnIntervalPlusPress() {
            const step = Number(this.intervalStep) || 1;
            const max = Number(this.intervalMax);
            const min = Number(this.intervalMin);
            let val = Number(this.form.interval_delta);
            if (isNaN(val)) {
                val = min;
            }
            val += step;
            if (val > max) {
                val = max;
                this.$notify({ type: 'warning', title: this.trans('warning'), text:  this.trans('max_num')+max });

            }
            this.form.interval_delta = this._normalizeNumber(val);;
        },
        onChangeNumber: function (event) {
            if ( this.form.digit_number_list>=this.number_max){
                this.form.digit_number_list=this.number_max;
            }
            this.form.titleDigit_number='Max: '+this.number_max;
        },
        onChangeType: function (tableLink) {

            this.number_min = 2;
            this.number_max= 1000;
            if (tableLink=='training_number_letter'){
               // this.form.range_increment_list={'key':10,'value':'0-10'};//
            }
            if (tableLink=='training_memory'){
                this.form.level='practice';
            }
            if (tableLink=='training_words'){
                this.form.level='practice';
            }
            if (tableLink=='training_history'){
                this.form.level='practice';
                this.form.range_even_list = [];
                this.CalcTotalNumber();
            }
            if (tableLink=='training_associative'){
                if (this.form.level=='profi'){
                    this.form.level='learn';
                }
            }
            if (tableLink=='training_faces'){

            }
            if (tableLink=='training_abacus'){

                if (this.form.level=='learn'){
                    this.form.level='practice';
                }
                this.number_max= 25;
                this.number_min = 2;
            }
            if (tableLink=='olympiad_number_letter'){
                if (!this.form.evaluation) {
                    this.form.evaluation = 'practice';
                }
               // this.form.range_increment_list={'key':0,'value':'0-10'};
            }
            if (tableLink=='olympiad_memory' || tableLink=='olympiad_words' || tableLink=='olympiad_binnary' || tableLink=='olympiad_cards' || tableLink=='olympiad_faces' || tableLink=='olympiad_history'){
                if (!this.form.evaluation) {
                    this.form.evaluation = 'practice';
                }
            }


        }, //end onchene
        onChangeCategoryMaths: function (cat) {
            if (cat=='division'){
                this.form.div_comma='0';
            }


        }, //end onchene

//category_maths_list

    },

    beforeDestroy() { this.$validator.pause() },
    created() {
         this.localeValue = $('#locale').val();
       // console.log('ssss:',   this.currentAttributes);
        // console.log('localeValue:', this.localeValue);
          if   (this.localeValue==undefined) {
              this.localeValue = 'uk';
          }
        if (  this.localeValue === 'pl') {
            this.currentAttributes = require('./../pl.attribute.json');
        }
        if ( this.localeValue === 'uk') {
            this.currentAttributes = require('./../ua.attribute.json');
        }
        if ( this.localeValue === 'en') {
            this.currentAttributes = require('./../en.attribute.json');
        }

        if (this.form.max_digit_number>0) {
            this.number_max= this.form.max_digit_number;
        }
       //console.log('ssss:',   this.form);
        //alert('created');
    }
})

