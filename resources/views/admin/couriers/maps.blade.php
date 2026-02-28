 @extends('admin.layouts.app')

 @section('content')
     <div class="container-fluid">
         <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
             <h2 class="mb-3 me-auto">Kurye Takip Otomasyonu</h2>
             <div>
                 <ol class="breadcrumb">
                     <li class="breadcrumb-item"><a href="javascript:void(0)">Kuryeler</a></li>
                     <li class="breadcrumb-item"><a href="javascript:void(0)">Kurye Takip</a></li>
                 </ol>
             </div>
         </div>
         @if (session()->has('message'))
             <div class="alert alert-success alert-dismissible fade show">
                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close">
                 </button>
                 <a href="#"> {{ session()->get('success') }}</a>
             </div>
         @endif

         <style type="text/css">
             .couriers ul li {
                 padding: 10px;
             }
         </style>
         <div class="row">
             <div class="col-xl-12 col-lg-12">
                 <div id="map" style="width: %10; height: 650px;"></div>
             </div>
         </div>

     </div>
     <script>
		 let defaultLat = '{{auth()->user()->latitude}}';
		 let defaultLon = '{{auth()->user()->longitude}}';
		 var map = L.map('map').setView([defaultLat, defaultLon], 14);
         L.tileLayer('https://mt1.google.com/vt/lyrs=r&x={x}&y={y}&z={z}', {
             attribution: '&copy; <a href="#">{{config('site.name')}}</a> Kurye Takip Otomasyonu'
         }).addTo(map);

         var markersLayer = L.layerGroup().addTo(map);

         function updateMarkers(locations) {
             markersLayer.clearLayers();

             locations.forEach(function(location) {
                 var icon = L.icon({
                     iconUrl: 'https://i.hizliresim.com/1zxqajx.png',
                     iconSize: [65, 65],
                     iconAnchor: [16, 32],
                     popupAnchor: [0, -42]
                 });

                 var marker = L.marker([location.lat, location.lng], {
                     icon: icon
                 }).addTo(markersLayer);


                 marker.bindPopup('<b>' + location.name + '</b><br>' + location.description);
             });
         }


         function fetchLocations() {
             fetch('https://panel.parskurye.net/api/getLocations')
                 .then(response => response.json())
                 .then(data => {
                     updateMarkers(data);
                 })
                 .catch(error => {
                     console.error('Hata:', error);
                 });
         }

         fetchLocations();
         setInterval(fetchLocations, 5001);
     </script>
 @endsection
