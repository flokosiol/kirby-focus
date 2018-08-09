panel.plugin("flokosiol/focus", {
  fields: {
    focus: {
      props: {
        label: String,
        value: String,
        image: String
      },
      data () {
        let coordinates = JSON.parse(this.value || '{"x":0.5,"y":0.5}')

        return {
          left: coordinates.x,
          top: coordinates.y,
          image: null
        }
      },
      computed: {
        json () {
          return `{"x":${this.left},"y":${this.top}}`
        },
        style () {
          return {
            left: `${this.left * 100}%`,
            top: `${this.top * 100}%`
          }
        }
      },
      methods: {
        setFocus(event) {
          this.left = Math.round(event.offsetX / event.target.width * 100) / 100
          this.top = Math.round(event.offsetY / event.target.height * 100) / 100
          this.$emit('input', this.json)
        }
      },
      template: `
        <k-field v-bind="$props" v-if="image" class="kirby-focus-field" >
          <div class="focus-box">
            <img class="focus-preview" :src="image" @click="setFocus" />
            <div class="focus-point" :style="style"></div>
          </div>
        </k-field>
      `
    }
  }
});