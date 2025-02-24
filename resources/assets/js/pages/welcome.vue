<template>
  <div>
    <b-row>
      <b-col cols="5">
        <div>
          <img class="head-img" :src="src('landingHead')">
          <div class="top-head">
            <h2 class="white-subtitle">IFRC Safety</h2>
            <h2 class="white-subtitle">Message Database</h2>
            <p class="white-text mt-4">Safety messages based on IFRC Public Awareness and</p>
            <p class="white-text">Public Education (PAPE) resource</p>
            <div class="bottom-head">
              <p class="bottom-head-text">
                Access and share trusted Red Cross Red Crescent
              </p>
              <p class="bottom-head-text">
                actionable messages to help people safely prepare for
              </p>
              <p class="bottom-head-text">
                and respond to hazards.
              </p>
              <b-button class="btn-primary mt-4" @click="$router.push('/register')">
                Sign Up
              </b-button>
            </div>
          </div>
        </div>
      </b-col>
      <b-col cols="7">
        <img class="map-img" :src="src('mapImg')">
      </b-col>
    </b-row>
    <h2 class="mid-subt">How It Works</h2>
    <b-row>
      <b-col>
        <div class="black-band">
          <div class="line-container">
            <div class="line-item-container">
              <div class="line-item">
                <p>
                  Red Cross Red Crescent National
                </p>
                <p>
                  Society and government partners
                </p>
                <p>
                  agree on national sets of messages
                </p>
              </div>
              <div class="red-line-frag left-radius">
                <div class="red-pointer-top"></div>
              </div>
              <div class="number-line-item">
                <div class="circle-item">
                  <p class="circle-item-number">
                    1
                  </p>
                </div>
              </div>
            </div>
            <div class="line-item-container">
              <div class="number-line-item">
                <div class="circle-item">
                  <p class="circle-item-number">
                    2
                  </p>
                </div>
              </div>
              <div class="red-line-frag">
                <div class="red-pointer-bottom"></div>
              </div>
              <div class="line-item">
                <p>
                  Red Cross Red Crescent National
                </p>
                <p>
                  Society and government partners
                </p>
                <p>
                  agree on national sets of messages
                </p>
              </div>
            </div>
            <div class="line-item-container">
              <div class="line-item">
                <p>
                  Red Cross Red Crescent National
                </p>
                <p>
                  Society and government partners
                </p>
                <p>
                  agree on national sets of messages
                </p>
              </div>
              <div class="red-line-frag">
                <div class="red-pointer-top"></div>
              </div>
              <div class="number-line-item">
                <div class="circle-item">
                  <p class="circle-item-number">
                    3
                  </p>
                </div>
              </div>
            </div>
            <div class="line-item-container">
              <div class="number-line-item">
                <div class="circle-item">
                  <p class="circle-item-number">
                    4
                  </p>
                </div>
              </div>
              <div class="red-line-frag">
                <div class="red-pointer-bottom"></div>
              </div>
              <div class="line-item">
                <p>
                  Red Cross Red Crescent National
                </p>
                <p>
                  Society and government partners
                </p>
                <p>
                  agree on national sets of messages
                </p>
              </div>
            </div>
            <div class="line-item-container">
              <div class="line-item">
                <p>
                  Red Cross Red Crescent National
                </p>
                <p>
                  Society and government partners
                </p>
                <p>
                  agree on national sets of messages
                </p>
              </div>
              <div class="red-line-frag right-radius">
                <div class="red-pointer-top"></div>
              </div>
              <div class="number-line-item">
                <div class="circle-item">
                  <p class="circle-item-number">
                    5
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="btns-container">
            <b-button class="btn-primary mr-2">
              Learn more
            </b-button>
            <b-button class="btn-primary ml-2">
              Watch video
            </b-button>
          </div>
        </div>
      </b-col>
    </b-row>
    <h2 class="mid-subt">Example Use Case</h2>
    <b-row class="usecases">
      <b-col class="card-use-container">
        <div class="example-card">
          <h6 class="card-subt">
            API
          </h6>
          <p class="card-txt">
            Include messages with hurricane early
          </p>
          <p class="card-txt">
            warning alerts
          </p>
          <img class="map-img" src="cardImage1">
        </div>
      </b-col>
      <b-col class="card-use-container">
        <div class="example-card">
          <h6 class="card-subt">
            Social Media
          </h6>
          <p class="card-txt">
            Post images of wildfire preparedness
          </p>
          <p class="card-txt">
            messages to your network
          </p>
          <img class="map-img" src="cardImage3">
        </div>
      </b-col>
      <b-col class="card-use-container">
        <div class="example-card">
          <h6 class="card-subt">
            WhatsApp
          </h6>
          <p class="card-txt">
            Share flood mitigation actions with your
          </p>
          <p class="card-txt">
            family and friends
          </p>
          <img class="map-img" src="cardImage3">
        </div>
      </b-col>
    </b-row>
    <h2 class="mid-subt">Impact</h2>
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
  height: 40rem;
  background: black;
  color: white;
  padding: 4rem;
}

.line-item {
  font-size: 1rem;
  line-height: 0.6rem;
  text-align: center;
  height: 5rem;
}

.circle-item {
  background: #F6333F;
  width: 5rem;
  height: 5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.circle-item-number {
  font-size: 4rem;
  font-weight: 600;
  margin-top: 1rem;
}

.red-line-frag {
  margin-top: 4rem;
  height: 3rem;
  width: 100%;
  background: #F6333F;
  margin-bottom: 4rem;
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

.line-item-container {
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
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

.left-radius {
  border-bottom-left-radius: 100px;
  border-top-left-radius: 100px;
}

.right-radius {
  border-bottom-right-radius: 100px;
  border-top-right-radius: 100px;
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
  width: 70%;
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
  line-height: 1rem;
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
</style>

