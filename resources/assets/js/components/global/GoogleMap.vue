<template>
  <div class="google-map w-100 h-100" :id="mapName"></div>
</template>
<script>
export default {
  name: 'google-map',
  props: ['name', 'geoJson'],
  data: function () {
    return {
      mapName: this.name + "-map",
      map: null
    }
  },
  mounted () {
    this.map = new google.maps.Map(document.getElementById(this.mapName), {
      zoom: 4,
      center: {lat: 0, lng: 0}
    })

    // zoom to show all the features
    let bounds = new google.maps.LatLngBounds()
    this.map.data.addListener('addfeature', (e) => {
      this.processPoints(e.feature.getGeometry(), bounds.extend, bounds)
      this.map.fitBounds(bounds)
    })

    if (this.geoJson) {
      this.map.data.addGeoJson(this.geoJson)
      // Set the stroke width, and fill color for each polygon
      this.map.data.setStyle({
        fillColor: '#ff5113',
        strokeColor: '#ff5113',
        strokeWeight: 10
      })
    }
  },
  methods: {
    processPoints (geometry, callback, thisArg) {
      if (geometry instanceof google.maps.LatLng) {
        callback.call(thisArg, geometry)
      } else if (geometry instanceof google.maps.Data.Point) {
        callback.call(thisArg, geometry.get())
      } else {
        geometry.getArray().forEach((g) => {
          this.processPoints(g, callback, thisArg)
        })
      }
    }
  }
}
</script>
