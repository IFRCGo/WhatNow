<template>
  <div>
    <b-row class="head-component">
      <b-col>
        <div>
          <img class="head-img" :src="src('landingHead')">
          <div class="top-head">
            <h2 class="white-subtitle">{{ $t('landing.head_1') }}</h2>
            <h2 class="white-subtitle">{{ $t('landing.head_2') }}</h2>
            <p class="white-text mt-4 pre-line line-height-50">{{ $t('landing.head_text') }}</p>
            <div class="bottom-head">
              <p class="bottom-head-text pre-line">
                {{ $t('landing.head_bottom') }}
              </p>
              <b-button class="btn-primary s-up-btn mt-4" @click="$router.push('/register')">
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
  <div class="it-works">
    <h2 class="mid-subt">{{ $t('landing.it_works') }}</h2>
  </div>
    <b-row>
      <div class="black-band-wrapper">
        <div class="black-band">
          <div class="line-container">
            <div class="line-item-container">
              <div class="line-item ">
                <div>
                  <img class="works-img" :src="src('works1')">
                </div>
                <div>
                  <p>
                    {{ $t('landing.works_1') }}
                  </p>
                </div>
              </div>
              <div class="red-line-frag left-radius">
                <div class="red-pointer-top"></div>
              </div>
              <div></div>
            </div>
            <div class="line-item-container">
              <div></div>
              <div class="red-line-frag">
                <div class="red-pointer-bottom"></div>
              </div>
              <div class="line-item ">
                <div class="number-line-item ">
                  <img class="works-img" :src="src('works2')">
                </div>
                <div>
                  <p>
                    {{ $t('landing.works_2') }}
                  </p>
                </div>
              </div>
            </div>
            <div class="line-item-container">
              <div class="line-item ">
                <div class="number-line-item ">
                  <img class="works-img" :src="src('works3')">
                </div>
                <div>
                  <p>
                    {{ $t('landing.works_3') }}
                  </p>
                </div>

              </div>
              <div class="red-line-frag">
                <div class="red-pointer-top"></div>
              </div>
              <div></div>

            </div>
            <div class="line-item-container">
              <div></div>
              <div class="red-line-frag">
                <div class="red-pointer-bottom"></div>
              </div>
              <div class="line-item ">
                <div class="number-line-item ">
                  <img class="works-img" :src="src('works4')">
                </div>
                <div>
                  <p>
                    {{ $t('landing.works_4') }}
                  </p>
                </div>
              </div>
            </div>
            <div class="line-item-container">
              <div class="line-item ">
                <div class="number-line-item ">
                  <img class="works-img" :src="src('works5')">
                </div>
                <div>
                  <p>
                    {{ $t('landing.works_5') }}
                  </p>
                </div>

              </div>
              <div class="red-line-frag right-radius">
                <div class="red-pointer-top"></div>
              </div>
              <div></div>

            </div>
          </div>
          <div class="line-vertical-container">
            <b-row class="vertical-item-container">
              <b-col class="center">

              </b-col>
              <b-col class="center">
                <div class="vertical-red-frag top-radius"></div>
                <div class="red-pointer-right"></div>
              </b-col>
              <b-col class="center">
                <div class="line-item">
                  <div>
                    <img class="works-img" :src="src('works1')">
                  </div>
                  <div>
                    <p>
                      {{ $t('landing.works_1') }}
                    </p>
                  </div>
                </div>
              </b-col>
            </b-row>
            <b-row class="vertical-item-container">
              <b-col class="center">
                <div class="line-item">
                  <div>
                    <img class="works-img" :src="src('works2')">
                  </div>
                  <div>
                    <p>
                      {{ $t('landing.works_2') }}
                    </p>
                  </div>
                </div>
              </b-col>
              <b-col class="center">
                <div class="vertical-red-frag"></div>
                <div class="red-pointer-left"></div>
              </b-col>
              <b-col class="center">

              </b-col>
            </b-row>
            <b-row class="vertical-item-container">
              <b-col class="center">
              </b-col>
              <b-col class="center">
                <div class="vertical-red-frag">
                  <div class="red-pointer-right"></div>
                </div>
              </b-col>
              <b-col class="center">
                <div class="line-item">
                  <div>
                    <img class="works-img" :src="src('works3')">
                  </div>
                  <div>
                    <p>
                      {{ $t('landing.works_3') }}
                    </p>
                  </div>
                </div>
              </b-col>
            </b-row>
            <b-row class="vertical-item-container">
              <b-col class="center">
                <div class="line-item">
                  <div>
                    <img class="works-img" :src="src('works4')">

                  </div>
                <div>
                  <p>
                    {{ $t('landing.works_4') }}
                  </p>
                </div>
                </div>
              </b-col>
              <b-col class="center">
                <div class="vertical-red-frag"></div>
                <div class="red-pointer-left"></div>
              </b-col>
              <b-col class="center">
              </b-col>
            </b-row>
            <b-row class="vertical-item-container">
              <b-col class="center">
              </b-col>
              <b-col class="center">
                <div class="vertical-red-frag bottom-radius">
                  <div class="red-pointer-right"></div>
                </div>
              </b-col>
              <b-col class="center">
                <div class="line-item">
                  <div>
                    <img class="works-img" :src="src('works5')">
                  </div>
                  <div>
                    <p>
                      {{ $t('landing.works_5') }}
                    </p>
                  </div>
                </div>
              </b-col>
            </b-row>
          </div>
          <div class="btns-container">
            <b-button class="btn-primary mr-2"  @click="goToLink('https://preparecenter.org/activity/whatnow-service/')">
              {{ $t('landing.learn_more') }}
            </b-button>
            <b-button class="btn-primary ml-2" @click="goToLink('https://www.youtube.com/watch?v=Tc0P1kX5xJA&embeds_referring_euri=https%3A%2F%2Fpreparecenter.org%2F')">
              {{ $t('landing.watch_video') }}
            </b-button>
          </div>
        </div>
      </div>
    </b-row>
    <h2 class="mid-subt">{{ $t('landing.example_case') }}</h2>
    <b-row class="usecases">
      <b-col class="card-use-container">
        <div class="example-card">
          <div>
            <h6 class="card-subt">
              {{ $t('landing.api') }}
            </h6>
            <p class="card-txt">
              {{ $t('landing.hurricane') }}
            </p>
          </div>
          <div class="example-card-img-container">
            <img :src="src('homeExampleCase1')">
          </div>
        </div>
      </b-col>
      <b-col class="card-use-container">
        <div class="example-card">
          <div>
            <h6 class="card-subt">
              {{ $t('landing.social_media') }}
            </h6>
            <p class="card-txt">
              {{ $t('landing.post_images') }}
            </p>
          </div>
          <div class="example-card-img-container">
            <img :src="src('homeExampleCase2')">
          </div>
        </div>
      </b-col>
      <b-col class="card-use-container">
        <div class="example-card">
          <div>
            <h6 class="card-subt">
              {{ $t('landing.whatsapp') }}
            </h6>
            <p class="card-txt">
              {{ $t('landing.share_flood') }}
            </p>
          </div>
          <div class="example-card-img-container">
            <img :src="src('homeExampleCase3')">
          </div>
        </div>
      </b-col>
    </b-row>
    <h2 class="mid-subt">{{ $t('landing.impact') }}</h2>
    <b-row>
      <b-col class="red-section">
        <div class="quote-author-img">
          <img class="head-img" :src="src('quoteAuthor1')">
        </div>
        <p class="red-sec-txt">
            {{ $t('landing.quote_description_1') }}
        </p>
        <p class="red-sec-txt">— {{ $t('landing.quote_author_1') }}</p>
        <div class="div-white"></div>
        <div class="quote-author-img quote-author-img-2">
          <img class="head-img" :src="src('quoteAuthor2')">
        </div>
        <p class="red-sec-txt">
            {{ $t('landing.quote_description_2') }}
        </p>
        <p class="red-sec-txt">— {{ $t('landing.quote_author_2') }}</p>
      </b-col>
    </b-row>
    <div class="btn-bottom-container">
      <b-button class="btn-primary btn-bottom" @click="$router.push('/register')">Sign up today</b-button>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    link: {
      type: String,
      required: true
    }
  },
  methods: {
    goToLink (url) {
      window.open(url, '_blank')
    }
  }
};
</script>
<style scoped>

.pre-line {
  white-space: pre-line;
}

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
  line-height: 1.5rem;
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

.it-works {
  margin-top: -15rem;
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
  line-height: 1.5rem;
}

.mid-subt {
  margin-top: 4rem;
  text-align: center;
  font-size: 3rem;
}

.black-band-wrapper{
  width: 100%;
}

.black-band {
  margin-top: 3rem;
  width: 100%;
  height: 45rem;
  background: black;
  color: white;
  padding: 4rem;
}
.line-item-container {
  width: 100%;
  display: flex;
  align-items: center;
  flex-direction: column;
  &:nth-child(odd){
    & > div:last-child {
      height: 7rem;
    }
  }
  &:nth-child(even){
    & > div:first-child {
      height: 7rem;
    }
  }

}

.line-item {

  font-size: 0.85rem;
  line-height: 1rem;
  height: 7rem;
  text-align: center;
  display: flex;
  align-items: center;
  gap: 10px;
  & > div {

    &:nth-child(1){
      display: flex;
      align-items: center;
      height: 100%;
    }

    & > p{
      text-align: center;
      white-space: pre-line;
    }
  }
}

.works-img {
  width: 5rem;
  height: 5rem;

}

.red-line-frag {
  margin-top: 4rem;
  margin-bottom: 4rem;
  height: 3rem;
  width: calc(100% + 1px);
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

.s-up-btn {
  font-size: 1.3rem;
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
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.example-card .example-card-img-container {
  height: 300px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.example-card .example-card-img-container img {
  height: 230px;
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
  font-weight: 300;
  line-height: 34px;
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

.quote-author-img {
  width: 12rem;
  height: 12rem;
  border-radius: 50%;
  overflow: hidden;
  margin-bottom: 2rem;
}

.quote-author-img img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

.quote-author-img-2 img {
  width: 350px;
  height: 350px;
  object-fit: cover;
  object-position: -100px -30px;
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
  max-width: 1200px;
  text-align: center;
  line-height: 35px;
  font-weight: 600;
}

.btn-bottom-container {
  width: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
}

.btn-bottom {
  margin-top: 3rem;
  margin-bottom: 3rem;
  font-size: 2rem;
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
    bottom: 20rem;
  }

  .head-img {
    width: 80%;
  }

  .map-img {
    margin-top: -10rem;
  }

  .it-works {
    margin-top: 0;
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




@media screen and (max-width: 1280px) {
  .white-text {
    font-size: 1rem;
  }
  .white-subtitle {
    font-size: 2.6rem;
  }
  .works-img {
    width: 4rem;
    height: 4rem;
  }

  .line-item {
    font-size: 0.8rem;
    flex-direction: column;
  }

}
@media screen and (max-width: 768px) {
  .white-text {
    font-size: 0.9rem;
  }
  .white-subtitle {
    font-size: 2rem;
  }
  .line-item {
      flex-direction: column;
      font-size: 0.8rem;
    }
}


</style>

