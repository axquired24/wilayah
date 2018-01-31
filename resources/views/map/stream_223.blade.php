<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1, maximum-scale=1,user-scalable=no">
<title>KML {{ strtoupper($lokasi) }}</title>
<link rel="stylesheet" href="https://js.arcgis.com/3.23/dijit/themes/tundra/tundra.css">
<link rel="stylesheet" href="https://js.arcgis.com/3.23/esri/css/esri.css">
<style>
  html, body { height: 100%; width: 100%; margin: 0; padding: 0; }
  #map { height: 100%; margin: 0; padding: 0; }
  #meta {
    position: absolute;
    left: 20px;
    bottom: 20px;
    width: 300px;
    height: 100px;
    z-index: 40;
    background: #fff;
    color: #777;
    padding: 5px;
    border: 2px solid #666;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    font-family: arial;
    font-size: 0.9em;
  }
  #meta h3 {
    color: #666;
    font-size: 1.1em;
    padding: 0px;
    margin: 0px;
    display: inline-block;
  }
  #loading {
    float: right;
  }
</style>

<script src="https://js.arcgis.com/3.23/"></script>
<script>
  var map;
  require([
    "esri/map", "esri/layers/KMLLayer",
    "dojo/parser", "dojo/dom-style",

    "dijit/layout/BorderContainer", "dijit/layout/ContentPane", "dojo/domReady!"
  ], function(
    Map, KMLLayer,
    parser, domStyle
  ) {
    map = new Map("map", {
      basemap: "topo",
      center: [-108.663, 42.68],
      zoom: 6
    });

    parser.parse();

    // var kmlUrl = "https://esri.box.com/shared/static/nr6tol9ln8vng0szqlaqx5kmd9wf6bb6.kml";
    var kmlUrl = "{{ $kmlstream }}";
    var kml = new KMLLayer(kmlUrl);
    map.addLayer(kml);
    kml.on("load", function() {
      domStyle.set("loading", "display", "none");
    });
  });
</script>
</head>

<body class="tundra">
<div data-dojo-type="dijit/layout/BorderContainer"
     data-dojo-props="design:'headline',gutters:false"
     style="width: 100%; height: 100%; margin: 0;">
  <div id="map"
       data-dojo-type="dijit/layout/ContentPane"
       data-dojo-props="region:'center'">
    <div id="meta">
      <span id="loading"><img src="images/loading_black.gif" /></span>
      <h3>Display KML Using a <a href="https://developers.arcgis.com/javascript/3/jsapi/kmllayer-amd.html">KMLLayer</a></h3>
      <br />
      The map displays a simple KML file that was created using Google Earth and
      is hosted on an Esri server. Symbology and attributes are honored.
    </div>
  </div>
</div>
</body>
</html>