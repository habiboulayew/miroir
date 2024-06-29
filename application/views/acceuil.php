<!doctype html>
<html lang="en">
<head>
    <?php $this->load->view('layout/header') ?>
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script>
    <!-- notification js-->
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/plugins/notifications/notification.css" rel="stylesheet">
    <script src="<?php echo base_url(); ?>assets/plugins/notifyjs/dist/notify.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/notifications/notify-metro.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/notifications/notifications.js"></script>
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
                <!--<h1 class="h2">Espace de travail</h1>-->
                <!-- Target -->
                <?php if(!empty($id) && !empty($password) && $password == '1'): ?>
                    <button id="btn_copy_password" onclick="$.Notification.autoHideNotify('success', 'top right', 'Alerte', 'Mot de passe copié avec succées !');" class="btn btn-primary btn_copy_password"
                            style="font-weight: bolder;background-color: #2e93ff;"
                            data-clipboard-text="sA95HF:m@#w#1!è$ %e_ yvzm{}[]()/\'`~,;:.ee129<?=$id?>@#$%Gj9@#$%">
                       <span data-feather="unlock"></span> Cliquer ici pour copier le mot de passe<br> à utiliser pour accéder au fichier
                    </button>
                <?php elseif(!empty($id) && !empty($password) && $password == '2'): ?>
                    <button id="btn_copy_password" onclick="$.Notification.autoHideNotify('success', 'top right', 'Alerte', 'Mot de passe copié avec succées !');" class="btn btn-primary btn_copy_password"
                            style="font-weight: bolder;background-color: #2e93ff;"
                            data-clipboard-text="<?=$this->session->email_connexion?>">
                       <span data-feather="unlock"></span> Cliquer ici pour copier le mot de passe<br> Mot de passe: <?=$this->session->email_connexion?>
                    </button>
                <?php else: ?>
                    <h1 class="h2">Espace de travail</h1>
                <?php endif ?>
                <?php $this->load->view('layout/btn_logout') ?>
            </div>
            <div id="div_container" style="height: 800px"></div>
        </main>
    </div>
</div>
<script>
    new ClipboardJS('#btn_copy_password');
</script>
<?php $this->load->view('layout/footer') ?>
<script type="text/javascript">PDFObject.embed("<?=$file_path?>", "#div_container");</script>
<!--<script type="text/javascript">PDFObject.embed("./DATA/exemple.pdf?#toolbar=0&navpanes=0&scrollbar=0", "#div_contain");</script>-->
</body>
</html>