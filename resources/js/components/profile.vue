<style>
.widget-user-header{
    background-position: center center;
    background-size:cover; 

}
</style>

<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-3">

            <!-- Widget: user widget style 1 -->
            <div class="card card-widget widget-user">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              <div class="widget-user-header text-white" style="background-image: url('./image/background.jpg');">
                <h3 class="widget-user-username text-right">{{ this.form.name }}</h3>
                <h5 class="widget-user-desc text-right">{{ this.form.type }}</h5>
              </div>
              <div class="widget-user-image">
                <!-- : bind src atribute to a method for pic-->
                <img class="img-circle" :src="getProfilePic()" alt="User Avatar">
              </div>
            </div>
            <!-- /.widget-user -->  
            
            
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">


                  <div class="tab-pane active" id="settings">
                    <form class="form-horizontal" method="put">
                     
                      <div class="form-group ">
                        <label for="inputName" class="col-sm-12 control-label">Name</label>
                        <div class="col-sm-12">
                          <input type="name" v-model="form.name"  class="form-control" id="inputName" placeholder="Name" :class="{ 'is-invalid': form.errors.has('name') }">
                          <has-error :form="form" field="name"></has-error>
                        </div>
                      </div>

                      <div class="form-group ">
                        <label for="inputEmail" class="col-sm-12 control-label">Email</label>
                        <div class="col-sm-10">
                          <input  type="email" v-model="form.email" class="form-control" id="inputEmail" placeholder="Email" :class="{ 'is-invalid': form.errors.has('email') }">
                          <has-error :form="form" field="email"></has-error>
                        </div>
                      </div>

                      <div class="form-group ">
                        <label for="io" class="col-sm-12 control-label">bio</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" v-model="form.bio"  id="inputExperience" placeholder="bio" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                          <has-error :form="form" field="bio"></has-error>
                        </div>
                      </div>

                      <div class="form-group ">
                        <label for="Photo" class="col-sm-12 control-label">Profile Photo</label>
                        <div class="col-sm-10">
                            <input type="file" @change="updatePhoto" name="fileToUpload" id="fileToUpload">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="Password" class="col-sm-12 control-label">Password(leave empty if not changing)</label>
                        <div class="col-sm-10">
                          <input type="password" v-model="form.password" class="form-control" id="inputSkills" placeholder="password" :class="{ 'is-invalid': form.errors.has('password') }">
                          <has-error :form="form" field="password"></has-error>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-offset-2 col-sm-12">
                          <!-- prevent  wont refresh the page -->
                          <button @click.prevent='updateInfo' type="submit" class="btn btn-success">Update</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
                        
            </div>
        </div>
    </div>
</template>

<script>
    import Form from 'vform';
    export default {
        data(){
          return{
            form: new Form({
            id:'',
            name:'',
            email : '',
            password : '',
            type:'',
            bio :'',
            photo : ''
          })
          }
        },
        mounted() {
            console.log('Component mounted.')
        },
        methods:{
          getProfilePic(){
            //  if this.form.photo value matches any regular expression which a based64 data has, 
            // then assign an empty string ' ' to prefix variable 
            // otherwise assign the '/img/profile/' instead
            let photo = (this.form.photo.length>100) ? this.form.photo : '/image/profile/'+this.form.photo;
            return photo;
          },
          updateInfo(){
            this.$Progress.start();
            this.form.put('api/profile')
                .then(()=>{
                  Toast.fire({
                            icon: 'success',
                            title: 'Updated Successfully'
                          })
                  this.$Progress.finish();
                })
                .catch(()=>{
                  this.$Progress.fail();
                })
          },
          updatePhoto(e){
            //convert photo to base64
            let file = e.target.files[0];
            let reader = new FileReader();
            // can only upload file less than 2mb
            if(file.size < 2111775){
              reader.onload = ()=>{
                  this.form.photo= reader.result;
                }
            reader.readAsDataURL(file);
            }else{
                Swal.fire({
                  type:'error',
                  title:'Oooops..',
                  text:'File cannot be more than 2Mb'
                })
            }
            
          },
          getData(){
            axios.get('api/profile')
                .then(({ data })=>(this.form.fill(data)));  
                Fire.$emit('GetData');
                this.$Progress.finish();
                }
        },

        //once component created run this function
        created(){
              this.getData();
              Fire.$on('GetData',()=>{
                  this.getData()
              })
        }
    }
</script>
