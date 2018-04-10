<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <!-- Styles -->
        <link href="{{mix('css/app.css')}}" rel="stylesheet" type="text/css">
        <link href="./css/style.css" rel="stylesheet" type="text/css">
        <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.44.1/mapbox-gl.js'></script>
        <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.44.1/mapbox-gl.css' rel='stylesheet' />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.6.2/css/bulma.min.css">
        <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }
        </style>
    </head>
    <body>

        <div id='bottle1' class="progress1"></div>
        <div id='bottle2' class="progress1"></div>
        <div id='bottle3' class="progress1"></div>
        <div id='bottle4' class="progress1"></div>

      <div class="container">
            <div class="columns">
                <div class="column">
                    <div id='map' style='width: 1200px; height: 800px;'></div>
                </div>
                <div class="column">
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.0/socket.io.js"></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js'></script>
        <script type="text/javascript">

            mapboxgl.accessToken = 'pk.eyJ1IjoiYXNxdWFyZTk1IiwiYSI6ImNpeTJlazJyYjAwMXIzM21ucXNyZGt4eTMifQ.KlTGrUh9ptARSYnCUmhWow';
            var map = new mapboxgl.Map({
                container: 'map',
                center: [101.689284, 2.924542],
                zoom: 13,
                style: 'mapbox://styles/mapbox/streets-v9'
            });

            var gates = ['bottle1', 'bottle2', 'bottle3', 'bottle4'];

            var coords  = [[101.688478, 2.908532],[101.682815, 2.926047],[101.674933, 2.914450],[101.674616, 2.899531]];

            gates.forEach(function(gate, i){
                return marker = new mapboxgl.Marker(document.getElementById(gate))
                  .setLngLat(coords[i])
                  .addTo(map);
            });

            function moveProgressBar(progId, state, level){

                var states = ['started', 'inProgress', 'completed'],
                    segmentWidth = 100,
                    currentState = 'started';

                var colorScale = d3.scale.ordinal()
                    .domain(states)
                    .range(['yellow', 'orange', 'green']);
                var progs = d3.select(progId);
                progs.transition()
                    .duration(800)
                    .attr('fill', 'grey')
                    .attr('height', function(){
                        return 100 - level;
                    });
            }

            gates.forEach(function(gate, i){
                var index = i + 1;
                return window['bottle'+index] = new Vue({
                  el: '#'+gate,
                  data: {
                    water_level: ''
                  },

                  created: function() {
                   var id = i + 1;
                   var progId = "prog"+id;
                   var gateId = "#bottle"+id;
                   var vm = this;
                   var level = vm.fetchData();
                   level.then(data => {vm.d3create(gateId, progId, data)})
                  },

                  methods: {
                   fetchData: function() {
                    var id = i + 1;
                    var vm = this;
                    var level = axios.get('{{ url('/') }}/api/level/'+id)
                    .then(function(data) {
                        vm.water_level = data.data;
                        return vm.water_level;
                    });
                    return level;
                   },

                   d3create: function(bottleId, progId, level) {
                        var svg = d3.select(bottleId)
                            .append('svg')
                            .attr('width', 20)
                            .attr('height', 100);

                        var states = ['started', 'inProgress', 'completed'],
                            segmentWidth = 100,
                            currentState = 'started';

                        var colorScale = d3.scale.ordinal()
                            .domain(states)
                            .range(['yellow', 'orange', 'green']);

                        svg.append('rect')
                            .attr('class', 'bg-rect')
                            .attr('rx', 0)
                            .attr('ry', 10)
                            .attr('fill', ' #5abcd8')
                            .attr('width', 20)
                            .attr('height', 100)
                            .attr('x', 0);

                        var progress = svg.append('rect')
                                        .attr('class', 'progress-rect')
                                        .attr('id', progId)
                                        .attr('fill', 'grey')
                                        .attr('width', 20)
                                        .attr('height', function(){
                                            return 100 - level;
                                        })
                                        .attr('rx', 0)
                                        .attr('ry', 10)
                                        .attr('x', 0)
                                        .attr('y', 0);

                        // progress.transition()
                        //     .duration(1000)
                        //     .attr('y', 0)
                        //     .attr('height', function(){
                        //         console.log(100 - level);
                        //         return 100 - level;
                        //     });

                        return svg;

                    }

                  }
                });
            });

            var socket = io('http://localhost:6001');

            socket.on('test-channel:App\\Events\\WaterLevelChanged', function(data) {
               console.log(data);
               var id = data.gate.id;
               window['bottle'+id].water_level = data.gate.water_level;
               var progId = "#prog"+id;
                if (data.gate.water_level <= 40) {
                    moveProgressBar(progId, 'started', data.gate.water_level);
                } else if (data.gate.water_level > 40 && data.gate.water_level <= 50) {
                    moveProgressBar(progId, 'inProgress', data.gate.water_level);
                } else {
                    moveProgressBar(progId, 'completed', data.gate.water_level);
                }
            });

        </script>
    </body>
</html>
