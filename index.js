panel.plugin("flokosiol/focus", {
  fields: {
    focus: {
      props: {
        label: String,
        value: String,
        image: String,
        help: String,
        isFileBlueprint: Boolean
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
      watch: {
      	value(newVal, oldVal) {
      		var newVal = JSON.parse(newVal);
      		if(newVal.x != this.left) this.left = newVal.x
	      	if(newVal.y != this.top)  this.top  = newVal.y
	    },
      },
      methods: {
        setFocus(event) {
          this.left = Math.round(event.offsetX / event.target.width * 100) / 100
          this.top = Math.round(event.offsetY / event.target.height * 100) / 100
          this.$emit('input', this.json)
        }
      },
      template: `
        <k-field v-bind="$props" class="kirby-focus-field" >
          <div v-if="image" class="focus-box">
            <div class="focus-preview-container">
            	<img class="focus-preview" :src="image" @click="setFocus" />
            	<div class="focus-point" :style="style"></div>
            </div>
            <div class="focus-background"></div>
            <slot name="footer"></slot>
          </div>
          <div v-if="!isFileBlueprint">
            <k-info-field label="" text="Sorry. The focus field can only be used in a file blueprint!" theme="negative" />
            <div data-theme="help" class="k-text k-field-help">
              Learn more about <a href="https://getkirby.com/docs/reference/panel/blueprints/file" target="_blank" title="check out the Kirby documentation">file blueprints</a>.
            </div>
          </div>
        </k-field>
      `
    }
  }
});