<template>
  <div>
    <navbar></navbar>
    <b-row class="w-100 mt-5">
      <div class="wel-card mx-auto p-4">
        <h2 class="text-title">
          {{ $t('new_welcome.title_1') }} <span class="red-title">{{ $t('new_welcome.red_title') }}</span>
          {{ $t('new_welcome.title_2') }}
          <br>
          {{ $t('new_welcome.title_3') }}
        </h2>
        <p class="text-header mt-3">
          {{ $t('new_welcome.text_header') }}
        </p>
        <p class="text-header">
          {{ $t('new_welcome.text_header_2') }}
        </p>
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
            <b-button
              v-for="(slide, index) in slides"
              :key="index"
              :class="{ active: currentIndex === index }"
              @click="goToSlide(index)"
              class="step-button"
            >
            </b-button>
          </div>
        <div>
          <b-button variant="danger" class="button-go mt-5">
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
            <b-button
              v-for="(slide, index) in slides2"
              :key="index"
              :class="{ active: currentIndex2 === index }"
              @click="goToSlide2(index)"
              class="step-button"
            >
            </b-button>
          </div>
          <div>
            <b-button variant="danger" class="button-go mt-5">
              {{ $t('new_welcome.slider_2.btn') }}
            </b-button>
          </div>
        </div>
      </div>
    </b-row>
    <b-row class="w-100 mt-3">
      <div class="wel-card mx-auto p-4">
        <h3 class="btm-subt">
          {{ $t('new_welcome.bottom_subtitle') }}
        </h3>
        <p class="btm-text">
          {{ $t('new_welcome.bottom_text') }}
        </p>
        <b-button variant="danger" class="button-go mt-3">
          {{ $t('new_welcome.bottom_btn') }}
        </b-button>
      </div>
    </b-row>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Navbar from '~/components/Navbar'

export default {
  layout: 'default',

  metaInfo () {
    return { title: this.$t('home.home') }
  },

  computed: mapGetters({
    authenticated: 'auth/check',
    user: 'auth/user'
  }),

  data () {
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
    }
  },
  components: {
    Navbar
  },
  methods: {
    nextSlide () {
      this.currentIndex = (this.currentIndex + 1) % this.slides.length
    },
    prevSlide () {
      this.currentIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length
    },
    startAutoSlide () {
      this.interval = setInterval(this.nextSlide, 4000)
    },
    stopAutoSlide () {
      clearInterval(this.interval)
    },
    goToSlide (index) {
      this.currentIndex = index
    },
    nextSlide2 () {
      this.currentIndex2 = (this.currentIndex2 + 1) % this.slides2.length
    },
    prevSlide2 () {
      this.currentIndex2 = (this.currentIndex2 - 1 + this.slides2.length) % this.slides2.length
    },
    startAutoSlide2 () {
      this.interval2 = setInterval(this.nextSlide2, 4000)
    },
    stopAutoSlide2 () {
      clearInterval(this.interval2)
    },
    goToSlide2 (index) {
      this.currentIndex2 = index
    }
  },
  mounted () {
    this.startAutoSlide()
    this.startAutoSlide2()
  },

  beforeDestroy () {
    this.stopAutoSlide()
    this.stopAutoSlide2()
  }
}
</script>
<style scoped>
.wel-card {
  width: 40%;
  background: #F7F7F7;
  border-radius: 20px;
  text-align: center;
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
}


.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to {
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
