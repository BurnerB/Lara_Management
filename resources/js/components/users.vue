<template>
    <div class="container">
      <!-- in v-if o need this.$gate -->
        <div class="row mt-5" v-if="$gate.isAdmin()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">User Management List</h3>

                <div class="card-tools">
                    <button class="btn btn-success" @click="newModal">Add New <i class="fas fa-user-plus fa-fw"></i></button>
                </div>
              
              </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Type</th>
                      <th>Registered at</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="user in users" :key="user.id">
                      <td>{{ user.id }}</td>
                      <td>{{ user.name}}</td>
                      <td>{{ user.email }}</td>
                      <td>{{ user.type | capitalize}}</td>
                      <td>{{ user.created_at | date }}</td>
                      <td>
                          <a href="#" @click="editModal(user)">
                              <i class="fa fa-edit blue"></i>
                          </a>
                            /
                          <a href="#" @click="deleteUser(user.id)">
                              <i class='fa fa-trash red'></i>
                          </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 v-show="!editmode" class="modal-title" id="exampleModalLabel">Add New User</h5>
                    <h5 v-show="editmode" class="modal-title" id="exampleModalLabel">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form  @submit.prevent="editmode ? updateUser() : createUser()">
                  <div class="modal-body">
                      <div class="form-group">
                        <!-- 2 way modal binding -->
                        <input v-model="form.name" type="text" name="name"
                        placeholder="Name"
                          class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
                        <has-error :form="form" field="name"></has-error>
                      </div>

                      <div class="form-group">
                        <!-- 2 way modal binding -->
                        <input v-model="form.email" type="text" name="email"
                        placeholder="Email"
                          class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                        <has-error :form="form" field="name"></has-error>
                      </div>

                      <div class="form-group">
                          <!-- 2 way modal binding -->
                          <textarea v-model="form.bio" type="text" name="bio"
                          placeholder=" short bio(optional)"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                          <has-error :form="form" field="bio"></has-error>
                    </div>

                      <div class="form-group">
                          <select name="type" v-model="form.type" class="form-control" :class="{
                            'is-invalid':form.errors.has('type')}">
                            <option value="">Select User role</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                            <option value="author">Author</option>
                          </select>
                          <has-error :form="form" field="bio"></has-error>
                    </div>
                  

                    <div class="form-group">
                          <!-- 2 way modal binding -->
                          <input v-model="form.password" type="password" name="password"
                          placeholder="Password"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                          <has-error :form="form" field="password"></has-error>
                    </div>

                    

                    

                    
                  </div>
                
                  <div class="modal-footer">
                      <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                      <button v-show="editmode" type="submit" class="btn btn-success">Update</button>
                      <button v-show="!editmode" type="submit" class="btn btn-primary">Create</button>
                  </div>
                </form>
                </div>
            </div>
            </div>
        </div>

    
</template>

<script>
    import Form from 'vform';
    export default {
      data(){
        return{
          editmode:false,
          users: {},
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
      methods:{
        updateUser(id){
          //progress bar start
          this.$Progress.start();
          //put with vform
          this.form.put('api/user/'+this.form.id)
              .then(()=>{
                // hide modal
                $('#addNew').modal('hide')
                // modal pop up
                Swal.fire(
                                'Updated!',
                                'Information Updated.',
                                'success'
                              ),
                //progress bar finish
                this.$Progress.finish();
                //reload table show updated data
                Fire.$emit('reload');            
              })
              .catch(()=>{
                //red progress bar if any errors
                this.$Progress.fail();
              })
        },

        editModal(user){
          //same modal for create and edit
          this.editmode = true;
          //vform reset
          this.form.reset()        
          $('#addNew').modal('show')

          //vform table filled with user data 
          this.form.fill(user)
        },

        newModal(){
          this.editmode = false;
          //vform reset
          this.form.reset()
          $('#addNew').modal('show')
        },

        //Delete User with Ajax Request and SweetAlert Modal 
        deleteUser(id){
          // modal pop up
          Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
          }).then((result) => {
            //send request to server
            if (result.value){
                this.form .delete('api/user/'+id)
                          .then(()=>{
                            // modal pop up
                              Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                              )
                              Fire.$emit('reload');
                              })
                          .catch(()=>{
                              Swal.fire('Failed',"Something went wrong.","warning")
                          });
            }
          })    
        },

        loadUsers(){ 
          if(this.$gate.isAdmin){
            axios.get('api/user')
                .then(({ data })=> (this.users = data.data));
          }        
        },
        
        createUser(){
                this.$Progress.start()

                //use promise (callback) to Detect Successfull HTTP  
                this.form.post('api/user')
                    .then(()=>{
                        Fire.$emit('reload');
                        $('#addNew').modal('hide')

                        Toast.fire({
                            icon: 'success',
                            title: 'User created successfully'
                          })
                        this.$Progress.finish();
                    })
                    .catch(()=>{
                        this.$Progress.fail();
                    })

    
                
      }
    },
    created() {
            this.loadUsers();
            // custom event--better way use laravel echo pusher
            Fire.$on('reload',()=>{
              this.loadUsers();
            })
            // update dataevery 3 seconds BAD ON PERFORMANCE
            // setInterval(()=>this.loadUsers(),3000);
        }
  }
</script>