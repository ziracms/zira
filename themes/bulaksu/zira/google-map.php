<?php $apiKey = Zira\Config::get('google_map_key'); ?>
<?php $latitude = Zira\Config::get('maps_latitude'); ?>
<?php $longitude = Zira\Config::get('maps_longitude'); ?>
<?php if (((!empty($latitude)  && !empty($longitude)) || !empty($address)) && !empty($apiKey)): ?>
<div id="google-map" style="width:100%; height: 400px"></div>
<?php if (!empty($latitude) && !empty($longitude) && !empty($apiKey)): ?>
<?php layout_js_begin(); ?>
<script type="text/javascript">
function google_map_init() {
    var map = new google.maps.Map(document.getElementById('google-map'), {
      zoom: 15,
      center: {lat: <?php echo Zira\Helper::html($latitude); ?>, lng: <?php echo Zira\Helper::html($longitude); ?>}
    });
    var marker = new google.maps.Marker({
        map: map,
        position: {lat: <?php echo Zira\Helper::html($latitude); ?>, lng: <?php echo Zira\Helper::html($longitude); ?>},
        title: '<?php if (!empty($name)) echo Zira\Helper::html($name); else if (!empty($address)) echo Zira\Helper::html($address); ?>'
    });
}
</script>
<?php layout_js_end(); ?>
<?php endif; ?>
<?php if ((empty($latitude)  || empty($longitude)) && !empty($address) && !empty($apiKey)): ?>
<?php layout_js_begin(); ?>
<script type="text/javascript">
function google_map_init() {
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({'address': '<?php echo Zira\Helper::html($address); ?>'}, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
            var map = new google.maps.Map(document.getElementById('google-map'), {
              zoom: 15,
              center: results[0].geometry.location
            });
            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location,
                title: '<?php if (!empty($name)) echo Zira\Helper::html($name); else echo Zira\Helper::html($address); ?>'
            });
        } else {
            // not found
        }
    });
}
</script>
<?php layout_js_end(); ?>
<?php endif; ?>
<?php layout_js_begin(); ?>
<script async defer src="https://maps.googleapis.com/maps/api/js?language=<?php echo Zira\Locale::getLanguage(); ?>&callback=google_map_init&key=<?php echo Zira\Helper::html($apiKey); ?>"></script>
<?php layout_js_end(); ?>
<?php endif; ?>