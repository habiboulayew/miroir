<!doctype html>
<html lang="en">
<head>
    <?php $this->load->view('layout/header') ?>
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
                <h1 class="h2">Changement de mot de passe</h1>
                <?php $this->load->view('layout/btn_logout') ?>
            </div>
            <div id="div_container">
                <form action="#" id="form" class="form-horizontal">
                    <div class="form-body" id="div-password">
                        <div class="col-md-6" style="border: 1px solid #ccc; padding: 10px">
                            <div class="form-group">
                                 <div class="cssload-thecube" id="div_loading" style="display: none;">
                                    <div class="cssload-cube cssload-c1"></div>
                                    <div class="cssload-cube cssload-c2"></div>
                                    <div class="cssload-cube cssload-c4"></div>
                                    <div class="cssload-cube cssload-c3"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-12">Ancien mot de passe<span
                                            class="text-danger">*</span></label>
                                <div class="col-md-12">
                                    <input name="oldPassword" id="oldPassword"
                                           class="form-control" type="password" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-12">Nouveau mot de passe (8 caractéres minimum)<span
                                            class="text-danger">*</span></label>
                                <div class="col-md-12">
                                    <input name="newPassword" id="newPassword"
                                           class="form-control" type="password" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-12">Confirmer le nouveau mot de passe<span
                                            class="text-danger">*</span></label>
                                <div class="col-md-12">
                                    <input name="confirmPassword" id="confirmPassword"
                                           class="form-control" type="password" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="alert alert-danger" id="div-alerte" style="display: none; text-align: center;">ss
                                </div>
                                <div class="alert alert-success" id="div-success" style="display: none; text-align: center;">qq
                                </div>
                            </div>
                            <div class="form-group" style="text-align: right;padding-right: 15px;">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <span data-feather="save"></span> Valider
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
<?php $this->load->view('layout/footer') ?>
<script type="text/javascript">
    var menu_encours = 'menu_password';
    var id_div_container = 'div_container';
</script>
<script src="<?php echo base_url(); ?>assets/managing_ajax.js?v=0.0.3"></script>
<!-- sweetalert  -->
<link href="<?php echo base_url(); ?>assets/plugins/sweetalert/dist/sweetalert.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>assets/plugins/sweetalert/dist/sweetalert.min.js"></script>
<!-- jQuery Validate Plugin -->
<link href="<?php echo base_url(); ?>assets/plugins/jquery-validation/src/jquery.validate.css" rel="stylesheet"
      type="text/css"/>
<script src="<?php echo base_url(); ?>assets/plugins/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/jquery-validation/src/localization/messages_fr.js"></script>
<!-- notification js-->
<link href="<?php echo base_url(); ?>assets/plugins/notifications/notification.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/plugins/notifyjs/dist/notify.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/notifications/notify-metro.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/notifications/notifications.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $('#div-password').divcrud({
            id_form: 'form',
            url_submit: "<?php echo site_url('C_personnel/save_password')?>"
        });

        $('#oldPassword').focus();

        $.fn.beforeSave_divcrud = function () {

            $('#div-alerte').html('').hide();
            $('#div-success').html('').hide();

            if ($('#newPassword').val().length < 8) {
                $.Notification.autoHideNotify('error', 'bottom right', 'Alerte', '8 caractéres minimum requis pour le mot de passe.');
                $('#div-alerte').html('8 caractéres minimum requis pour le mot de passe.').show();
                $('#newPassword').focus();
                return false;
            }
            else if ($('#newPassword').val() != $('#confirmPassword').val()) {
                $.Notification.autoHideNotify('error', 'bottom right', 'Alerte', 'Le nouveau mot de passe saisie et la confirmation doivent être identique.');
                $('#div-alerte').html('Le nouveau mot de passe saisie et la confirmation doivent être identiques.').show();
                $('#confirmPassword').focus();
                return false;
            }

            $('#div_loading').show();
        };


        $.fn.afterSave_divcrud = function(args)
        {
            if(args.data.status == 'error')
            {
                $('#div-success').html('').hide();
                $('#div-alerte').html(args.data.message).show();
            }
            else
            {
                $('#oldPassword,#newPassword,#confirmPassword').val('');
                $('#div-alerte').html('').hide();
                $('#div-success').html(args.data.message).show();
            }
            $('#div_loading').hide();
        }


    });
</script>
</body>
</html>