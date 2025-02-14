<template>
  <b-container fluid>
    <b-form @submit="formSubmit">
      <page-banner v-if="profile || cannot(authUser, permissions.USERS_EDIT)">
        <b-col cols="6">
          <h1>{{ $t('common.my_profile') }}</h1>
        </b-col>
        <b-col cols="6">
        </b-col>
      </page-banner>
      <page-banner v-else :breadcrumbs="[{ name: isApiUser ? 'api-usage.api-users' : 'users.list', params: {}, text: $t('users.list.manage') }]">
        <b-col cols="6">
          <h1 v-if="newUser">{{ $t('users.edit.create_user') }}</h1>
          <h1 v-else>{{ $t('users.list.detail') }}</h1>
        </b-col>
        <b-col cols="6">
        </b-col>
      </page-banner>

      <b-row class="pl-4 pr-4 pb-4 pt-4 bg-white">
        <b-col>
          <h3 class="styled-heading text-uppercase">
            {{ $t('users.edit.user_details') }}
          </h3>
          <b-card class="bg-grey">
            <b-row class="mb-3">
              <b-col cols="2" xl="1" class="pr-0 rtl-pr-3">
                <v-gravatar v-if="user.photo_url" :email="user.email" alt="" role="presentation" default-img="mm" :size="60" class="img-fluid rounded-circle"/>
                <div v-else>
                  <avatar v-if="!loading" :size="60" :username="user.first_name === null ? '' : user.first_name + ' ' + user.last_name"></avatar>
                </div>
              </b-col>

              <b-col cols="10" xl="11">
                <b-row>
                  <b-col>
                    <label for="first-name">{{ $t('first_name') }}</label>
                    <b-form-input
                      v-model="user.first_name"
                      type="text" id="first-name"
                      name="first-name"
                      :placeholder="$t('first_name')"
                      required
                      class="mb-1 inputs-form"/>

                      <transition name="shake">
                        <div v-if="formErrors.first_name">
                          <i class="text-danger" v-for="error in formErrors.first_name" :key="error">{{ error }} <br /></i>
                        </div>
                      </transition>
                  </b-col>

                  <b-col sm>
                    <label for="last-name">{{ $t('last_name') }}</label>
                    <b-form-input
                      v-model="user.last_name"
                      type="text" id="last-name"
                      name="last-name"
                      :placeholder="$t('last_name')"
                      class="mb-1 inputs-form"
                      required/>

                      <transition name="shake">
                        <div v-if="formErrors.first_name">
                          <i class="text-danger" v-for="error in formErrors.first_name" :key="error">{{ error }} <br /></i>
                        </div>
                      </transition>
                  </b-col>
                  <b-col>
                    <label for="email">{{ $t('email') }}</label>
                    <b-input-group class="mb-2">
                      <b-form-input
                        type="email"
                        v-model="user.email"
                        id="email"
                        name="email"
                        class="mb-1 inputs-form"
                        :placeholder="$t('email')"
                        :disabled="emailDisabled && !newUser"
                        required/>
                      <b-button v-if="isMe" slot="right" variant="dark" v-b-toggle.email-update @click="emailDisabled = !emailDisabled" class="ml-2 rtl-mr-2">{{ $t('common.edit') }}</b-button>
                    </b-input-group>
                    <transition name="shake">
                      <div v-if="formErrors.email">
                        <i class="text-danger" v-for="error in formErrors.email" :key="error">{{ error }} <br /></i>
                      </div>
                    </transition>
                    <b-collapse id="email-update">
                      <i>{{ $t('users.edit.email_update') }}</i>
                      <b-form-input type="password" v-model="userPassword"></b-form-input>
                      <transition name="shake">
                        <div v-if="formErrors.password">
                          <i class="text-danger" v-for="error in formErrors.password" :key="error">{{ error }} <br /></i>
                        </div>
                      </transition>
                    </b-collapse>
                  </b-col>
                </b-row>
              </b-col>
            </b-row>
            <b-row class="mb-3">
              <b-col cols="2" xl="1" class="pr-0 rtl-pr-3">
              </b-col>
              <b-col cols="10" xl="11"  v-if="isMe">
                    <update-password ref="uploadPasswordRef"></update-password>
              </b-col>
            </b-row>
            <b-row class="mb-3" v-if="!newUser && user.role_id === 1">
              <b-col cols="2" xl="1" class="pr-0">
              </b-col>
              <b-col cols="10" xl="11">
                <b-row>
                  <b-col cols="4">
                    <label for="api_used_in">{{ $t('register_form.api_used_in') }} <span style="color:red">*</span></label>
                    <b-input-group class="mb-1">
                      <b-form-textarea
                      type="api_used_in"
                      v-model="user.api_used_in"
                      id="api_used_in"
                      name="api_used_in"
                      class="mb-1 inputs-form"
                      required
                      :disabled="!isMe"
                      />
                    </b-input-group>
                    <transition name="shake">
                      <div v-if="formErrors.api_used_in">
                        <i class="text-danger" v-for="error in formErrors.api_used_in" :key="error">{{ error }} <br /></i>
                      </div>
                    </transition>
                  </b-col>
                </b-row>
              </b-col>
            </b-row>

            <b-row class="mb-3" v-if="!newUser && !isMe && can(authUser, permissions.USERS_EDIT)">
              <b-col cols="2" xl="1" class="pr-0">
                <label for="status">{{ $t('status') }}</label>
              </b-col>

              <b-col cols="10" xl="11">
                <p v-if="user.activated">
                  {{ $t('users.status.active') }}
                </p>
                <p v-else>
                  {{ $t('users.status.deactivated') }}
                </p>
                <b-button size="sm" variant="dark" class="mt-2 d-block" @click="resendActivation" v-if="!isMe && (can(authUser, permissions.USERS_DEACTIVATE) && can(authUser, permissions.USERS_REACTIVATE))">
                  <fa :icon="['fas', 'spinner']" spin v-show="sendingActivation"/>
                  {{ $t('common.resend_activation') }}
                </b-button>
              </b-col>
            </b-row>
            <b-row class="mb-4" v-if="profile || cannot(authUser, permissions.USERS_EDIT)">
              <b-col cols="6">
              </b-col>
              <b-col cols="6">
                <b-button type="submit" size="lg" class="float-right rtl-float-left mr-2 btn-outline-primary" @click="update = true">
                  <fa :icon="['fas', 'spinner']" spin v-show="updating"/>
                  {{ $t('common.save_changes') }}
                </b-button>
              </b-col>
            </b-row>
            <b-row class="mb-4" v-else :breadcrumbs="[{ name: isApiUser ? 'api-usage.api-users' : 'users.list', params: {}, text: $t('users.list.manage') }]">
              <b-col cols="6">
              </b-col>
              <b-col cols="6">
                <b-button size="lg" variant="light" class="float-right rtl-float-left" @click="goToUserList" v-if="!isMe">
                  {{ $t('common.cancel') }}
                </b-button>

                <b-button type="submit" class="float-right rtl-float-left mr-2 btn-outline-primary" v-if="(!newUser && can(authUser, permissions.USERS_EDIT)) || isMe" @click="update = true">
                  <fa :icon="['fas', 'spinner']" spin v-show="updating"/>
                  {{ $t('common.save_changes') }}
                </b-button>
              </b-col>
            </b-row>
          </b-card>
        </b-col>
      </b-row>

      <b-row class="pl-4 pr-4 pb-4 pt-3 bg-white" v-if="!isMe">
        <b-col>
          <h3 class="styled-heading text-uppercase">
            {{ $t('users.edit.roles_heading') }}
          </h3>
          <b-card class="bg-grey">
            <b-row>
              <b-col cols="7">
                <label for="roles-permissions" class="visually-hidden">{{ $t('users.edit.roles_label') }}</label>
                <b-form-select
                  id="roles-permissions"
                  name="roles-permissions"
                  v-model="roleId"
                  value-field="id"
                  text-field="name"
                  :options="filteredRoles"
                  :disabled="isMe"
                  class="mb-3"></b-form-select>
                </b-col>
              </b-row>

              <div v-if="roleOptionText && roleOptionText.length > 0">
                <i class="mb-3 d-block" v-b-toggle.collapseUserPermission>
                  {{ $t('users.edit.user_can_do')}}
                  <fa icon="chevron-down" fixed-width/>
                  <fa icon="chevron-up" fixed-width/>
                </i>
                <b-collapse id="collapseUserPermission" class="mt-2">
                  <ul class="row">
                    <li class="col col-6" v-for="item in roleOptionText">
                      {{ item.display_name }}
                    </li>
                  </ul>
                </b-collapse>
              </div>
              <div v-else>
                <i>{{ $t('users.edit.user_no_permission')}}</i>
              </div>

              <transition name="shake">
                <div v-if="formErrors.role_id">
                  <i class="text-danger" v-for="error in formErrors.role_id" :key="error">{{ error }} <br /></i>
                </div>
              </transition>
          </b-card>
        </b-col>
         </b-row>
      <hr>
         <b-row class="pl-4 pr-4 pb-4 pt-3 bg-white" v-if="can(authUser, permissions.CONTENT_EDIT)">
            <b-col>
              <h3 class="styled-heading text-uppercase">
                {{ $t('users.edit.societies_heading') }}
              </h3>
               <b-card class="bg-grey">
                 <b-row>
                   <b-col>
                     <b-button variant="link" class="float-right btn-outline-primary" @click="societies.showAll = true"
                        v-if="!societies.showAll && userSocieties.length > societies.limitNumber">
                        <u>{{ $t('users.edit.view')}} {{ userSocieties.length - societies.limitNumber }} {{ $t('users.edit.more')}}
                        <fa icon="chevron-down" fixed-width/></u>
                     </b-button>
                     <b-button variant="link" class="float-right btn-outline-primary" @click="societies.showAll = false"
                        v-if="societies.showAll && userSocieties.length > societies.limitNumber">
                        <u>{{ $t('users.edit.less')}}
                        <fa icon="chevron-up" fixed-width/></u>
                     </b-button>
                   </b-col>
                 </b-row>

                 <div class="times-container mb-4" v-if="userHasPermission(permissions.ALL_ORGANISATIONS) || userSocieties.length > 0">
                    <transition-group name="fade-slide" tag="b-row">
                      <b-col
                        cols="auto" class="mb-3" v-for="(n, index) in societies.showAll ? userSocieties.length : societies.limitNumber"
                        :key="userSocieties[index].countryCode" v-if="userSocieties[index]">
                        <div class="times-card">
                           <div class="d-flex justify-content-between align-items-center">
                             <span class="mr-2">{{ userSocieties[index].name }}</span>
                             <b-button size="sm" class="times-btn" @click="removeSociety(index)" v-if="can(authUser, permissions.USERS_EDIT)">
                               <fa icon="times"/>
                             </b-button>
                           </div>
                        </div>
                      </b-col>
                    </transition-group>
                  </div>

                  <b-row v-if="can(authUser, permissions.USERS_EDIT)">
                     <b-col md="12" xl="12" class="mb-3" v-if="!userHasPermission(permissions.ALL_ORGANISATIONS)">
                        <b-button size="sm" class="btn-outline-primary" @click="societies.isAddSocVisible = true" v-if="!societies.isAddSocVisible">
                           {{ $t('users.edit.add_society')}}
                        </b-button>
                        <div v-if="societies.isAddSocVisible && can(authUser, permissions.USERS_EDIT)" class="d-flex align-items-center">
                             <selectSociety :selected.sync="societies.selectedSoc" class="mr-3"></selectSociety>
                           <div>
                              <b-button size="sm" class="mr-3 btn-outline-primary" @click="addSociety(societies.selectedSoc)">
                                {{ $t('common.add') }}
                              </b-button>
                              <b-button class="cancel-btn" variant="light" @click="societies.isAddSocVisible = false">
                                {{ $t('common.cancel') }}
                              </b-button>
                           </div>
                        </div>
                     </b-col>
                  </b-row>
               </b-card>
            </b-col>
         </b-row>

      <hr>

        <audit-log v-bind:user-id="user.id" v-if="user.id != null && can(authUser, permissions.CONTENT_CREATE)"></audit-log>
         <b-row>
           <b-col>
             <b-button size="lg" :variant="user.activated ? 'danger' : 'success'" class="float-right mr-2 mb-4 mt-4" v-if="(!newUser && !isMe) && (can(authUser, permissions.USERS_DEACTIVATE) && can(authUser, permissions.USERS_REACTIVATE))" @click="toggleDeactivate(user)">
               {{ user.activated ? $t('common.deactivate_user') : $t('common.reactivate_user') }}
             </b-button>
             <b-button type="submit" size="lg" variant="dark" class="float-right mr-2 mb-4 mt-4" @click="reset = false" v-if="newUser && can(authUser, permissions.USERS_CREATE)">
                <fa :icon="['fas', 'spinner']" spin v-show="saving && !reset"/>
                {{ $t('common.send_invite') }}
             </b-button>
             <b-button type="submit" size="lg" variant="dark" class="float-right mr-2 mb-4 mt-4" v-if="newUser && can(authUser, permissions.USERS_CREATE)" @click="reset = true">
                <fa :icon="['fas', 'spinner']" spin v-show="saving && reset"/>
                {{ $t('common.invite_and_create') }}
             </b-button>
           </b-col>
         </b-row>
      </b-form>
   </b-container>
</template>

<script>
import swal from 'sweetalert2'
import axios from 'axios'
import { mapGetters } from 'vuex'
import SelectSociety from '~/pages/content/selectSociety'
import PageBanner from '~/components/PageBanner'
import Avatar from 'vue-avatar'
import * as permissionsList from '../../store/permissions'
import UpdatePassword from '~/pages/settings/password'
import AuditLog from '../content/auditLog'

const items = [{
  action: 'Edited Content',
  content: 'What Now > Radiological Emergency',
  society: 'Canada',
  date: '12/12/2015'
},
{
  action: 'Published Content',
  content: 'What Now > Radiological Emergency',
  society: 'New Zealand',
  date: '10/12/2015'
},
{
  action: 'Submitted Content',
  content: 'What Now > Radiological Emergency',
  society: 'UAE',
  date: '12/11/2015'
}
]
const fields = [{ key: 'action', sortable: true, tdClass: 'align-middle' },
                { key: 'content', sortable: true, tdClass: 'align-middle' },
                { key: 'society', sortable: true, tdClass: 'align-middle' },
                { key: 'date', sortable: true, tdClass: 'align-middle' },
  'actions']

export default {
  props: {
    id: {
      type: [String, Number],
      default: null
    },
    createNew: {
      type: Boolean,
      default: false
    },
    profile: {
      type: Boolean,
      default: false
    },
    isApiUser: {
      type: Boolean,
      default: false
    }
  },
  components: {
    AuditLog,
    SelectSociety,
    Avatar,
    UpdatePassword,
    PageBanner
  },
  data () {
    return {
      permissions: permissionsList,
      roleId: null,
      roleOptionText: null,
      user: {
        first_name: null,
        last_name: null,
        email: '',
        country_code: 'US',
        // organisation: '',
        // industry_type: '',
        role_id: null,
        photo_url: '',
        id: null,
        activated: null,
        organisations: [],
        permissions: [],
        api_used_in: ''
      },
      societies: {
        selectedSoc: null,
        isAddSocVisible: false,
        limitNumber: 3,
        showAll: false
      },
      activity: {
        items: items,
        fields: fields
      },
      saving: false,
      updating: false,
      update: false,
      reset: false,
      loading: false,
      emailDisabled: true,
      userPassword: '',
      sendingActivation: false
    }
  },
  metaInfo () {
    return {
      title: this.$t('users.list.manage')
    }
  },
  watch: {
    roleId (id) {
      this.updateRoleText(id)
      this.user.role_id = id
      this.user.role_name = this.roleOptions.find(role => role.id === id).name
    }
  },
  mounted () {
    this.$store.dispatch('users/fetchRoles')
    if (!this.newUser) {
      this.fetchUser()
    }
    this.fetchOrganisations()
  },
  methods: {
    async formSubmit (evt) {
      evt.preventDefault()
      if (this.newUser) {
        this.createUser(evt)
      } else if (this.update) {
        this.patchUser()
      }
    },
    async createUser (evt) {
      this.saving = true
      try {
        const newUser = {
          ...this.user
        }

        if (newUser.role_name) {
          newUser.api_used_in = newUser.role_name
        }

        await this.$store.dispatch('users/createUser', newUser)

        if (this.formErrorsCheck) {
          evt.target.reset()
          this.user.first_name = ''
          this.user.last_name = ''
          this.user.organisations = []
          this.$noty.success(this.$t('users.edit.created'))
          this.$fireGTEvent(this.$gtagEvents.CreateContentUser)
          if (!this.reset) {
            // Redirect back to user list.
            this.goToUserList()
          }
        }
        this.saving = false
      } catch (err) {
        this.saving = false
        if (e.response.status === 403) {
          this.$noty.error(this.$t('error_permission_alert_text'))
        } else {
          this.$noty.error(this.$t('error_alert_text'))
        }
      }
    },
    async fetchOrganisations () {
      await this.$store.dispatch('content/fetchOrganisations')
    },
    async patchUser () {
      this.updating = true
      try {
        const changes = {
          first_name: this.user.first_name,
          last_name: this.user.last_name,
          organisations: this.user.organisations
        }

        if (this.user.api_used_in && this.user.api_used_in.length > 0) {
          changes.api_used_in = this.user.api_used_in
        }

        if (!this.isMe) {
          changes.role_id = this.user.role_id
        }

        if (!this.emailDisabled) {
          changes.password = this.userPassword
          changes.email = this.user.email
        }
        if (this.$refs.uploadPasswordRef) {
          this.$refs.uploadPasswordRef.triggerSave()
        }
        await this.$store.dispatch('users/patchUser', { id: this.userId, changes })

        if (this.formErrorsCheck) {
          this.$noty.success(this.$t('users.edit.updated'))
          this.$fireGTEvent(this.$gtagEvents.CreateContentUser)

          if (this.can(this.authUser, this.permissions.USERS_EDIT)) {
            this.goToUserList()
          }
        }
        this.updating = false
        this.update = false
      } catch (e) {
        this.saving = false
        if (e.response && e.response.status === 403) {
          this.$noty.error(this.$t('error_permission_alert_text'))
        } else {
          this.$noty.error(this.$t('error_alert_text'))
        }
      }
    },
    async fetchUser () {
      this.loading = true
      await this.$store.dispatch('users/fetchEditUser', this.userId)
      this.loading = false
      const user = this.$store.getters['users/userEdit']
      this.roleId = user.role.id
      this.user = {
        first_name: user.user_profile.first_name,
        last_name: user.user_profile.last_name,
        email: user.email,
        country_code: user.user_profile.country_code,
        // organisation: '',
        // industry_type: '',
        role_id: user.role.id,
        is_super: user.role.super,
        photo_url: user.user_profile.photo_url,
        activated: user.activated,
        status: user.status,
        id: user.id,
        organisations: user.organisations,
        permissions: user.role.permissions,
        api_used_in: user.user_profile.api_used_in
      }
    },
    async resendActivation () {
      if (!this.newUser) {
        this.sendingActivation = true
        try {
          await axios.get(`/api/users/${this.userId}/resend`)
          this.$noty.success(this.$t('common.email_sent'))
        } catch (e) {
          this.$noty.error(this.$t('error_alert_text'))
        }
        this.sendingActivation = false
      }
    },
    goToUserList () {
      this.$router.push({ name: this.isApiUser ? 'api-usage.api-users' : 'users.list' })
    },
    toggleDeactivate (item) {
      const action = item.activated ? this.$t('common.deactivate') : this.$t('common.activate')
      const apiUrl = item.activated ? 'deactivate' : 'reactivate'
      swal({
        title: this.$t('common.are_you_sure'),
        text: `${action} ${item.first_name} ${item.last_name}`,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: action
      }).then((result) => {
        item.activated = !item.activated
        axios.post(`/api/users/${item.id}/${apiUrl}`)
      }).catch(swal.noop)
    },
    removeSociety (index) {
      this.user.organisations.splice(index, 1)
    },
    addSociety (val) {
      // todo: check that society isn't already in the list + show visual feedback
      const index = this.user.organisations.findIndex((code) => code === val.countryCode)
      if (index === -1) {
        this.user.organisations.push(val.countryCode)
        this.societies.showAll = true
      } else {
        swal({
          type: 'warning',
          title: this.$t('error_alert_title'),
          text: this.$t('users.edit.soc_already_added')
        })
      }
    },
    updateRoleText (id) {
      this.roleOptions.forEach(role => {
        if (role.id === id) {
          this.roleOptionText = role.permissions
        }
      })
    },
    userHasPermission (permissionName) {
      return !!this.user.permissions.find(permission => permission.name === permissionName)
    }
  },
  computed: {
    userId () {
      return parseInt(this.id, 10) || this.authUser.data.id
    },
    newUser () {
      return this.createNew
    },
    limitedSocieties () {
      return this.societies.societies.slice(0, this.societies.limitNumber)
    },
    isMe () {
      if (!this.newUser) {
        return this.userId === this.authUser.data.id
      }
      return false
    },
    userSocieties () {
      return this.user.organisations.map(code => this.organisations.find(soc => soc.countryCode === code))
    },
    ...mapGetters({
      roleOptions: 'users/roles',
      formErrors: 'users/formErrors',
      formErrorsCheck: 'users/formErrorsCheck',
      authUser: 'auth/user',
      organisations: 'content/organisations'
    }),
    filteredRoles () {
      const filteredRoleArray = this.roleOptions
      filteredRoleArray.forEach(function (role, index) {
        if (role.name === 'API User') {
          filteredRoleArray.splice(index, 1)
        }
      })
      return filteredRoleArray
    }
  }
}
</script>
<style>
.inputs-form {
  background: #E9E9E9;
  border: none;
  border-radius: 10px;
}

.times-btn {
  border: none;
  background: transparent;
  color: #E9E9E9;
  color: dimgrey;
  font-size: 1rem;
  padding: 0;
}
.times-btn:hover {
  background: transparent;
  color: dimgrey;
}
.cancel-btn {
  border: 3px solid black;
  padding: 0.2rem 0.3rem;
  font-size: 1rem;
}

.times-container {
  background: transparent;
}

.times-card {
  padding: 1rem;
  border-radius: 10px;
  background: #E9E9E9;
}
</style>
