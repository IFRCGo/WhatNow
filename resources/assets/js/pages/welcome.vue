<template>
  <div>
    <b-row class="head-component">
      <b-col>
        <div>
          <img class="head-img" :src="src('landingHead')">
          <div class="top-head">
            <h2 class="white-subtitle">{{ $t('landing.head_1') }}</h2>
            <h2 class="white-subtitle">{{ $t('landing.head_2') }}</h2>
            <p class="white-text mt-4">{{ $t('landing.head_text_1') }}</p>
            <p class="white-text">{{ $t('landing.head_text_2') }}</p>
            <div class="bottom-head">
              <p class="bottom-head-text">
                {{ $t('landing.head_bottom_1') }}
              </p>
              <p class="bottom-head-text">
                {{ $t('landing.head_bottom_2') }}
              </p>
              <p class="bottom-head-text">
                {{ $t('landing.head_bottom_3') }}
              </p>
              <b-button class="btn-primary mt-4" @click="$router.push('/register')">
                {{ $t('landing.signup') }}
              </b-button>
            </div>
          </div>
        </div>
      </b-col>
      <b-col>
        <img class="map-img" :src="src('mapImg')">
      </b-col>
    </b-row>
    <h2 class="mid-subt">{{ $t('landing.it_works') }}</h2>
    <b-row>
      <b-col>
        <div class="black-band">
          <div class="line-container">
            <div class="line-item-container">
              <div class="line-item mb-5">
                <p>
                  {{ $t('landing.works_1_a') }}
                </p>
                <p>
                  {{ $t('landing.works_1_b') }}
                </p>
                <p>
                  {{ $t('landing.works_1_c') }}
                </p>
              </div>
              <div class="red-line-frag left-radius">
                <div class="red-pointer-top"></div>
              </div>
              <div class="number-line-item mb-5">
                <div>
                  <img class="works-img" :src="src('works1')">
                </div>
              </div>
            </div>
            <div class="line-item-container">
              <div class="number-line-item mb-5">
                <img class="works-img" :src="src('works2')">
              </div>
              <div class="red-line-frag">
                <div class="red-pointer-bottom"></div>
              </div>
              <div class="line-item mb-5 pt-5">
                <p>
                  {{ $t('landing.works_2_a') }}
                </p>
                <p>
                  {{ $t('landing.works_2_b') }}
                </p>
                <p>
                  {{ $t('landing.works_2_c') }}
                </p>
              </div>
            </div>
            <div class="line-item-container">
              <div class="line-item mb-5">
                <p>
                  {{ $t('landing.works_3_a') }}
                </p>
                <p>
                  {{ $t('landing.works_3_b') }}
                </p>
                <p>
                  {{ $t('landing.works_3_c') }}
                </p>
              </div>
              <div class="red-line-frag">
                <div class="red-pointer-top"></div>
              </div>
              <div class="number-line-item mb-5">
                <img class="works-img" :src="src('works3')">
              </div>
            </div>
            <div class="line-item-container">
              <div class="number-line-item mb-5">
                <img class="works-img" :src="src('works4')">
              </div>
              <div class="red-line-frag">
                <div class="red-pointer-bottom"></div>
              </div>
              <div class="line-item mb-5 pt-5">
                <p>
                  {{ $t('landing.works_4_a') }}
                </p>
                <p>
                  {{ $t('landing.works_4_b') }}
                </p>
                <p>
                  {{ $t('landing.works_4_c') }}
                </p>
              </div>
            </div>
            <div class="line-item-container">
              <div class="line-item mb-5">
                <p>
                  {{ $t('landing.works_5_a') }}
                </p>
                <p>
                  {{ $t('landing.works_5_b') }}
                </p>
                <p>
                  {{ $t('landing.works_5_c') }}
                </p>
              </div>
              <div class="red-line-frag right-radius">
                <div class="red-pointer-top"></div>
              </div>
              <div class="number-line-item mb-5">
                <img class="works-img" :src="src('works5')">
              </div>
            </div>
          </div>
          <div class="line-vertical-container">
            <b-row class="vertical-item-container">
              <b-col class="center">
                <img class="works-img" :src="src('works1')">
              </b-col>
              <b-col class="center">
                <div class="vertical-red-frag top-radius"></div>
                <div class="red-pointer-right"></div>
              </b-col>
              <b-col class="center">
                <div class="line-item">
                  <p>
                    {{ $t('landing.works_1_a') }}
                  </p>
                  <p>
                    {{ $t('landing.works_1_b') }}
                  </p>
                  <p>
                    {{ $t('landing.works_1_c') }}
                  </p>
                </div>
              </b-col>
            </b-row>
            <b-row class="vertical-item-container">
              <b-col class="center">
                <div class="line-item">
                  <p>
                    {{ $t('landing.works_2_a') }}
                  </p>
                  <p>
                    {{ $t('landing.works_2_b') }}
                  </p>
                  <p>
                    {{ $t('landing.works_2_c') }}
                  </p>
                </div>
              </b-col>
              <b-col class="center">
                <div class="vertical-red-frag"></div>
                <div class="red-pointer-left"></div>
              </b-col>
              <b-col class="center">
                <img class="works-img" :src="src('works2')">
              </b-col>
            </b-row>
            <b-row class="vertical-item-container">
              <b-col class="center">
                <img class="works-img" :src="src('works3')">
              </b-col>
              <b-col class="center">
                <div class="vertical-red-frag">
                  <div class="red-pointer-right"></div>
                </div>
              </b-col>
              <b-col class="center">
                <div class="line-item">
                  <p>
                    {{ $t('landing.works_3_a') }}
                  </p>
                  <p>
                    {{ $t('landing.works_3_b') }}
                  </p>
                  <p>
                    {{ $t('landing.works_3_c') }}
                  </p>
                </div>
              </b-col>
            </b-row>
            <b-row class="vertical-item-container">
              <b-col class="center">
                <div class="line-item">
                  <p>
                    {{ $t('landing.works_4_a') }}
                  </p>
                  <p>
                    {{ $t('landing.works_4_b') }}
                  </p>
                  <p>
                    {{ $t('landing.works_4_c') }}
                  </p>
                </div>
              </b-col>
              <b-col class="center">
                <div class="vertical-red-frag"></div>
                <div class="red-pointer-left"></div>
              </b-col>
              <b-col class="center">
                <img class="works-img" :src="src('works4')">
              </b-col>
            </b-row>
            <b-row class="vertical-item-container">
              <b-col class="center">
                <img class="works-img" :src="src('works5')">
              </b-col>
              <b-col class="center">
                <div class="vertical-red-frag bottom-radius">
                  <div class="red-pointer-right"></div>
                </div>
              </b-col>
              <b-col class="center">
                <div class="line-item">
                  <p>
                    {{ $t('landing.works_5_a') }}
                  </p>
                  <p>
                    {{ $t('landing.works_5_b') }}
                  </p>
                  <p>
                    {{ $t('landing.works_5_c') }}
                  </p>
                </div>
              </b-col>
            </b-row>
          </div>
          <div class="btns-container">
            <b-button class="btn-primary mr-2">
              {{ $t('landing.learn_more') }}
            </b-button>
            <b-button class="btn-primary ml-2">
              {{ $t('landing.watch_video') }}
            </b-button>
          </div>
        </div>
      </b-col>
    </b-row>
    <h2 class="mid-subt">{{ $t('landing.example_case') }}</h2>
    <b-row class="usecases">
      <b-col class="card-use-container">
        <div class="example-card">
          <h6 class="card-subt">
            {{ $t('landing.api') }}
          </h6>
          <p class="card-txt">
            {{ $t('landing.hurricane') }}
          </p>
          <p class="card-txt">
            {{ $t('landing.warning_alerts') }}
          </p>
          <img class="map-img" src="cardImage1">
        </div>
      </b-col>
      <b-col class="card-use-container">
        <div class="example-card">
          <h6 class="card-subt">
            {{ $t('landing.social_media') }}
          </h6>
          <p class="card-txt">
            {{ $t('landing.post_images') }}
          </p>
          <p class="card-txt">
            {{ $t('landing.messages_network') }}
          </p>
          <img class="map-img" src="cardImage3">
        </div>
      </b-col>
      <b-col class="card-use-container">
        <div class="example-card">
          <h6 class="card-subt">
            {{ $t('landing.whatsapp') }}
          </h6>
          <p class="card-txt">
            {{ $t('landing.share_flood') }}
          </p>
          <p class="card-txt">
            {{ $t('landing.family') }}
          </p>
          <img class="map-img" src="cardImage3">
        </div>
      </b-col>
    </b-row>
    <h2 class="mid-subt">{{ $t('landing.impact') }}</h2>
    <b-row>
      <b-col class="red-section">
        <i class="fas fa-user-circle impact-icon"></i>
        <p class="red-sec-txt">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut elit eget enim semper maximus vitae
          placerat
        </p>
        <p class="red-sec-txt">
          nulla. Pellentesque vitae metus nunc. Donec dolor elit, gravida vel lectus in, sollicitudin commodo dolor.
          Cras
        </p>
        <p class="red-sec-txt">
          aliquet interdum sapien at tempor. Integer viverra vulputate posuere. Aliquam tincidunt turpis a mi malesuada.
        </p>
        <div class="div-white"></div>
        <i class="fas fa-user-circle impact-icon"></i>
        <p class="red-sec-txt">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer ut elit eget enim semper maximus vitae
          placerat
        </p>
        <p class="red-sec-txt">
          nulla. Pellentesque vitae metus nunc. Donec dolor elit, gravida vel lectus in, sollicitudin commodo dolor.
          Cras
        </p>
        <p class="red-sec-txt">
          aliquet interdum sapien at tempor. Integer viverra vulputate posuere. Aliquam tincidunt turpis a mi malesuada.
        </p>

      </b-col>
    </b-row>
    <div class="btn-bottom-container">
      <b-button class="btn-primary btn-bottom" @click="$router.push('/register')">Sign up today</b-button>
    </div>
  </div>
</template>
<script>
</script>
<style scoped>
.top-head {
  position: relative;
  left: 3rem;
  bottom: 20rem;
}

.white-subtitle {
  color: white;
  font-size: 3rem;
}

.white-text {
  color: white;
  line-height: 1rem;
  font-size: 1.2rem;
}

.center {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.head-img {
  width: 100%;
}

.map-img {
  width: 100%;
}

.bottom-head {
  display: flex;
  flex-direction: column;
  width: 100%;
  justify-content: center;
  align-items: flex-start;
  padding-top: 10rem;
  padding-left: 6rem;
}

.bottom-head-text {
  font-size: 1.2rem;
  line-height: 1rem;
}

.mid-subt {
  margin-top: 4rem;
  text-align: center;
  font-size: 3rem;
}

.black-band {
  margin-top: 3rem;
  width: 100%;
  height: 45rem;
  background: black;
  color: white;
  padding: 4rem;
}

.line-item {
  font-size: 1rem;
  line-height: 1rem;
  text-align: center;
  height: 5rem;
}

.works-img {
  width: 5rem;
  height: 5rem;
}

.red-line-frag {
  margin-top: 4rem;
  margin-bottom: 4rem;
  height: 3rem;
  width: 100%;
  background: #F6333F;
  display: flex;
  align-items: center;
  justify-content: space-around;
}

.vertical-red-frag {
  margin-left: 4rem;
  margin-right: 4rem;
  height: 12rem;
  width: 3rem;
  background: #F6333F;
  display: flex;
  align-items: center;
  justify-content: space-around;
}

.line-container {
  width: 100%;
  display: flex;
  justify-content: space-around;
  align-items: center;
}

.line-vertical-container{
  display: none;
}

.line-item-container {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.vertical-item-container {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}


.red-pointer-top {
  background: #F6333F;
  height: 5rem;
  width: 2rem;
  border-radius: 100px;
  margin-bottom: 3rem;
}

.red-pointer-bottom {
  background: #F6333F;
  height: 5rem;
  width: 2rem;
  border-radius: 100px;
  margin-top: 3rem;
}

.red-pointer-right {
  background: #F6333F;
  height: 2rem;
  width: 5rem;
  border-radius: 100px;
  margin-left: 3rem;
  position: absolute;
}

.red-pointer-left {
  background: #F6333F;
  height: 2rem;
  width: 5rem;
  border-radius: 100px;
  margin-right: 3rem;
  position: absolute;
}


.left-radius {
  border-bottom-left-radius: 100px;
  border-top-left-radius: 100px;
}

.right-radius {
  border-bottom-right-radius: 100px;
  border-top-right-radius: 100px;
}

.top-radius {
  border-top-left-radius: 100px;
  border-top-right-radius: 100px;
}

.bottom-radius {
  border-bottom-left-radius: 100px;
  border-bottom-right-radius: 100px;
}

.btns-container {
  margin-top: 6rem;
  display: flex;
  align-items: center;
  justify-content: center;

  button {
    font-size: 1.3rem;
  }
}

.usecases {
  background: #FAFAFA;
  padding: 3rem 10rem;
}

.card-use-container {
  display: flex;
  justify-content: center;
}

.example-card {
  background: #EFEFEF;
  border-radius: 20px;
  width: 80%;
  padding: 2rem;
}

.card-subt {
  color: #F6333F;
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.card-txt {
  font-size: 1.2rem;
  line-height: 1.2rem;
}

.red-section {
  margin-top: 2rem;
  background: #FB444F;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 4rem;
}

.impact-icon {
  background: #FB444F;
  color: white;
  font-size: 10rem;
  margin-bottom: 2rem;
}

.div-white {
  background: white;
  height: 2px;
  width: 30%;
  margin-top: 3rem;
  margin-bottom: 3rem;
}

.red-sec-txt {
  color: white;
  font-size: 1.2rem;
}

.btn-bottom-container {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.btn-bottom {
  margin-top: 6rem;
  margin-bottom: 6rem;
  font-size: 3rem;
  border-radius: 50px;
}

@media screen and (max-width: 1300px) {
  .usecases {
    padding: 3rem 1rem;
  }

  .example-card {
    width: 95%;
    padding: 1rem;
  }
}

@media screen and (max-width: 1110px) {
  .head-component {
    flex-direction: column;
  }

  .top-head {
    position: relative;
    left: 10rem;
    bottom: 20rem;
  }

  .head-img {
    width: 80%;
  }

  .map-img {
    margin-top: -10rem;
  }

}

@media screen and (max-width: 1050px) {
  .line-container {
    display: none;
  }

  .line-vertical-container {
    display: block;
  }

  .black-band {
    height: 80rem;
  }

}

@media screen and (max-width: 800px) {
  .head-component {
    flex-direction: column;
  }

  .usecases {
    flex-direction: column;
  }

  .example-card {
    margin: 1rem
  }

  .top-head {
    position: relative;
    left: 0rem;
    bottom: 20rem;
    padding: 1rem;
  }

  .head-img {
    width: 100%;
  }

  .map-img {
    margin-top: -10rem;
  }

  .bottom-head {
    padding-left: 1rem;
  }
}
@media screen and (max-width: 650px) {

  .red-pointer-right {
    display: none;
  }

  .red-pointer-left {
    display: none;
  }

  .vertical-red-frag {
    display: none;
  }

  .vertical-item-container {
    height: 12rem;
  }
}

@media screen and (max-width: 550px) {
  .black-band {
    height: 92rem;
  }

  .vertical-item-container {
    height: 15rem;
  }
}
</style>

