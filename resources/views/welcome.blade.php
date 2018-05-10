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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.min.css">
        <link rel="stylesheet" type="text/css" href="/css/style.css">
        <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
        <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
        <style>

            .highcharts-yaxis-grid .highcharts-grid-line {
                display: none;
            }

            .chart-container{
                width: 500px;
                height: 300px;
            }

            .axis {
                font-family: sans-serif;
                fill: #d35400;
                font-size: 12px;
            }
            .line {
                fill: none;
                stroke: #f1c40f;
                stroke-width: 3px;
            }
            .smoothline {
                fill: none;
                stroke: #e74c3c;
                stroke-width: 3px;
            }
            .area {
                fill: #e74c3c;
                opacity: 0.5;
            }
            .circle {
                stroke: #e74c3c;
                stroke-width: 3px;
                fill: #FFF;
            }
            .grid {
                stroke: #DDD;
                stroke-width: 1px;
                fill: none;
            }
        </style>
    </head>
    <body style="background: #f5f5f5;">
        <section class="hero is-bold">
          <div class="hero-body has-text-centered">
            <div class="container">
              <h1 class="title">
                IoT Hydro Gate
              </h1>
              <h2 class="subtitle">
                Real Time Water Level Update
              </h2>
              <a class="button is-info is-outlined has-text-weight-bold" href="#map" id="show-me">
                  <span class="icon is-medium">
                    <i class="fas fa-angle-down"></i>
                  </span>
                  <span>Show Me</span>
              </a>
            </div>
          </div>
        </section>
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

      <div class="container" style="margin-left: 30px;">
            <div class="columns">
                <div class="column is-one-quarter" style="margin-top: 16px;">
                    <p class="buttons">
                        <a class="button is-success is-outlined has-text-weight-bold">
                          <span class="icon is-medium">
                            <i class="fas fa-angle-down"></i>
                          </span>
                          <span>Minimum</span>
                        </a>
                        <a class="button is-danger is-outlined has-text-weight-bold">
                          <span class="icon is-medium">
                            <i class="fas fa-angle-up"></i>
                          </span>
                          <span>Maximum</span>
                        </a>
                    </p>
                    <a id="bottle1-btn" data="bottle1" onclick="bounceMarker(1)" class="button mat-btn card-2 has-text-left">Gate 1: Tasik Putrajaya&nbsp;&nbsp;</a>
                    <a id="bottle2-btn" data="bottle2" onclick="bounceMarker(2)"  class="button mat-btn card-2 has-text-left">Gate 2: Sg. Ramal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                    <a id="bottle3-btn" data="bottle3" onclick="bounceMarker(3)" class="button mat-btn card-2 has-text-left">Gate 3: Sg. Chua&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                    <a id="bottle4-btn" data="bottle4" onclick="bounceMarker(4)"  class="button mat-btn card-2 has-text-left">Gate 4: Sg. Long&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                </div>
                <div class="column" id="map-col">
                    <div class="card card-5" id='map' style='width: 900px; height: 600px;'></div>
                    <div id="gate-popups">
                        <modal-popup gate="1" id="popup1" ref="popup1"></modal-popup>  
                        <modal-popup gate="2" id="popup2" ref="popup2"></modal-popup>
                        <modal-popup gate="3" id="popup3" ref="popup3"></modal-popup>
                        <modal-popup gate="4" id="popup4" ref="popup4"></modal-popup>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/vue@2.5.16/dist/vue.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.0/socket.io.js"></script>
       {{--  <script src='https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js'></script> --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
        <script src="https://d3js.org/d3.v5.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/highcharts-more.js"></script>js
        <script src="https://code.highcharts.com/modules/solid-gauge.js"></script>
        <script type="text/javascript">

            $(document).ready(function(){
              // Add smooth scrolling to all links
              $("#show-me").on('click', function(event) {

                // Make sure this.hash has a value before overriding default behavior
                if (this.hash !== "") {
                  // Prevent default anchor click behavior
                  event.preventDefault();

                  // Store hash
                  var hash = this.hash;

                  // Using jQuery's animate() method to add smooth page scroll
                  // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
                  $('html, body').animate({
                    scrollTop: $(hash).offset().top
                  }, 800, function(){
               
                    // Add hash (#) to URL when done scrolling (default click behavior)
                    window.location.hash = hash;
                  });
                } // End if
              });

              $(".mat-btn").on('click', function(event){

              });
            }); 

            mapboxgl.accessToken = 'pk.eyJ1IjoiYXNxdWFyZTk1IiwiYSI6ImNpeTJlazJyYjAwMXIzM21ucXNyZGt4eTMifQ.KlTGrUh9ptARSYnCUmhWow';
            var map = new mapboxgl.Map({
                container: 'map',
                center: [101.674933, 2.914450],
                zoom: 13,
                style: 'mapbox://styles/mapbox/streets-v9'
            });

            var gates = ['bottle1', 'bottle2', 'bottle3', 'bottle4'];

            var coords  = [[101.688478, 2.908532],[101.682815, 2.926047],[101.674933, 2.914450],[101.674616, 2.899531]];

            function moveProgressBar(progId, state, level, id){

                var states = ['started', 'inProgress', 'completed'],
                    segmentWidth = 100,
                    currentState = 'started';

                var colorScale = d3.scaleOrdinal()
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
                var circleId = "#bottle"+id+"-circle";
                var btnCircleId = "#bottle"+id+"-circle-btn";
                var circle = d3.select(circleId)
                    .attr('fill', fill);

                var circle_btn = d3.select(btnCircleId)
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
                   level.then(data => {vm.d3create(gateId, progId, id)});
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

                   d3create: function(bottleId, progId, id) {
                        var vm = this;
                        var btnId = bottleId + "-btn";
                        var svgId = "svgProgress"+id
                        var svg = d3.select(bottleId)
                            .append('svg')
                            .attr('width', 20)
                            .attr('id', svgId)
                            .attr('height', 100);

                        var btnSvg = d3.select(btnId)
                            .append('svg')
                            .attr('width', 30)
                            .attr('height', 30)

                        var states = ['started', 'inProgress', 'completed'],
                            segmentWidth = 100,
                            currentState = 'started';

                        var colorScale = d3.scaleOrdinal()
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
                        var btnCircleId = "bottle"+id+"-circle-btn";

                        var fill = vm.water_level > 50 ? "#f43b47" : "#73C8A9";
                        var circle = svg.append("circle")
                            .attr("cx", 10)
                            .attr("cy", 10)
                            .attr("id", circleId)
                            .attr('fill', fill)
                            .attr("r", 5);

                        var btn_circle = btnSvg.append("circle")
                            .attr("cx", 10)
                            .attr("cy", 15)
                            .attr("id", btnCircleId)
                            .attr('fill', fill)
                            .attr("r", 5);

                        return svg;

                    },

                    infoChart: function() {
                        var id = i + 1;
                        var popupId = "popup"+id;
                        var vm = this;
                        var level = axios.get('{{ url('/') }}/api/info/'+id)
                        .then(function(data) {
                            vm.info = data.data;
                        });
                        document.getElementById(popupId).className += ' is-active';
                        window['bottle'+id].$emit("active", id);
                        // return level;
                    }

                  }
                });
            });

            gates.forEach(function(gate, i){
                var markerId = i + 1;
                console.log(markerId);
                window['marker'+markerId] = new mapboxgl.Marker(document.getElementById(gate))
                  .setLngLat(coords[i])
                  .addTo(map);
            });

            function bounceMarker(markerId){
                var circleId = "#svgProgress"+markerId;
                d3.select(circleId).transition()
                      .duration(10000)
                      .attrTween("transform", function() { return d3.interpolateString("rotate(0)", "rotate(720)"); });
            }  

             function pulsate(selection) {
                recursive_transitions();

                function recursive_transitions() {
                    selection.transition()
                        .duration(400)
                        .attr("stroke-width", 2)
                        .attr("r", 8)
                        .ease(d3.easeSinIn)
                        .transition()
                        .duration(800)
                        .attr('stroke-width', 3)
                        .attr("r", 12)
                        .ease(d3.easeBounceIn)
                        .on("end", recursive_transitions);

                    // transition back to normal
                    setTimeout(function(){
                        selection.transition()
                        .duration(200)
                        .attr("r", 5)
                        .attr("stroke-width", 2)
                        .attr("stroke-dasharray", "1, 0");
                    }, 4000);          
                }
            }

            // Define a new component called button-counter
            Vue.component('modal-popup', {
              props:['gate', 'id'],
              data: function () {
                var vm = this;
                var info = axios.get('{{ url('/') }}/api/info/'+ vm.gate)
                .then(function(data) {
                    vm.info = data.data;
                    vm.lineChartId = "lineChart" + vm.gate;
                    vm.meterId = "meter" + vm.gate;
                    vm.gate_opened = data.data.gate_opened == 1 ? "opened" : "closed";
                });

                var records = axios.get('{{ url('/') }}/api/records/'+ vm.gate)
                .then(function(data) {
                    vm.records = data.data;
                });


                return {
                  info: '',
                  active: false,
                  'lineChartId': '',
                  'gate_opened': '',
                  'meterId': '',
                  'records': ''
                }
              },
              created: function() {
                // this.d3create();
              },

              mounted: function() {
                var vm = this;
                window['bottle'+vm.gate].$on('active', function (id) {
                  vm.d3create();
                })
              },

              updated: function() {
                    var vm = this;
              },

              methods:{

                    fetchData: function() {

                    },

                    closeModal: function(id) {
                        document.getElementById(id).classList.remove("is-active");
                    },
                    d3create: function() {
                        var vm = this;
                        var gateId = "lineChart" + vm.gate;
                        var chartId = "#chart" + this.gate
                        var modalId = "#"+this.id;
                        var labels = [];
                        var count = 0,
                            milliseconds = 0;
                        while(count < 20){
                            milliseconds += 3000;
                            var date = new Date(Date.now() - milliseconds);
                            labels.push(date.toLocaleTimeString());
                            count++;
                        }
                        window["chart_line"+vm.gate] = new Chart(document.getElementById(gateId),{"type":"line","data":{"labels":labels,"datasets":[{"label":"Water Level","data":vm.records,"fill":false,"borderColor":"rgb(75, 192, 192)","lineTension":0.1}]},"options":{
                            scales: {
                                yAxes: [{
                                  scaleLabel: {
                                    display: true,
                                    labelString: 'Water Level'
                                  }
                                }]
                              } 
                        }});
                    }
             },

              template: `<div class="modal" v-bind:id="id">
                              <div class="modal-background"></div>
                              <div class="modal-card" style="width: 80%;">
                                <header class="modal-card-head">
                                  <p class="modal-card-title" style="line-height:inherit;"> 
                                  <a v-if="gate_opened === 'opened'" class="button is-success">@{{ gate_opened }}</a>
                                  <a v-else class="button is-danger">@{{ gate_opened }}</a>
                                  &nbsp;&nbsp;Gate @{{ info.id }} Insight
                                  </p>
                                  <!--<p class="modal-card-title has-text-centered" style="line-height:inherit;"></p>-->
                                  <br>
                                  <button class="delete" aria-label="close" v-on:click="closeModal(id)"></button>
                                </header>
                                <section class="modal-card-body">
                                  Gate ID: @{{ info.id }} <br>
                                  Water Level: @{{ info.water_level }} <br>
                                    <div class="columns">
                                        <div class="column">
                                            <div class="chart-container">
                                                <canvas v-bind:id="lineChartId" style="width: 500px; height: 300px;"></canvas>
                                            </div>
                                        </div>
                                        <div class="column">
                                            <gauge :id="meterId" :initial="info.water_level" :gate="gate"></gauge>
                                            <a  v-if="gate_opened === 'opened'" v-on:click="gate_opened = 'closed'" class="button is-danger is-outlined has-text-weight-bold" style="margin-left:120px;">
                                              <span class="icon is-medium">
                                                <i class="fas fa-stop-circle"></i>
                                              </span>
                                              <span>Close Gate</span>
                                            </a>

                                            <a v-else v-on:click="gate_opened = 'opened'" class="button is-success is-outlined has-text-weight-bold" style="margin-left:120px;">
                                              <span class="icon is-medium">
                                                <i class="fab fa-codepen"></i>
                                              </span>
                                              <span>Open Gate</span>
                                            </a>
                                        </div>
                                    </div>
                                </section>
                                <footer class="modal-card-foot">
                                </footer>
                              </div>
                         </div>`
            });

            Vue.component('gauge',{
                props:['id', 'initial', 'gate'],
                data: function() {
                    var vm = this;
                    var info = axios.get('{{ url('/') }}/api/info/'+ vm.gate)
                    .then(function(data) {
                        vm.data = data.data;
                        vm.water_level = data.data.water_level;
                    });
                    return {
                        'data': '',
                        'water_level': ''
                    }
                },

                created: function(){

                },

                mounted: function(){
                    var vm = this;
                    window['bottle'+vm.gate].$on('active', function (id) {
                      vm.d3create();
                    })
                },

                methods:{
                    d3create: function(){
                        var vm = this;
                        var gaugeOptions = {

                            chart: {
                                type: 'solidgauge'
                            },

                            title: null,

                            pane: {
                                center: ['50%', '85%'],
                                size: '140%',
                                startAngle: -90,
                                endAngle: 90,
                                background: {
                                    backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                                    innerRadius: '60%',
                                    outerRadius: '100%',
                                    borderWidth: 0,
                                    shape: 'arc'
                                }
                            },

                            tooltip: {
                                enabled: false
                            },

                            // the value axis
                            yAxis: {
                                stops: [
                                    [0.1, '#55BF3B'], // green
                                    [0.5, '#DDDF0D'], // yellow
                                    [0.9, '#DF5353'] // red
                                ],
                                lineWidth: 0,
                                minorTickInterval: null,
                                tickAmount: 2,
                                title: {
                                    y: -70
                                },
                                labels: {
                                    y: 16
                                }
                            },

                            plotOptions: {
                                solidgauge: {
                                    dataLabels: {
                                        y: 5,
                                        borderWidth: 0,
                                        useHTML: true
                                    }
                                }
                            }
                        };

                        // The speed gauge
                        // console.log("water_pie"+this.gate);
                        window["water_pie"+this.gate] = Highcharts.chart(vm.id, Highcharts.merge(gaugeOptions, {
                            yAxis: {
                                min: 0,
                                max: 100,
                                title: {
                                    text: 'Water Level'
                                }
                            },

                            credits: {
                                enabled: false
                            },

                            series: [{
                                name: 'Water Level',
                                data: [vm.initial],
                                dataLabels: {
                                    format: '<div style="text-align:center"><span style="font-size:25px;color:' +
                                        ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span><br/>' +
                                           '<span style="font-size:12px;color:silver">litre</span></div>'
                                },
                                tooltip: {
                                    valueSuffix: 'litre'
                                }
                            }]

                        }));
                    }
                },

                template: `<div style="width: 400px; height: 200px; margin: 0 auto">
                             <div v-bind:id="id" style="width: 300px; height: 200px;"></div>
                           </div>`

            });

            var socket = io('http://hydro.azad.work:6001');

            var gate_popups = new Vue({ el: '#gate-popups' });

            function updateLine(id, data) {
                var chart = window["chart_line"+id];
                var date = new Date();
                if (chart != undefined) {
                    chart.data.labels.push(date.toLocaleTimeString());
                    if (chart.data.labels.length == 20) {
                        chart.data.labels.splice(0, 1); 
                    } 
                    chart.data.datasets.forEach((dataset, i) => {
                        dataset.data.push(data);
                        if (dataset.data.length == 20) {
                            dataset.data.splice(0,1);
                        } 
                    });
                    chart.update();
                }
            }

            function updateMeter(id, data) {
                try{
                    window["water_pie"+id].series[0].update({
                        pointStart: 0,
                        data: [data,]
                    }, true); //true / false to redraw

                }catch(e){

                }
            }



            socket.on('test-channel:App\\Events\\WaterLevelChanged', function(data) {
               var id = data.gate.id;
               window['bottle'+id].water_level = data.gate.water_level;
               if ( id == 1 ) {
                 window["gate_popups"].$refs.popup1.info = data.gate;
                 window["gate_popups"].$refs.popup1.gate_opened = data.gate.gate_open == 1 ? "opened" : "closed";
               } else if (id == 2){
                 window["gate_popups"].$refs.popup2.info = data.gate;
                 window["gate_popups"].$refs.popup2.gate_opened = data.gate.gate_open == 1 ? "opened" : "closed";
               } else if (id == 3){
                 window["gate_popups"].$refs.popup3.info = data.gate;
                 window["gate_popups"].$refs.popup3.gate_opened = data.gate.gate_open == 1 ? "opened" : "closed";
               } else {
                 window["gate_popups"].$refs.popup4.info = data.gate;
                 window["gate_popups"].$refs.popup4.gate_opened = data.gate.gate_open == 1 ? "opened" : "closed";
               } 
               var progId = "#prog"+id;
                if (data.gate.water_level <= 40) {
                    moveProgressBar(progId, 'started', data.gate.water_level, id);
                } else if (data.gate.water_level > 40 && data.gate.water_level <= 50) {
                    moveProgressBar(progId, 'inProgress', data.gate.water_level, id);
                } else {
                    moveProgressBar(progId, 'completed', data.gate.water_level, id);
                }

                updateLine(id, data.gate.water_level);
                updateMeter(id, data.gate.water_level);
            });

        </script>
{{--         <script async defer src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_API_KEY')}}&callback=initMap"></script>
 --}}    </body>
</html>
