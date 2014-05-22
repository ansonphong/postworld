<script>
  function <?php echo $slider['instance']; ?>($scope) {
    $scope.sliderInterval = <?php echo $slider['interval']; ?>;
    $scope.slides = <?php echo json_encode($posts); ?>;
  }
</script>

<div
  id="<?php echo $slider['id']; ?>"
  class="<?php echo $slider['class']; ?>"
  style="height: 480px"
  ng-controller="<?php echo $slider['instance']; ?>">

  <carousel interval="sliderInterval">
    <slide ng-repeat="slide in slides" active="slide.active">
      <img
        ng-src="{{slide.image.full.url}}"
        style="margin:auto; width:100%; height:480px;">
      <div class="carousel-caption">
        <h4>{{slide.post_title}}</h4>
        <p>{{slide.post_excerpt}}</p>
      </div>
    </slide>
  </carousel>
</div>
