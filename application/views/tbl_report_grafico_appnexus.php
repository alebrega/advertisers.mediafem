<!-- GRAFICO REPORTES ************************************************************************* -->
<?php if($datos){ ?>
    <style type="text/css">
        #highcharts-0{
            overflow: visible !important;
        }

        #grafico_<?= $anunciante_id ?>{
            margin-bottom: 30px;
        }
    </style>

    <?php
    if($cant_datos > 12){
        $max = 12;
    }else{
        $max = $cant_datos - 1;
    }
    ?>

    <script type="text/javascript">
        $(function () {
            $('#grafico_<?= $anunciante_id ?>').highcharts({
                chart: {
                    marginTop: 50,
                    zoomType: 'xy'
                },
                title: {
                    text: '',
                    x: -20 //center
                },
                xAxis: {
                    categories: [<?= $graph_categories ?>],
                    min: 0,
                    max: <?= $max ?>
                },
                yAxis: {
                    title: {text: ''},
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                },
                tooltip: {
                    crosshairs: true,
                    valueSuffix: ''
                },
                legend: {
                    align: 'left',
                    verticalAlign: 'top',
                    floating: true,
                    x: 25,
                    y: 0,
                    borderWidth: 0
                },
                credits: {
                    enabled: true,
                    href: "https://www.mediafem.com",
                    text: "www.mediafem.com"
                },
                colors: ["#007FFF", "#00B200", "#FFC926"],
                series: [
                    <?php if($modalidad_de_compra == 'cpm'){ ?>
                    {
                    name: 'Impresiones',
                    data: [<?= $graph_imps ?>]
                    }
                    <?php } ?>
                    <?php if($modalidad_de_compra == 'cpc'){ ?>
                    {
                    name: 'Clicks',
                    data: [<?= $graph_clicks ?>]
                    }
                    <?php } ?>
                ],
                scrollbar: {
                    enabled: true,
                    barBackgroundColor: '#eee',
                    barBorderRadius: 2,
                    barBorderWidth: 0,
                    buttonBackgroundColor: '#eee',
                    buttonBorderWidth: 0,
                    buttonArrowColor: 'white',
                    buttonBorderRadius: 2,
                    rifleColor: 'white',
                    trackBackgroundColor: 'white',
                    trackBorderWidth: 1,
                    trackBorderColor: '#eee',
                    trackBorderRadius: 2
                }
            });
        });
    </script>

    <div id="grafico_<?= $anunciante_id ?>" style="height: 400px; min-width: 1125px;"></div>
<?php } ?>