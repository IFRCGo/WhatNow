<template>
  <div>
    <navbar></navbar>
    <b-row class="w-100 mt-5">
      <div class="wel-card mx-auto p-4">
        <h2 class="text-title">
          {{ $t('new_welcome.title_1') }} <span class="red-title">{{ $t('new_welcome.red_title') }}</span>
          <br>
          {{ $t('new_welcome.title_2') }}
          {{ $t('new_welcome.title_3') }}
        </h2>
        <p class="text-header mt-5">
          {{ $t('new_welcome.text_header') }}
        </p>
        <p class="text-header">
          {{ $t('new_welcome.text_header_2') }}
        </p>

        <div class="text-center mt-4">
          <b-form-checkbox v-model="dontShowAgain" class="mb-2">
            {{ $t('new_welcome.skip_checkbox') }}
          </b-form-checkbox>
          <b-button variant="outline-primary" class="button-go" @click="skipIntro">
            {{ $t('new_welcome.skip_btn') }}
          </b-button>
        </div>
      </div>
    </b-row>
    <b-row class="w-100 mt-3">
      <div class="wel-card mx-auto p-4">
        <div class="manual-carousel">
          <transition name="fade" mode="out-in">
            <div :key="currentIndex">
              <b-row class="w-100 d-flex justify-content-between align-items-center flex-nowrap carousel-row">
                <b-button @click="prevSlide" class="slide-btn">‹</b-button>
                <div class="slide-content text-center mt-4">
                  <div class="slider-header">
                    <i :class="slides[currentIndex].icon"></i>
                    <h2>{{ slides[currentIndex].title }}</h2>
                  </div>
                  <hr>
                  <div class="slider-content">
                    <p>{{ slides[currentIndex].text }}</p>
                    <p v-if="currentIndex === 1" class="text-truncate">{{ slides[currentIndex].text2 }}</p>
                  </div>
                </div>
                <b-button @click="nextSlide" class="slide-btn">›</b-button>
              </b-row>
            </div>
          </transition>
        </div>
        <div class="bottom-elements pt-4">
          <div class="step-indicators">
            <b-button v-for="(slide, index) in slides" :key="index" :class="{ active: currentIndex === index }"
              @click="goToSlide(index)" class="step-button">
            </b-button>
          </div>
          <div>
            <b-button variant="danger" class="button-go mt-5" :to="{ name: 'applications.dash', params: { } }">
              {{ $t('new_welcome.slider_1.btn') }}
            </b-button>
          </div>
        </div>
      </div>
    </b-row>
    <b-row class="w-100 mt-3">
      <div class="wel-card mx-auto p-4">
        <div class="manual-carousel">
          <transition name="fade" mode="out-in">
            <div :key="currentIndex2">
              <b-row class="w-100 d-flex justify-content-between flex-nowrap align-items-center carousel-row">
                <b-button @click="prevSlide2" class="slide-btn">‹</b-button>
                <div class="slide-content text-center mt-4">
                  <div class="slider-header">
                    <i :class="slides2[currentIndex].icon"></i>
                    <h2>{{ slides2[currentIndex2].title }}</h2>
                  </div>
                  <hr>
                  <div class="slider-content">
                    <p>{{ slides2[currentIndex2].text }}</p>
                  </div>
                </div>
                <b-button @click="nextSlide2" class="slide-btn">›</b-button>
              </b-row>
            </div>
          </transition>
        </div>
        <div class="bottom-elements pt-4">
          <div class="step-indicators">
            <b-button v-for="(slide, index) in slides2" :key="index" :class="{ active: currentIndex2 === index }"
              @click="goToSlide2(index)" class="step-button">
            </b-button>
          </div>
          <div>
            <b-button variant="danger" class="button-go text-small mt-5" :to="{ name: 'view-whatnow' }">
              {{ $t('new_welcome.slider_2.btn') }}
            </b-button>
          </div>
        </div>
      </div>
    </b-row>
    <b-row class="w-100 mt-3 mb-5">
      <div class="wel-card mx-auto p-4">
        <h3 class="btm-subt">
          {{ $t('new_welcome.bottom_subtitle') }}
        </h3>
        <p class="btm-text">
          {{ $t('new_welcome.bottom_text') }}
        </p>
        <b-button variant="danger" class="button-go mt-3" @click="showModal">
          {{ $t('new_welcome.bottom_btn') }}
        </b-button>
      </div>
    </b-row>
    <b-modal ref="contactModal" hide-footer centered size="md" :title="$t('new_welcome.contact_modal.title')" dialog-class="custom-modal">
      <div>
        <h5 class="mb-4 contact-modal-label">{{ $t('new_welcome.contact_modal.question_1') }}</h5>
        <b-form-group>
          <div class="d-flex role-radio-group">
            <b-form-radio
              v-for="role in roleOptions"
              :key="role.value"
              v-model="form.selectedRole"
              :value="role.value"
              :class="['mr-4', 'contact-modal-radio',  form.errors.has('selectedRole') ? 'is-invalid' : '']"
            >
              {{ role.text }}
            </b-form-radio>
          </div>
          <has-error :form="form" field="selectedRole" class="pl-2 font-italic"></has-error>
        </b-form-group>

        <h5 class="mb-2 contact-modal-label">{{ $t('new_welcome.contact_modal.question_2') }}</h5>
        <div class="form-group register-input-container pr-lg-4 pr-md-4">
          <b-form-input
            v-model="form.nationalSociety"
            class="mb-4 form-control styled-input"
            :class="{ 'is-invalid': form.errors.has('nationalSociety') }"
          />
          <has-error :form="form" field="nationalSociety" class="pl-2 font-italic"></has-error>
        </div>

        <div class="text-right">
          <b-button variant="danger" size="sm" class="send-button" @click="submitRoleRequest">
            <i class="fas fa-share mr-2"></i> {{ $t('new_welcome.contact_modal.send') }}
          </b-button>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Navbar from '~/components/Navbar'
import Form from 'vform'

export default {
  layout: 'default',

  metaInfo() {
    return { title: this.$t('home.home') }
  },

  computed: mapGetters({
    authenticated: 'auth/check',
    user: 'auth/user'
  }),

  data() {
    return {
      title: window.config.appName,
      activeTab: 0,
      currentIndex: 0,
      activeTab2: 0,
      currentIndex2: 0,
      slides: [
        { title: this.$t('new_welcome.slider_1.step_1.header'), text: this.$t('new_welcome.slider_1.step_1.text'), icon: 'fas fa-th' },
        { title: this.$t('new_welcome.slider_1.step_2.header'), text: this.$t('new_welcome.slider_1.step_2.text'), text2: this.$t('new_welcome.slider_1.step_2.text_2'), icon: 'fas fa-shield-alt' },
        { title: this.$t('new_welcome.slider_1.step_3.header'), text: this.$t('new_welcome.slider_1.step_3.text'), icon: 'fas fa-comments' },
        { title: this.$t('new_welcome.slider_1.step_4.header'), text: this.$t('new_welcome.slider_1.step_4.text'), icon: 'fas fa-share' }
      ],
      interval: null,
      slides2: [
        { title: this.$t('new_welcome.slider_2.step_1.header'), text: this.$t('new_welcome.slider_2.step_1.text'), icon: 'fas fa-comment' },
        { title: this.$t('new_welcome.slider_2.step_2.header'), text: this.$t('new_welcome.slider_2.step_2.text'), icon: 'fas fa-plus' },
        { title: this.$t('new_welcome.slider_2.step_3.header'), text: this.$t('new_welcome.slider_2.step_3.text'), icon: 'fas fa-share' }
      ],
      interval2: null,
      roleOptions: [
        { text: 'National Society Editor', value: 'National Society Editor' },
        { text: 'National Society Admin', value: 'National Society Admin' }
      ],
      dontShowAgain: false,
      form: new Form({
        selectedRole: '',
        nationalSociety: ''
      }),
    }
  },
  components: {
    Navbar
  },
  methods: {
    nextSlide() {
      this.currentIndex = (this.currentIndex + 1) % this.slides.length
    },
    prevSlide() {
      this.currentIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length
    },
    startAutoSlide() {
      this.interval = setInterval(this.nextSlide, 10000)
    },
    stopAutoSlide() {
      clearInterval(this.interval)
    },
    goToSlide(index) {
      this.currentIndex = index
    },
    nextSlide2() {
      this.currentIndex2 = (this.currentIndex2 + 1) % this.slides2.length
    },
    prevSlide2() {
      this.currentIndex2 = (this.currentIndex2 - 1 + this.slides2.length) % this.slides2.length
    },
    startAutoSlide2() {
      this.interval2 = setInterval(this.nextSlide2, 10000)
    },
    stopAutoSlide2() {
      clearInterval(this.interval2)
    },
    goToSlide2(index) {
      this.currentIndex2 = index
    },
    showModal() {
      this.$refs.contactModal.show();
    },
    submitRoleRequest() {
      try {
        this.form.clear()
        const role = this.form.selectedRole
        const society = this.form.nationalSociety
        const errors = {}
        if (!this.form.selectedRole) {
          errors.selectedRole = this.$t('new_welcome.contact_modal.error_role')
        }

        if (!this.form.nationalSociety) {
          errors.nationalSociety = this.$t('new_welcome.contact_modal.error_society')
        }

        if (Object.keys(errors).length > 0) {
          this.form.errors.set(errors)
          return
        }
        const bodyStructure = this.$t('new_welcome.contact_modal.email_body_structure').replace('{role}', role).replace('{society}', society);
        const subject = encodeURIComponent(this.$t('new_welcome.contact_modal.email_subject'))
        const body = encodeURIComponent(bodyStructure)
        const mailtoLink = `mailto:im@ifrc.org?subject=${subject}&body=${body}`
        window.open(mailtoLink, '_blank');
        this.$refs.contactModal.hide()
        this.form.reset()
      } catch (error) {
        console.log('Error al enviar el correo:', error)
      }
    },
    async skipIntro() {
      if (this.dontShowAgain) {
        const changes = {
          confirmed_role: true
        }
        await this.$store.dispatch('auth/patchUser', { id: this.user.data.id, changes })
      }
      this.$router.push({ name: 'applications.dash' })
    }
  },
  mounted() {
    this.startAutoSlide()
    this.startAutoSlide2()
  },
  beforeDestroy() {
    this.stopAutoSlide()
    this.stopAutoSlide2()
  },
  computed: {
    ...mapGetters({
      user: 'auth/user'
    })
  }
}
</script>
<style scoped>
.wel-card {
  width: 90%;
  max-width: 950px;
  background: #F7F7F7;
  border-radius: 20px;
  text-align: center;
}

.wel-card .button-go {
  font-weight: 500;
  font-size: 1rem;
}

.slide-card-container {
  display: flex;
  justify-content: center;
  align-items: center;
}

.slide-card {
  border-radius: 10px;
  border: 1px solid #CECECE;
}

.manual-carousel {
  position: relative;
}

.carousel-slide {
  min-height: 150px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.slide-content {
  padding: 20px 0px;
  border-radius: 10px;
  border: 1px solid #CECECE;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
  width: 80%;
  max-width: 700px;
}


.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}

.slide-btn {
  background: transparent;
  color: #676767;
  font-weight: 400;
  font-size: 3rem;
}

.bottom-elements {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
}

.carousel-row {
  margin: 0;
}

.slider-header {
  display: flex;
  padding-left: 1rem;
  padding-right: 1rem;
  justify-content: flex-start;
  align-items: center;

  svg {
    font-size: 2rem;
    color: #f6333f;
    margin-right: 2rem;
  }

  h2 {
    font-size: 2rem;
  }
}


.slider-content {
  padding: 1rem 1rem 3rem 1rem;

  p {
    font-size: 1rem;
  }
}

.step-button {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  border: 2px solid #ccc;
  background-color: transparent;
  transition: background-color 0.3s, border-color 0.3s;
  cursor: pointer;
  outline: none;
  padding: 0;
  margin: 0px 5px;
}

.step-button.active {
  background-color: #f6333f;
  border-color: #f6333f;
}

.red-title {
  color: #f6333f;
}

.text-title {
  font-size: 3.5rem;
}

.text-header {
  font-size: 1rem;
}

.btm-subt {
  font-size: 2rem;
  font-weight: 600;
}

.btm-text {
  font-size: 1rem;
}
.contact-modal {
   max-width: 600px;
}
.custom-modal .modal-content {
  border-radius: 15px;
  padding: 1.5rem;
}
.custom-modal .modal-header {
  border-bottom: 1px solid #e0e0e0;
}

.send-button {
  border-radius: 30px;
  font-weight: 600;
  padding: 0.5rem 1.5rem;
  font-size: .7rem;
}

.contact-modal-label {
  font-size: 1rem;
  font-weight: 400;
  color: #000;
}

::v-deep .contact-modal-radio .custom-control-input:checked ~ .custom-control-label::before {
  background-color: #f6333f;
  border-color: #f6333f;
}

@media (max-width: 1270px) {
  .wel-card {
    width: 60%;
  }
}

@media (max-width: 834px) {
  .wel-card {
    width: 80%;
  }
}

@media (max-width: 639px) {
  .wel-card {
    width: 95%;
  }
}
</style>
