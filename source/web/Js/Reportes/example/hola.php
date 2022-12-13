

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title></title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="ECharts">
  <meta name="author" content="kener.linfeng@gmail.com">
  <title>ECharts · Example</title>
  <link rel="shortcut icon" href="../asset/ico/favicon.png">
  <!--<link href="../asset/css/font-awesome.min.css" rel="stylesheet"> -->
  <!--<link href="../asset/css/carousel.css" rel="stylesheet"> -->
  <!--  <script src="../asset/js/javascript.js"></script> -->
  <!-- <link href="../asset/css/codemirror.css" rel="stylesheet"> -->
<!--  <link href="../asset/css/monokai.css" rel="stylesheet">  -->
<!--  <link href="../asset/css/bootstrap.css" rel="stylesheet"> -->
  <link href="../asset/css/echartsHome.css" rel="stylesheet">
  <script src="./www/js/echarts.js"></script>
  <script src="../asset/js/codemirror.js"></script>

</head>
<body>
  <div class="container-fluid">
    <div class="row-fluid example">
      <div id="sidebar-code" hidden="true" class="col-md-4">
        <div class="well sidebar-nav">
          <div class="nav-header"><a href="#" onclick="autoResize()" class="glyphicon glyphicon-resize-full" id ="icon-resize" ></a>option</div>
          <textarea  id="code" name="code">
            option = {
              tooltip: {
                show: true,
                formatter: "{a} <br/>{b} : {c} ({d}%)"
              },
              legend: {
                orient: 'vertical',
                x: 'left',
                data: ['TOTAL', 'VIOLETA', 'AZUL', 'NARANJA', 'ROJO', 'GRIS', 'AMARILLO', 'VERDE', 'MARRON', 'ROSA']
              },
              toolbox: {
                show: true,
                feature: {
                  mark: {
                    show: true
                  },
                  dataView: {
                    show: true,
                    readOnly: false
                  },
                  restore: {
                    show: true
                  },
                  saveAsImage: {
                    show: true
                  }
                }
              },
              calculable: true,
              series: [ {
                name: 'CantInspDiaSolicit',
                type: 'pie',
                clockWise: true,
                startAngle: 135,
                center: ['20%', 200],
                radius: [80, 120],
                itemStyle: 　{
                  normal: {
                    label: {
                      show: false
                    },
                    labelLine: {
                      show: false
                    }
                  },
                  emphasis: {
                    color: (function() {
                      var zrColor = require('zrender/tool/color');
                      return zrColor.getRadialGradient(
                      650, 200, 80, 650, 200, 120, [
                      [0, 'rgba(255,255,0,1)'],
                      [1, 'rgba(255,0,0,1)']
                      ]
                      )
                    })(),
                    label: {
                      show: true,
                      position: 'center',
                      formatter: "{d}%",
                      textStyle: {
                        color: 'red',
                        fontSize: '30',
                        fontFamily: 'Open Sans',
                        fontWeight: 'bold'
                      }
                    }
                  }
                },
                data: [{
                  value: 335,
                  name: 'TOTAL'
                }, {
                  value: 310,
                  name: 'NARANJA'
                }, {
                  value: 234,
                  name: 'ROJO'
                }, {
                  value: 135,
                  name: 'GRIS'
                }, {
                  value: 1548,
                  name: 'AZUL'
                }],
                markPoint: {
                  symbol: 'star',
                  data: [{
                    name: 'Max',
                    value: 1548,
                    x: '25%',
                    y: 50,
                    symbolSize: 32
                  }]
                }
              },
              {
                name: 'CantInspSemanaSolicit',
                type: 'pie',
                clockWise: true,
                startAngle: 135,
                center: ['40%', 200],
                radius: [80, 120],
                itemStyle: 　{
                  normal: {
                    label: {
                      show: false
                    },
                    labelLine: {
                      show: false
                    }
                  },
                  emphasis: {
                    color: (function() {
                      var zrColor = require('zrender/tool/color');
                      return zrColor.getRadialGradient(
                      650, 200, 80, 650, 200, 120, [
                      [0, 'rgba(255,255,0,1)'],
                      [1, 'rgba(255,0,0,1)']
                      ]
                      )
                    })(),
                    label: {
                      show: true,
                      position: 'center',
                      formatter: "{d}%",
                      textStyle: {
                        color: 'red',
                        fontSize: '30',
                        fontFamily: 'Open Sans',
                        fontWeight: 'bold'
                      }
                    }
                  }
                },
                data: [{
                  value: 335,
                  name: 'TOTAL'
                }, {
                  value: 310,
                  name: 'NARANJA'
                }, {
                  value: 234,
                  name: 'ROJO'
                }, {
                  value: 135,
                  name: 'GRIS'
                }, {
                  value: 1548,
                  name: 'AZUL'
                }],
                markPoint: {
                  symbol: 'star',
                  data: [{
                    name: 'Max',
                    value: 1548,
                    x: '50%',
                    y: 50,
                    symbolSize: 32
                  }]
                }
              },
              {
                name: 'CantInspMesSolicit',
                type: 'pie',
                clockWise: true,
                startAngle: 135,
                center: ['60%', 200],
                radius: [80, 120],
                itemStyle: 　{
                  normal: {
                    label: {
                      show: false
                    },
                    labelLine: {
                      show: false
                    }
                  },
                  emphasis: {
                    color: (function() {
                      var zrColor = require('zrender/tool/color');
                      return zrColor.getRadialGradient(
                      650, 200, 80, 650, 200, 120, [
                      [0, 'rgba(255,255,0,1)'],
                      [1, 'rgba(255,0,0,1)']
                      ]
                      )
                    })(),
                    label: {
                      show: true,
                      position: 'center',
                      formatter: "{d}%",
                      textStyle: {
                        color: 'red',
                        fontSize: '30',
                        fontFamily: 'Open Sans',
                        fontWeight: 'bold'
                      }
                    }
                  }
                },
                data: [{
                  value: 335,
                  name: 'TOTAL'
                }, {
                  value: 310,
                  name: 'NARANJA'
                }, {
                  value: 234,
                  name: 'ROJO'
                }, {
                  value: 135,
                  name: 'GRIS'
                }, {
                  value: 1548,
                  name: 'AZUL'
                }],
                markPoint: {
                  symbol: 'star',
                  data: [{
                    name: 'Max',
                    value: 1548,
                    x: '70%',
                    y: 50,
                    symbolSize: 32
                  }]
                }
              },
              {
                name: 'CantInspDiaRealiz',
                type: 'pie',
                clockWise: true,
                startAngle: 135,
                center: ['80%', 200],
                radius: [80, 120],
                itemStyle: 　{
                  normal: {
                    label: {
                      show: false
                    },
                    labelLine: {
                      show: false
                    }
                  },
                  emphasis: {
                    color: (function() {
                      var zrColor = require('zrender/tool/color');
                      return zrColor.getRadialGradient(
                      650, 200, 80, 650, 200, 120, [
                      [0, 'rgba(255,255,0,1)'],
                      [1, 'rgba(255,0,0,1)']
                      ]
                      )
                    })(),
                    label: {
                      show: true,
                      position: 'center',
                      formatter: "{d}%",
                      textStyle: {
                        color: 'red',
                        fontSize: '30',
                        fontFamily: 'Open Sans',
                        fontWeight: 'bold'
                      }
                    }
                  }
                },
                data: [{
                  value: 335,
                  name: 'TOTAL'
                }, {
                  value: 310,
                  name: 'NARANJA'
                }, {
                  value: 234,
                  name: 'ROJO'
                }, {
                  value: 135,
                  name: 'GRIS'
                }, {
                  value: 1548,
                  name: 'AZUL'
                }],
                markPoint: {
                  symbol: 'star',
                  data: [{
                    name: 'Max',
                    value: 1548,
                    x: '90%',
                    y: 50,
                    symbolSize: 32
                  }]
                }
              }
              ]
            };
          </textarea>
        </div><!--/.well -->
      </div><!--/span-->
      <div id="graphic" class="col-md-8">
        <div id="main" class="main"></div>
        <div>

          <button type="button" class="btn btn-sm btn-success" onclick="refresh(true)">Actualizar</button>
          <span class="text-primary">Color</span>
          <select id="theme-select"></select>

          <span id='wrong-message' style="color:red"></span>
        </div>
      </div><!--/span-->
    </div><!--/row-->

  </div><!--/.fluid-container-->
</body>

<script src="../asset/js/jquery.min.js"></script>
<!-- <script type="text/javascript" src="../asset/js/echartsHome.js"></script> -->
<!-- <script src="../asset/js/bootstrap.min.js"></script> -->
<script src="../asset/js/echartsExample.js"></script>
</html>
