// Focus field
panel.field('focus', {
  props: {
    name: {
      default: 'focus'
    },
    label: {
      default: 'Focus'
    }
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
  created () {
    this.fetch()
  },
  methods: {
    fetch () {
      this.$api.file.get(this.$route.params.path, this.$route.params.filename).then((file) => {
        this.image = file.url + '?v=' + file.modified
      })
    },
    setFocus(event) {
      this.left = Math.round(event.offsetX / event.target.width * 100) / 100
      this.top = Math.round(event.offsetY / event.target.height * 100) / 100
      this.$emit('input', this.json)
    }
  },
  template: `
    <kirby-field class="kirby-focus-field" :label="label" :required="required" :readonly="readonly" :name="name">
      <div class="focus-box">
        <img class="focus-preview" :src="image" @click="setFocus" />
        <div class="focus-point" :style="style"></div>
      </div>
    </kirby-field>
  `
});
