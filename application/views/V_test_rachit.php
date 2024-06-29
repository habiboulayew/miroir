<!doctype html>
<html lang="en">
<head>
    <?php $this->load->view('layout/header') ?>
    <link rel="stylesheet" href="<?= base_url("assets/") ?>jqwidgets/styles/jqx.base.css" type="text/css"/>
    <link rel="stylesheet" href="<?= base_url("assets/") ?>jqwidgets/custom/styles/demos.css" type="text/css"/>
</head>
<body>
<?php $this->load->view('layout/top_bar') ?>
<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="sidebar-sticky">
                <?php $this->load->view('layout/menu') ?>
            </div>
        </nav>
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Planning de activités du Mouvement national <?= date('Y') ?></h1>
                <?php $this->load->view('layout/btn_logout') ?>
            </div>
            <div id="div_container">
                <div class="row">
                    <div class="col-md-12">
                        <div id="demoWidget" style="position: relative;">
                            <div style="float: left;" id="gaugeContainer"></div>
                            <div id="gaugeValue" style="position: absolute; top: 235px; left: 132px; font-family: Sans-Serif; text-align: center; font-size: 17px; width: 70px;"></div>
                            <div style="margin-left: 60px; float: left;" id="linearGauge"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php $this->load->view('layout/footer') ?>
<script type="text/javascript"  src="<?= base_url("assets/") ?>jqwidgets/custom/scripts/webcomponents-lite.min.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxcore.elements.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxbuttons.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxscrollbar.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxdata.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxdate.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxscheduler.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxscheduler.api.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxdatetimeinput.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxmenu.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxcalendar.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxtooltip.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxwindow.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxcheckbox.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxlistbox.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxdropdownlist.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxnumberinput.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxradiobutton.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/jqxinput.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>jqwidgets/globalization/globalize.js"></script>
<script type="text/javascript"   src="<?= base_url("assets/") ?>jqwidgets/globalization/globalize.culture.fr-FR.js"></script>

<script type="text/javascript" src="<?= base_url("assets/") ?>/jqwidgets/jqxdraw.js"></script>
<script type="text/javascript" src="<?= base_url("assets/") ?>/jqwidgets/jqxgauge.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#gaugeContainer').jqxGauge({
            ranges: [{ startValue: 0, endValue: 55, style: { fill: '#4bb648', stroke: '#4bb648' }, endWidth: 5, startWidth: 1 },
                { startValue: 55, endValue: 110, style: { fill: '#fbd109', stroke: '#fbd109' }, endWidth: 10, startWidth: 5 },
                { startValue: 110, endValue: 165, style: { fill: '#ff8000', stroke: '#ff8000' }, endWidth: 13, startWidth: 10 },
                { startValue: 165, endValue: 220, style: { fill: '#e02629', stroke: '#e02629' }, endWidth: 16, startWidth: 13 }],
            ticksMinor: { interval: 5, size: '5%' },
            ticksMajor: { interval: 10, size: '9%' },
            value: 0,
            colorScheme: 'scheme05',
            animationDuration: 1200
        });
        $('#gaugeContainer').on('valueChanging', function (e) {
            $('#gaugeValue').text(Math.round(e.args.value) + ' kph');
        });
        $('#gaugeContainer').jqxGauge('value', 140);
        $('#linearGauge').jqxLinearGauge({
            orientation: 'vertical',
            width: 100,
            height: 350,
            ticksMajor: { size: '10%', interval: 10 },
            ticksMinor: { size: '5%', interval: 2.5, style: { 'stroke-width': 1, stroke: '#aaaaaa'} },
            max: 60,
            pointer: { size: '5%' },
            colorScheme: 'scheme05',
            labels: { interval: 20, formatValue: function (value, position) {
                    if (position === 'far') {
                        value = (9 / 5) * value + 32;
                        if (value === -76) {
                            return '°F';
                        }
                        return value + '°';
                    }
                    if (value === -60) {
                        return '°C';
                    }
                    return value + '°';
                }
            },
            ranges: [
                { startValue: -10, endValue: 10, style: { fill: '#FFF157', stroke: '#FFF157'} },
                { startValue: 10, endValue: 35, style: { fill: '#FFA200', stroke: '#FFA200'} },
                { startValue: 35, endValue: 60, style: { fill: '#FF4800', stroke: '#FF4800'}}],
            animationDuration: 1500
        });
        $('#linearGauge').jqxLinearGauge('value', 40);
    });
</script>



</body>
</html>
