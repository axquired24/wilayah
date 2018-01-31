<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
  <title>KML {{ strtoupper($lokasi) }}</title>

  <style>
    html,
    body,
    #viewDiv {
      padding: 0;
      margin: 0;
      height: 100%;
      width: 100%;
    }
  </style>

  <link rel="stylesheet" href="https://js.arcgis.com/4.6/esri/css/main.css">
  <script src="https://js.arcgis.com/4.6/"></script>

  <script>
    require([
      "esri/Map",
      "esri/views/MapView",
      "esri/layers/KMLLayer",
      "esri/widgets/ScaleBar",
      "dojo/domReady!"
    ], function(
      Map,
      MapView,
      KMLLayer,
      ScaleBar
    ) {
      
      // To add a KML file(.kml or .kmz) to a map, the KML must be available via a publicly accessible URL. Locally hosted or KML files inside a firewall are not supported.

      var layer = new KMLLayer({
        // url: "http://quickmap.dot.ca.gov/data/lcs.kml" // lane closures from California Dept of Transportation
        url: "{{ $kmlstream }}"
      });

      var map = new Map({
        basemap: "topo",
        layers: [layer]
      });

      // Indonesia Center Point
      // 0.7893° S, 113.9213° E

      // yogyakarta Center Point
      // -7.8722733,110.1434142,10z

      // Jawa Barat Center Point
      // @-6.9305803,107.1823786,9.25z
      var view = new MapView({
        container: "viewDiv",
        map: map,
        center: [107.1823786, -6.9305803],
        zoom: 9.25
      });

      var scalebar = new ScaleBar({
        view: view
      });
      view.ui.add(scalebar, "bottom-left");

    });
  </script>

</head>

<body>    
  <div id="viewDiv"></div>
</body>

</html>
