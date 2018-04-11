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
        <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.44.1/mapbox-gl.js'></script>
        <link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.44.1/mapbox-gl.css' rel='stylesheet' />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.6.2/css/bulma.min.css">
        <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    </head>
    <body>

        <div id="components-demo">
{{--             <modal-popup gate="1" :id="popup1"></modal-popup>
 --}}        </div>

        <div id="bottle1-container">
            <div id='bottle1' class="progress1" v-on:click="infoChart(1)"></div>
        </div>

        <div id="bottle2-container">
            <div id='bottle2' class="progress1" v-on:click="infoChart(2)"></div>
        </div>

        <div id="bottle3-container">
            <div id='bottle3' class="progress1" v-on:click="infoChart(3)"></div>
        </div>

        <div id="bottle4-container">
            <div id='bottle4' class="progress1" v-on:click="infoChart(4)"></div>
        </div>

      <div class="container">
            <div class="columns">
                <div class="column" id="map-col">
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
                var marker = new mapboxgl.Marker(document.getElementById(gate))
                  .setLngLat(coords[i])
                  .addTo(map);
            });

            function moveProgressBar(progId, state, level, id){

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
                var fill = level > 50 ? "#f43b47" : "#73C8A9";
                var circleId = "#bottle"+id+"-circle"
                var circle = d3.select(circleId)
                    .attr('fill', fill);
            }

            gates.forEach(function(gate, i){
                var id = i + 1;
                return window['bottle'+id] = new Vue({
                  el: '#'+gate+"-container",
                  data: {
                    water_level: '',
                    info: ''
                  },

                  created: function() {
                   var progId = "prog"+id;
                   var gateId = "#bottle"+id;
                   var vm = this;
                   var level = vm.fetchData();
                   level.then(data => {vm.d3create(gateId, progId, data)});
                  },

                  methods: {
                   fetchData: function() {
                    var vm = this;
                    var level = axios.get('{{ url('/') }}/api/level/'+id)
                    .then(function(data) {
                        vm.water_level = data.data;
                        return vm.water_level;
                    });
                    return level;
                   },

                   d3create: function(bottleId, progId) {
                        var vm = this;
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
                                            return 100 - vm.water_level;
                                        })
                                        .attr('rx', 0)
                                        .attr('ry', 10)
                                        .attr('x', 0)
                                        .attr('y', 0);

                        var circleId = "bottle"+id+"-circle";

                        var fill = vm.water_level > 50 ? "#f43b47" : "#73C8A9";
                        var circle = svg.append("circle")
                            .attr("cx", 10)
                            .attr("cy", 10)
                            .attr("id", circleId)
                            .attr('fill', fill)
                            .attr("r", 5);

                        return svg;

                    },

                    infoChart: function() {
                        console.log("clicked!");
                        var id = i + 1;
                        var vm = this;
                        var level = axios.get('{{ url('/') }}/api/info/'+id)
                        .then(function(data) {
                            console.log(data.data);
                            vm.info = data.data;
                        });
                        // return level;
                    }

                  }
                });
            });

            // Define a new component called button-counter
            Vue.component('modal-popup', {
              props:['gate', 'id'],
              data: function () {
                var vm = this;
                var info = axios.get('{{ url('/') }}/api/info/'+ vm.gate)
                .then(function(data) {
                    vm.info = data.data
                });
                return {
                  info: '',
                  active: false
                }
              },
              created: function() {

              },

              methods:{
                    d3create: function() {
                        var vm = this;
                        var gateId
                        var width = 330,
                            height = 250,
                            twoPi = 2 * Math.PI; 

                        var dataset = {
                                          progress: 35,
                                          total: 46
                                      };
                         
                        var arc = d3.svg.arc()
                            .innerRadius(170)
                            .outerRadius(220)
                            .startAngle(0);
                         
                        var svg = d3.select(vm.id).append("svg")
                            .attr("width", width)
                            .attr("height", height)
                          .append("g")
                            .attr("transform", "translate(" + width / 2 + "," + height / 2 + ")")

                        var meter = svg.append("g")
                            .attr("class", "season-progress");
                         
                        var background = meter.append("path")
                            .datum({endAngle: twoPi})
                            .style("fill", "#ddd")
                            .attr("d", arc);
                         
                        var foreground = meter.append("path")
                            .datum({endAngle:0})
                            .style("fill", "orange")
                            .attr("class", "foreground")
                            .attr("d", arc);
                         
                          foreground.transition()
                            .duration(1000)
                            .ease("linear")
                            .attrTween("d", function(d) {
                                       var interpolate = d3.interpolate(d.endAngle, twoPi * dataset["progress"] / dataset["total"])
                                       return function(t) {
                                          d.endAngle = interpolate(t);
                                          return arc(d);
                                       }  
                                    });

                          var text =  meter.append("text")
                            .attr("text-anchor", "middle")
                            .attr("dy", ".35em")
                            .attr("font-size", "24")
                            .text(dataset["progress"]);
                    }
              },

              template: `<div class="modal" v-bind:class="{'is-active': active != 'false'}">
                              <div class="modal-background"></div>
                              <div class="modal-card">
                                <header class="modal-card-head">
                                  <p class="modal-card-title">Modal title</p>
                                  <button class="delete" aria-label="close"></button>
                                </header>
                                <section class="modal-card-body">
                                    <div class="columns">
                                        <div class="column">
                                            <div id="halfCircle@{{ info.id }}"></div>
                                        </div>
                                        <div class="column">
                                            <div id="lineChart@{{ info.id }}"></div>
                                        </div>
                                    </div>
                                  @{{ info.id }}
                                  @{{ info.gate_open }}
                                  @{{ info.water_level }}
                                </section>
                                <footer class="modal-card-foot">
                                  <button class="button is-success">Save changes</button>
                                  <button class="button">Cancel</button>
                                </footer>
                              </div>
                         </div>`
            });

            new Vue({ el: '#components-demo' });

            var socket = io('http://localhost:6001');

            socket.on('test-channel:App\\Events\\WaterLevelChanged', function(data) {
               var id = data.gate.id;
               window['bottle'+id].water_level = data.gate.water_level;
               var progId = "#prog"+id;
                if (data.gate.water_level <= 40) {
                    moveProgressBar(progId, 'started', data.gate.water_level, id);
                } else if (data.gate.water_level > 40 && data.gate.water_level <= 50) {
                    moveProgressBar(progId, 'inProgress', data.gate.water_level, id);
                } else {
                    moveProgressBar(progId, 'completed', data.gate.water_level, id);
                }
            });

        </script>
{{--         <script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_API_KEY')}}&callback=initMap"></script>
 --}}    </body>
</html>
