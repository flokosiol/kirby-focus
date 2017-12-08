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
  data() {
    let focusImageUrl = panel.config.index + '/media/pages/' + this.$route.params.path + '/' + this.$route.params.filename;
    let focusCoordinates = JSON.parse(this.value || '{"x":0.5,"y":0.5}');

    return {
      focusImageUrl: focusImageUrl,
      focusPointerStyles: {
        left: focusCoordinates.x * 100 + '%',
        top: focusCoordinates.y * 100 + '%'
      }
    }
  },
  watch: {
    $route: 'fetchFocusData'
  },
  methods: {
    fetchFocusData() {
      this.$api.file.get(this.$route.params.path, this.$route.params.filename).then((file) => {
        this.focusImageUrl = file.url + '?v=' + file.modified;

        // #FIXME: focus field key is used fixed
        focusCoordinates = JSON.parse(file.content.focus || '{"x":0.5,"y":0.5}');
        this.focusPointerStyles = {
          left: focusCoordinates.x * 100 + '%',
          top: focusCoordinates.y * 100 + '%'
        }
      });
    },
    setFocus(event) {
      if (event) {
        let offsetXPercentage = Math.round(event.offsetX / event.target.width * 100) / 100;
        let offsetYPercentage = Math.round(event.offsetY / event.target.height * 100) / 100;

        // field value
        let pos = {'x':offsetXPercentage, 'y':offsetYPercentage};
        this.data = JSON.stringify(pos);

        // pointer
        this.focusPointerStyles.left = offsetXPercentage * 100 + '%';
        this.focusPointerStyles.top = offsetYPercentage * 100 + '%';
      }
    }
  },
  template: `
    <kirby-field :label="label" :required="required" :readonly="readonly" :name="name">
      <div class="kirby-focus">
        <div class="focus-wrapper">
          <div id="focus-box" class="focus-box" v-on:click="setFocus">
            <img class="focus-preview" :src="focusImageUrl">
            <div class="focus-point" :style="focusPointerStyles"></div>
          </div>
        </div>
        <input readonly class="focus-values" v-model="data">
      </div>
    </kirby-field>
  `,
});
