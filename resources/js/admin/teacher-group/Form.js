
import AppForm from '../app-components/Form/AppForm';
import AppListing from '../app-components/Listing/AppListing';
import Vue from "vue";
Vue.component('student-listing', {
    mixins: [AppListing]
});


Vue.component('teacher-group-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                id:'',
                country_id:  '' ,
                region_id:  '' ,
                city_id:  '' ,
                age_id:  '1' ,
                franchisee:  '' ,
                franchisee_id:  '' ,
                teacher:  '' ,
                country:  '' ,
                region:  '' ,
                city:  '' ,
                name:  '' ,
                age:  '' ,
                address:  '' ,
                locations:  '' ,
                start_day:  '' ,
                start_time:  '' ,
                workday1:  '' ,
                workday2:  '' ,
                workday3:  '' ,
                workday4:  '' ,
                workday5:  '' ,
                workday6:  '' ,
                workday7:  '' ,
                zoom_url:  '' ,
                zoom_img:  '' ,
                zoom_text:  '' ,
                enabled:  false ,
                isTeacherDisabled:true,
                isRegionDisabled: true,
                isCityDisabled: true,

            },
            isLoadingTeacher:false,
            isLoadingCity:false,
            isLoadingRegion:false,
            TeacherMe:[],
            RegionMe:[],
            CityMe:[],
            StartInit:1
        }
    },
    methods: {
         nameFranchisee:function ({ first_name,surname }) {
                return `${surname} ${first_name}`
            },
        nameTeacher:function ({ first_name,surname }) {
            return `${surname} ${first_name}`
        },
        //----- Load Ajax Teacher by franchisee-id
        onChangeFranchisee: function (franchiseeID) {
            console.log(franchiseeID);
            console.log('StartInit='+this.StartInit);
            this.isLoadingTeacher=true;
            this.form.isTeacherDisabled=true;
            this.TeacherMe=[];
            if (this.StartInit==0) {
                this.form.teacher = '';
                this.form.teacher_id = '';
            }
            this.isLoadingCity=true;
            this.form.isCityDisabled=true;
            this.CityMe=[];
            if (this.StartInit==0) {
                this.form.city_id = '';
                this.form.city = '';
            }

            this.isLoadingRegion=true;
            this.form.isRegionDisabled=true;

            this.RegionMe=[];
            if (this.StartInit==0) {
                this.form.region = '';
                this.form.region_id = '';
            }

            var self = this;
            if(franchiseeID){
                $.ajax({
                    type:"GET",
                    contentType : 'application/json',
                    url:window.location.origin+"/admin/teacher-groups/get-teacher-list/"+franchiseeID,
                    success:function(res){
                        self.TeacherMe=[];
                        self.RegionMe=[];
                        self.CityMe=[];
                        if (res.teacher){

                            self.TeacherMe=res.teacher;
                            self.form.isTeacherDisabled=false;
                        }
                        if (res.region){
                            self.RegionMe=res.region;
                            self.form.isRegionDisabled=false;
                        }
                        if (res.city){
                            self.CityMe=res.city;
                            self.form.isCityDisabled=false;
                        }
                        self.isLoadingTeacher=false;
                        self.isLoadingCity=false;
                        self.isLoadingRegion=false;

                    },
                    error: function (error) {
                        alert(JSON.stringify(error));
                    }
                });
            }
        }, //end onchene

        //----- Load Ajax City by region-id
        onChangeRegion: function (regionID) {
            console.log(regionID);
            this.isLoadingCity=true;

            console.log('onChangeRegion');

            if (this.StartInit==0) {
                this.form.city_id = '';
                this.form.city = '';
            }
            this.CityMe=[];
            var self = this;
            if(regionID){
                $.ajax({
                    type:"GET",
                    contentType : 'application/json',
                    url:window.location.origin+"/admin/franchisees/get-city-list/"+regionID,
                    success:function(res){
                        console.log(res);
                        self.CityMe=res;
                        self.isLoadingCity=false;
                        self.form.isCitDisabled=false;
                    },
                    error: function (error) {
                        alert(JSON.stringify(error));
                    }
                });
            }
        },//end onchene
        uploadFile(event) {
            const file = event.target.files[0];
            const formData = new FormData();
            formData.append("photo", file);
            axios.post("/admin/teacher-groups/"+this.form.id+"/upload", formData, {headers: {"Content-Type": "multipart/form-data"}})
                .then(response => {
                    if (response.data.success){
                        this.form.zoom_img = response.data.photoUrl+'?'+Math.random();
                        this.$notify({ type: 'success', title: 'Success!', text: '' });
                    }else
                    {
                        this.$notify({ type: 'error', title: 'Error!', text: error.response.data.errors });
                    }
                })
                .catch(error => {
                    this.$notify({ type: 'error', title: 'Error!', text: error.response.data.errors });
                    console.error("Error uploading photo:", error);
                });
        }

    },
    created() {
        if (this.StartInit==1){
            if (this.form.franchisee_id>0){
                this.onChangeFranchisee(this.form.franchisee_id);
            }

            if (this.form.region_id>0){
                    this.onChangeRegion(this.form.region_id);
              }

        }
        this.StartInit=0;

    }


});

