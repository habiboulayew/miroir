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
                <h1 class="h2">Gestion des documents</h1>
                <?php $this->load->view('layout/btn_logout') ?>
            </div>
            <div id="div_container">
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12" style="margin-bottom: 30px">
                        <button type="button" id="btn_add" class="btn btn-primary">Ajouter <span lass="m-l-5"><i
                                        class="fa fa-plus-square"></i></span></button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <table id="datatable" class="table table-striped table-bordered" style="width: 100%">
                            <thead>
                            <tr>
                                <th>Titre</th>
                                <th>filigrane</th>
                                <th>filigrane<br>identification</th>
                                <th>filigrane<br>Texte</th>
                                <th>Protection</th>
                                <th>Etat</th>
                                <th style="width: 1%"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($all_data as $value) { ?>
                                <tr>
                                    <td><?= $value->titre; ?></td>
                                    <td><?= $value->filigrane == '0' ? "<span class='text-danger'>Inactif</span>" : "<span class='text-success'>Actif</span>"; ?></td>
                                    <td><?= $value->filigrane_indentification == '0' ? "<span class='text-danger'>Inactif</span>" : "<span class='text-success'>Actif</span>"; ?></td>
                                    <td><?= $value->filigrane_texte; ?></td>
                                    <td><?= $value->password == '0' ? "<span class='text-danger'>Document ouvert</span>" : "<span class='text-success'>Document protégé</span>"; ?></td>
                                    <td><?= $value->etat == '0' ? "<span class='text-danger'>Inactif</span>" : "<span class='text-success'>Actif</span>"; ?></td>
                                    <td class="actions"
                                        style="width: 1%; text-align: center; white-space: nowrap">
                                        <a href="#" class="btn_edit" id='<?php echo $value->id_document; ?>'>
                                            <button type="button" class="btn btn-sm btn-primary"><span
                                                        data-feather="edit"></span> Modifier
                                            </button>
                                        </a>
                                        &nbsp;
                                        <a href="#" class="btn_delete" id='<?php echo $value->id_document; ?>'>
                                            <button type="button" class="btn btn-sm btn-danger"><span
                                                        data-feather="trash"></span> Supprimer
                                            </button>
                                        </a>

                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div> <!-- End Row -->
                </div>

                <!-- sample modal content -->
                <div id="modal_form" class="modal fade" tabindex="-1" role="dialog"
                     aria-labelledby="modal_formLabel"
                     aria-hidden="true">
                    <form action="#" id="form" class="form-horizontal" enctype="multipart/form-data">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modal_formLabel">Title</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="id_document" name="id_document"/>
                                    <div class="form-body">
                                        <div class="cssload-thecube" style="display: none;">
                                            <div class="cssload-cube cssload-c1"></div>
                                            <div class="cssload-cube cssload-c2"></div>
                                            <div class="cssload-cube cssload-c4"></div>
                                            <div class="cssload-cube cssload-c3"></div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-12">Titre<span
                                                        class="text-danger">*</span></label>
                                            <div class="col-md-12">
                                                <textarea name="titre" id="titre"
                                                          class="form-control" type="text" required></textarea>
                                            </div>
                                        </div>
                                        <!--<div class="form-group">
                                            <label class="control-label col-md-12">Description</label>
                                            <div class="col-md-12">
                                                    <textarea name="description" id="description"
                                                              class="form-control"></textarea>
                                            </div>
                                        </div>-->
                                        <div class="form-group">
                                            <label class="control-label col-md-12">Fichier(pdf)<span
                                                        class="text-danger">*</span></label>
                                            <div class="col-md-12">
                                                <input name="file_name" id="file_name"
                                                       class="form-control" type="file" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12" style="float: left">
                                                <input type="checkbox" name="filigrane" id="filigrane" value="1">
                                                filigrane
                                            </div>
                                            <br>
                                        </div>

                                        <div class="form-group div_filigrane" style="display: none;">
                                            <div class="col-md-12" style="float: left">
                                                <input type="checkbox" name="filigrane_indentification"
                                                       id="filigrane_indentification" value="1"> filigrane
                                                indentification (nom et prénom)
                                            </div>
                                            <br>
                                        </div>

                                        <div class="form-group div_filigrane" style="display: none;">
                                            <div class="col-md-12" style="float: left">
                                                <input type="checkbox" name="filigrane_confidentiel"
                                                       id="filigrane_confidentiel" value="1"> filigrane
                                                texte confidentialité
                                            </div>
                                            <br>
                                        </div>

                                        <div class="div_filigrane" style="display: none;">
                                            <label class="control-label col-md-12">filigrane texte (Exple: BROUILLON, PROVISOIRE etc.)</label>
                                            <div class="col-md-12" style="float: left">
                                                <input type="text" class="form-control" name="filigrane_texte"
                                                       id="filigrane_texte">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-12">Protection mot de passe<span
                                                        class="text-danger">*</span></label>
                                            <div style="padding-left: 10px">
                                                <select name="password" id="password"
                                                        class="form-control" required>
                                                    <option value="0">Aucune</option>
                                                    <option value="2">Email utilisateur</option>
                                                    <option value="1">Ultra sécurisé avec mot de passe complexe</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-12">Etat<span
                                                        class="text-danger">*</span></label>
                                            <div class="col-md-12">
                                                <select name="etat" id="etat"
                                                        class="form-control" required>
                                                    <option value="1">Actif</option>
                                                    <option value="0">Inactif</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-12">Profil autorisé pour lecture</label>
                                            <?php foreach ($all_data_profil as $val): ?>
                                                <div class="col-md-4" style="float: left">
                                                    <input type="checkbox" name="lst_id_profil[]"
                                                           value="<?= $val->id_profil ?>"
                                                           id="profil<?= $val->id_profil ?>"> <?= $val->libelle_profil ?>
                                                </div>
                                            <?php endforeach ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <input type="submit" class="btn btn-primary" value="Enregistrer"
                                           style="margin-bottom: 0px;"/>
                                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Fermer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div><!-- /.modal -->


            </div>
        </main>
    </div>
</div>
<?php $this->load->view('layout/footer') ?>
<script type="text/javascript">
    var menu_encours = 'menu_document';
    var id_div_container = 'div_container';
</script>
<script src="<?php echo base_url(); ?>assets/managing_ajax.js?v=0.0.3"></script>
<!-- sweetalert  -->
<link href="<?php echo base_url(); ?>assets/plugins/sweetalert/dist/sweetalert.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url(); ?>assets/plugins/sweetalert/dist/sweetalert.min.js"></script>
<!-- DataTables js && css -->
<link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="<?php echo base_url(); ?>assets/plugins/jquery-datatable/jquery.dataTables.js"></script>
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

        $('#datatable').managing_ajax({
            id_modal_form: 'modal_form',
            id_form: 'form',
            url_submit: "<?=site_url('C_document/save')?>",
            title_modal_add: 'Nouveau document',
            focus_add: 'titre',
            title_modal_edit: 'Edition de document',
            focus_edit: 'titre',
            url_edit: "<?=site_url('C_document/get_record')?>",
            url_delete: "<?=site_url('C_document/delete')?>",
        });

        $.fn.afterAdd = function (args) {
            $('#id_document').val('');
            $('#file_name').prop('disabled', false);
        };

        $.fn.afterEdit = function (args) {
            $('#file_name').prop('disabled', true);

            if($('#filigrane').is(':checked'))
                $('.div_filigrane').show();
            else
                $('.div_filigrane').hide();
        };


        $.fn.beforeSave = function (args) {
            $('.cssload-thecube').show();
        };

        $.fn.afterSave = function (args) {
            $('.cssload-thecube').hide();
            if (args.data.status == 'success') {
                window.location = "<?=site_url('C_document')?>"
                //$('#menu_personnel').click();
            }
        };

        $.fn.afterDelete = function (args) {
            if (args.data.status == 'success') {
                window.location = "<?=site_url('C_document')?>"
            }
        };

        $('#filigrane').click(function () {
            if($(this).is(':checked'))
            {
                $('.div_filigrane').show();
            }
            else{
                $('.div_filigrane').hide();
                $('#filigrane_texte').val("");
                $('#filigrane_indentification,#filigrane_confidentiel').prop("checked",false);
            }
        });

    });
</script>
</body>
</html>