<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Gambling.com Test</title>
    <link rel="shortcut icon" href="">
    <meta name="description" content="@yield('meta_description', config('app.name'))">
    <meta name="author" content="Tobias Liauw">
    <link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet'>
    <!-- Bootstrap Css -->
    <link href="{{ URL::asset('/assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('/assets/libs/leaflet/leaflet.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <style>
        #map { height: 100vh }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card mt-4 p-2">
                        <form method="POST" action="/"  enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="text-center">
                                        <h4 class="font-size-18 mt-4">Gambling.com Affiliate Distance Checker</h4>
                                    </div>
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-10">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="office">Office</label>
                                            <input type="text" value="Office Dublin" name="office" class="form-control" disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="latitude">Latitude</label>
                                            <input type="text" value="53.3340285" name="latitude" class="form-control" disabled>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="longitude">Longitude</label>
                                            <input type="text" value="-6.2535495" name="longitude" class="form-control" disabled>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="distance">Distance (in km)</label>
                                            <input type="number" name="distance" value="100" class="form-control" disabled>
                                        </div>
                                        <div class="col-md-9">
                                            <label for="file">File</label>
                                            <input type="file" name="file" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" style="padding-top: 45px">
                                    <input class="btn btn-primary btn-block pt-4 pb-4" type="submit" value="Upload List">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="panel">
                    <div class="row">
                        @if(isset($affiliates) && $affiliates->count()>0)
                            @foreach($affiliates as $affiliate)
                            <div class="col-md-3">
                                <div class="card p-1">
                                <strong>{!! $affiliate->name !!} (ID: {!! $affiliate->affiliate_id !!})</strong><br/>
                                    {!! $affiliate->latitude !!},{!! $affiliate->longitude !!}<br/>
                                Distance: {!! $affiliate->calculateDistance() !!}
                                </div>
                            </div>
                            @endforeach
                            <div class="col-md-12">
                                Number of valid Entries: {!! $affiliates->count() !!}
                                <a href="javascript:showValidAffiliates()">Show on Map</a>
                            </div>
                        @endif
                    </div>
                    </div>
                </div>
                <div class="col-md-6 p-0">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- JAVASCRIPT -->
    <script src="{{ URL::asset('/assets/libs/jquery/jquery.min.js')}}"></script>
    <script src="{{ URL::asset('/assets/libs/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{ URL::asset('/assets/libs/leaflet/leaflet.js')}}"></script>
    <script>
        const dublinOffice = [53.3340285, -6.2535495];
        let map = L.map('map').setView(dublinOffice, 8);

        function showValidAffiliates() {
            $.ajax({
                type: 'GET',
                url: '/api/showValidAffiliates',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response, textStatus, jqXhr) {
                    console.log(response);
                    Object.entries(response).forEach(([key,entry]) => {
                        console.log(entry);
                        L.marker([entry.latitude, entry.longitude],{icon:L.icon({iconUrl: 'map-marker.png'}),title: entry.name} ).addTo(map);
                    })
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log("Es ist ein Fehler aufgetreten: " + textStatus, errorThrown);
                }
            })
        }

        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let token = "pk.eyJ1IjoibGlhdXdtZWRpYSIsImEiOiJja3lybHo5eHEwMHYxMm5uMWxhcXk1a2x5In0.A1BVwwZU7kHVTVanNRhrng";
            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token='+token, {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
                maxZoom: 18,
                id: 'mapbox/streets-v11',
                tileSize: 512,
                zoomOffset: -1,
                accessToken: token
            }).addTo(map);

            let marker = L.marker(dublinOffice).addTo(map);
            let circle = L.circle(dublinOffice, {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.1,
                radius: 100000
            }).addTo(map);

        })
    </script>
</body>
</html>
