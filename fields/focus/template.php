<div data-box class="focus-box">
  <div data-point class="focus-point" style="left: <?php echo $x ?>%;top: <?php echo $y ?>%"></div>
  <div data-position class="focus-position"></div>
  <img class="focus-preview" src="<?php echo $file->url() ?>" width="300">
</div>

<script>
  $(document).ready(function () {
    
    $('[data-box]').click(function(){
      $('.focal_point').show();
    })

    var choose = $('[data-box]').focalpoint({
      point: $('[data-point]'),
      callback: function (pos) {
        delete pos.pos_x;
        delete pos.pos_y;
        $('#js-field-focus input').val(JSON.stringify(pos));
      }
    });

  });
</script>