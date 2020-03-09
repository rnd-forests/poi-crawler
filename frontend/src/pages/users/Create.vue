<template>
 <dynamic-default-layout>
   <div class="modal-dialog">
     <div class="modal-content border-0">
       <h4 class="mt-2 text-center font-weight-bold text-gray-800 text-uppercase">Tạo người dùng</h4>
       <div class="modal-body">
         <form @submit.prevent="createUser">
           <div class="form-group">
             <input v-model.lazy.trim="payload.email"
                    v-validate="'required|email'"
                    name="email"
                    type="text"
                    placeholder="Email"
                    class="form-control"
                    autofocus
             >
             <p v-if="errors.has('email')" class="text-danger">{{ errors.first('email') }}</p>
           </div>

           <div class="form-group">
             <input v-model.lazy.trim="payload.password"
                    v-validate="'required|min:6'"
                    name="password"
                    type="password"
                    placeholder="password"
                    class="form-control"
             >
             <p v-if="errors.has('password')" class="text-danger">{{ errors.first('password') }}</p>
           </div>

           <div class="form-group">
             <input v-model.lazy.trim="payload.name"
                    v-validate="'required'"
                    name="name"
                    type="text"
                    placeholder="Name"
                    class="form-control"
             >
             <p v-if="errors.has('name')" class="text-danger">{{ errors.first('name') }}</p>
           </div>

           <div class="form-group">
             <input v-model.lazy.trim="payload.phone"
                    v-validate="'required'"
                    name="phone"
                    type="text"
                    placeholder="Phone"
                    class="form-control"
             >
             <p v-if="errors.has('phone')" class="text-danger">{{ errors.first('phone') }}</p>
           </div>

           <div class="form-group">
             <select v-model.number="payload.role_id" name="role_id" class="form-control">
               <option value="0" selected>User</option>
               <option value="1">Admin</option>
             </select>
           </div>

           <div class="mb-2 mt-3 d-flex justify-content-between">
             <router-link to="/users" class="btn btn-secondary w-25">Cancel</router-link>
             <btn-loading :loading="loading" type="submit" class="btn btn-primary font-weight-bold w-25">OK</btn-loading>
           </div>
         </form>
       </div>
     </div>
   </div>
 </dynamic-default-layout>
</template>

<script>
import userService from '@/services/userService'
import bFormGroup from 'bootstrap-vue/es/components/form-group/form-group'

export default {
  components: {
    bFormGroup,
  },
  data: () => ({
    loading: false,
    payload: {
      email: null,
      password: null,
      name: null,
      phone: null,
      role_id: 0,
    }
  }),

  methods: {
    createUser () {
      userService.createUser(this.payload)
        .then(() => this.$router.push('/users'))
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
