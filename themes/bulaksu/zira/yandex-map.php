<?php $apiKey = Zira\Config::get('yandex_map_key'); ?>
<?php $latitude = Zira\Config::get('maps_latitude'); ?>
<?php $longitude = Zira\Config::get('maps_longitude'); ?>
<?php if ((!empty($latitude)  && !empty($longitude)) || !empty($address)): ?>
<script async defer src="https://api-maps.yandex.ru/2.1/?lang=<?php echo Zira\Locale::getLanguage(); ?>&onload=yandex_map_init<?php if (!empty($apiKey)) echo '&apikey='.Zira\Helper::html($apiKey); ?>" type="text/javascript"></script>
<div id="yandex-map" style="width:100%; height: 400px"></div>
<?php if (!empty($latitude) && !empty($longitude)): ?>
<script type="text/javascript">
function yandex_map_init(){
    yandexMap = new ymaps.Map("yandex-map", {
        center: [<?php echo Zira\Helper::html($latitude); ?>, <?php echo Zira\Helper::html($longitude); ?>],
        zoom: 15
    });
    yandexPlacemark = new ymaps.Placemark(
        [<?php echo Zira\Helper::html($latitude); ?>, <?php echo Zira\Helper::html($longitude); ?>], {
            hintContent: '<?php if (!empty($name)) echo Zira\Helper::html($name); ?>',
            balloonContent: '<?php if (!empty($address)) echo Zira\Helper::html($address); ?>'
        }
    );
    yandexMap.geoObjects.add(yandexPlacemark);
    //yandexMap.controls.add(new ymaps.control.ZoomControl()).add(new ymaps.control.ScaleLine()).add('typeSelector');
}
</script>
<?php endif; ?>
<?php if ((empty($latitude)  || empty($longitude)) && !empty($address)): ?>
<script type="text/javascript">
function yandex_map_init(){
    var yandexMap,yandexPlacemark,yandexGeocoder;
    yandexGeocoder = ymaps.geocode("<?php echo Zira\Helper::html($address); ?>");
    yandexGeocoder.then(
        function (res) {
            var firstGeoObject = res.geoObjects.get(0);
            yandexMap = new ymaps.Map("yandex-map", {
                center: firstGeoObject.geometry.getCoordinates(),
                zoom: 15
            });
            yandexPlacemark = new ymaps.Placemark(
                firstGeoObject.geometry.getCoordinates(), {
                    hintContent: '<?php if (!empty($name)) echo Zira\Helper::html($name); ?>',
                    balloonContent: '<?php echo Zira\Helper::html($address); ?>'
                }
            );
            yandexMap.geoObjects.add(yandexPlacemark);
            //yandexMap.controls.add(new ymaps.control.ZoomControl()).add(new ymaps.control.ScaleLine()).add('typeSelector');
        },
        function (err) {
            // not found
        }
    );
}
</script>
<?php endif; ?>
<?php endif; ?>
