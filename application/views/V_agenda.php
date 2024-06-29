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
                        <jqx-scheduler settings="schedulerSettings"></jqx-scheduler>
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
<script>
     var lheight = 700;
    var lwidth = $(window).width() - 350;

    var appointments = new Array();

    <?php   foreach($all_data as  $val):
        $now = new DateTime();
        $date_debut = new DateTime($val->date_debut);
        $date_fin = new DateTime($val->date_fin);

        $annee_now = $now->format('Y');
        $mois_now = (int)$now->format('m');
        $jours_now = (int)$now->format('d');

        $annee_debut = $date_debut->format('Y');
        $mois_debut = (int)$date_debut->format('m')-1;
        $jours_debut = (int)$date_debut->format('d');
        $heure_debut = (int)$date_debut->format('H');
        $minute_debut = (int)$date_debut->format('i');
        $seconde_debut = (int)$date_debut->format('s');


        $annee_fin = $date_fin->format('Y');
        $mois_fin = (int)$date_fin->format('m')-1;
        $jours_fin = (int)$date_fin->format('d');
        $heure_fin = (int)$date_fin->format('H');
        $minute_fin = (int)$date_fin->format('i');
        $seconde_fin = (int)$date_fin->format('s');
    ?>
        appointments.push({
            id: "id<?=$val->id_agenda?>",
            description: "<?=$val->description?>",
            location: "<?=$val->lieu?>",
            subject: "<?=$val->objet?> <?=empty($val->responsable) ? '' : '[ '.$val->responsable.' ]'?>",
            calendar: "<?=$val->libelle_type_activite?>",
            start: new Date(<?=$annee_debut?>, <?=$mois_debut?>, <?=$jours_debut?>, <?=$heure_debut?>, <?=$minute_debut?>, <?=$seconde_debut?>),
            end: new Date(<?=$annee_fin?>,  <?=$mois_fin?>, <?=$jours_fin?>, <?=$heure_fin?>, <?=$minute_fin?>, <?=$seconde_fin?>)
        });
    <?php endforeach; ?>

    var source =
        {
            dataType: 'array',
            dataFields: [
                {name: 'id', type: 'string'},
                {name: 'description', type: 'string'},
                {name: 'location', type: 'string'},
                {name: 'subject', type: 'string'},
                {name: 'calendar', type: 'string'},
                {name: 'start', type: 'date'},
                {name: 'end', type: 'date'}
            ],
            id: 'id',
            localData: appointments
        };

    <?php   $now = new DateTime(); ?>
    JQXElements.settings['schedulerSettings'] =
        {
            date: new jqx.date(<?=$annee_now?>, <?=$mois_now?>, <?=$jours_now?>),
            width: lwidth,
            height: lheight,
            source: new jqx.dataAdapter(source),
            view: 'agendaView',
            showLegend: true,
            //disabled: true,
            editDialog: false,
            contextMenu: false,
            ready: function () {
                var myScheduler = document.querySelector('jqx-scheduler');
                myScheduler.ensureAppointmentVisible('id1');
            },
            resources:
                {
                    colorScheme: 'scheme05',
                    dataField: 'calendar',
                    source: new jqx.dataAdapter(source)
                },
            appointmentDataFields:
                {
                    from: 'start',
                    to: 'end',
                    id: 'id',
                    description: 'description',
                    location: 'place',
                    subject: 'subject',
                    resourceId: 'calendar'
                },
            views:
                [
                    'dayView',
                    'weekView',
                    'monthView',
                    'agendaView'
                ],
            localization: {
                    // separator of parts of a date (e.g. '/' in 11/05/1955)
                    '/': "/",
                    // separator of parts of a time (e.g. ':' in 05:44 PM)
                    ':': ":",
                    // the first day of the week (0 = Sunday, 1 = Monday, etc)
                    firstDay: 1,
                    days: {
                        names: ["dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi"],
                        namesAbbr: ["dim.","lun.","mar.","mer.","jeu.","ven.","sam."],
                        namesShort: ["di","lu","ma","me","je","ve","sa"]
                    },
                    months: {
                        names: ["janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre",""],
				        namesAbbr: ["janv.","févr.","mars","avr.","mai","juin","juil.","août","sept.","oct.","nov.","déc.",""]
                    },
                    // AM and PM designators in one of these forms:
                    // The usual view, and the upper and lower case versions
                    //      [standard,lowercase,uppercase]
                    // The culture does not use AM or PM (likely all standard date formats use 24 hour time)
                    //      null
                    AM: null,
			        PM: null,
                    eras: [{"name":"ap. J.-C.","start":null,"offset":0}],
                    twoDigitYearMax: 2029,
                    patterns: {
                        d: "M/d/yyyy",
                        D: "dddd, MMMM dd, yyyy",
                        t: "h:mm tt",
                        T: "h:mm:ss tt",
                        f: "dddd, MMMM dd, yyyy h:mm tt",
                        F: "dddd, MMMM dd, yyyy h:mm:ss tt",
                        M: "MMMM dd",
                        Y: "yyyy MMMM",
                        S: "yyyy\u0027-\u0027MM\u0027-\u0027dd\u0027T\u0027HH\u0027:\u0027mm\u0027:\u0027ss",
                        ISO: "yyyy-MM-dd hh:mm:ss",
                        ISO2: "yyyy-MM-dd HH:mm:ss",
                        d1: "dd.MM.yyyy",
                        d2: "dd-MM-yyyy",
                        d3: "dd-MMMM-yyyy",
                        d4: "dd-MM-yy",
                        d5: "H:mm",
                        d6: "HH:mm",
                        d7: "HH:mm tt",
                        d8: "dd/MMMM/yyyy",
                        d9: "MMMM-dd",
                        d10: "MM-dd",
                        d11: "MM-dd-yyyy"
                    },
                    backString: "Précédent",
                    forwardString: "Suivant",
                    toolBarPreviousButtonString: "Précédent",
                    toolBarNextButtonString: "Suivant",
                    emptyDataString: "Aucune donnée à afficher",
                    loadString: "Chargement...",
                    clearString: "Effacer",
                    todayString: "Aujourd'hui",
                    dayViewString: "Jours",
                    weekViewString: "Semaine",
                    monthViewString: "Mois",
                    /*
                    timelineDayViewString: "Zeitleiste Day",
                    timelineWeekViewString: "Zeitleiste Woche",
                    timelineMonthViewString: "Zeitleiste Monat",
                    loadingErrorMessage: "Die Daten werden noch geladen und Sie können eine Eigenschaft nicht festgelegt oder eine Methode aufrufen . Sie können tun, dass, sobald die Datenbindung abgeschlossen ist. jqxScheduler wirft die ' bindingComplete ' Ereignis, wenn die Bindung abgeschlossen ist.",
                    editRecurringAppointmentDialogTitleString: "Bearbeiten Sie wiederkehrenden Termin",
                    editRecurringAppointmentDialogContentString: "Wollen Sie nur dieses eine Vorkommen oder die Serie zu bearbeiten ?",
                    editRecurringAppointmentDialogOccurrenceString: "Vorkommen bearbeiten",
                    editRecurringAppointmentDialogSeriesString: "Bearbeiten Die Serie",
                    editDialogTitleString: "Termin bearbeiten",
                    editDialogCreateTitleString: "Erstellen Sie Neuer Termin",
                    contextMenuEditAppointmentString: "Termin bearbeiten",
                    contextMenuCreateAppointmentString: "Erstellen Sie Neuer Termin",
                    editDialogSubjectString: "Subjekt",
                    editDialogLocationString: "Ort",
                    editDialogFromString: "Von",
                    editDialogToString: "Bis",
                    editDialogAllDayString: "Den ganzen Tag",
                    editDialogExceptionsString: "Ausnahmen",
                    editDialogResetExceptionsString: "Zurücksetzen auf Speichern",
                    editDialogDescriptionString: "Bezeichnung",
                    editDialogResourceIdString: "Kalender",
                    editDialogStatusString: "Status",
                    editDialogColorString: "Farbe",
                    editDialogColorPlaceHolderString: "Farbe wählen",
                    editDialogTimeZoneString: "Zeitzone",
                    editDialogSelectTimeZoneString: "Wählen Sie Zeitzone",
                    editDialogSaveString: "Sparen",
                    editDialogDeleteString: "Löschen",
                    editDialogCancelString: "Abbrechen",
                    editDialogRepeatString: "Wiederholen",
                    editDialogRepeatEveryString: "Wiederholen alle",
                    editDialogRepeatEveryWeekString: "woche(n)",
                    editDialogRepeatEveryYearString: "Jahr (en)",
                    editDialogRepeatEveryDayString: "Tag (e)",
                    editDialogRepeatNeverString: "Nie",
                    editDialogRepeatDailyString: "Täglich",
                    editDialogRepeatWeeklyString: "Wöchentlich",
                    editDialogRepeatMonthlyString: "Monatlich",
                    editDialogRepeatYearlyString: "Jährlich",
                    editDialogRepeatEveryMonthString: "Monate (n)",
                    editDialogRepeatEveryMonthDayString: "Day",
                    editDialogRepeatFirstString: "erste",
                    editDialogRepeatSecondString: "zweite",
                    editDialogRepeatThirdString: "dritte",
                    editDialogRepeatFourthString: "vierte",
                    editDialogRepeatLastString: "letzte",
                    editDialogRepeatEndString: "Ende",
                    editDialogRepeatAfterString: "Nach",
                    editDialogRepeatOnString: "Am",
                    editDialogRepeatOfString: "von",
                    editDialogRepeatOccurrencesString: "Eintritt (e)",
                    editDialogRepeatSaveString: "Vorkommen Speichern",
                    editDialogRepeatSaveSeriesString: "Save Series",
                    editDialogRepeatDeleteString: "Vorkommen löschen",
                    editDialogRepeatDeleteSeriesString: "Series löschen",
                    editDialogStatuses:
                    {
                        free: "Frei",
                        tentative: "Versuchsweise",
                        busy: "Beschäftigt",
                        outOfOffice: "Ausserhaus"
                    }*/
                },
        };
</script>
</body>
</html>
