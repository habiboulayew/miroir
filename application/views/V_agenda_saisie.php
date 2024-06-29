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
                <h1 class="h2">Gestion de chronogramme</h1>
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
                    <div class="table-responsive">
                        <div class="col-md-12">
                            <table id="datatable" class="table table-striped table-bordered table-condensed"
                                   style="width: 100%">
                                <thead>
                                <tr>
                                    <th>Objet</th>
                                    <th>Description</th>
                                    <th>Responsable</th>
                                    <th>Lieu</th>
                                    <th>Période du</th>
                                    <th>Au</th>
                                    <th>Type activité</th>
                                    <th>Etat</th>
                                    <th style="width: 1%"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($all_data as $value) { ?>
                                    <tr>
                                        <td><?= $value->objet; ?></td>
                                        <td><?= $value->description; ?></td>
                                        <td><?= $value->responsable; ?></td>
                                        <td><?= $value->lieu; ?></td>
                                        <td><?=date_heure_parse_en2fr($value->date_debut); ?></td>
                                        <td><?=date_heure_parse_en2fr($value->date_fin); ?></td>
                                        <td><?= $value->libelle_type_activite; ?></td>
                                        <td><?= $value->etat == '0' ? "<span class='text-danger'>Inactif</span>" : "<span class='text-success'>Actif</span>"; ?></td>
                                        <td class="actions" style="width: 1%; text-align: center; white-space: nowrap">
                                            <a href="#" class="btn_edit" id='<?php echo $value->id_agenda; ?>'>
                                                <button type="button" class="btn btn-sm btn-primary"><span
                                                            data-feather="edit"></span> Modifier
                                                </button>
                                            </a>
                                            &nbsp;
                                            <a href="#" class="btn_delete" id='<?php echo $value->id_agenda; ?>'>
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
                    </div> <!-- End Row -->
                </div>

                <!-- sample modal content -->
                <div id="modal_form" class="modal fade" tabindex="-1" role="dialog"
                     aria-labelledby="modal_formLabel"
                     aria-hidden="true">
                    <form action="#" id="form" class="form-horizontal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modal_formLabel">Title</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="id_agenda" name="id_agenda"/>
                                    <div class="form-body">

                                        <div class="form-group">
                                            <label class="control-label col-md-12">Objet<span class="text-danger">*</span></label>
                                            <div class="col-md-12">
                                                <input name="objet" id="objet"
                                                       class="form-control" type="text" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-12">Description</label>
                                            <div class="col-md-12">
                                                <textarea name="description" id="description"
                                                          class="form-control" type="text"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-12">Lieu</label>
                                            <div class="col-md-12">
                                                <input name="lieu" id="lieu"
                                                       class="form-control" type="text">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-12">Période du<span class="text-danger">*</span></label>
                                            <div class="col-md-12">
                                                <input name="date_debut" id="date_debut"
                                                       class="form-control" type="datetime-local" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-12">Au<span class="text-danger">*</span></label>
                                            <div class="col-md-12">
                                                <input name="date_fin" id="date_fin"
                                                       class="form-control" type="datetime-local" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-12">Responsable</label>
                                            <div class="col-md-12">
                                                <input name="responsable" id="responsable"
                                                       class="form-control" type="text">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-12">Type activité<span
                                                        class="text-danger">*</span></label>
                                            <div class="col-md-12">
                                                <select name="id_type_activite" id="id_type_activite" class="form-control"
                                                        required>
                                                   <?=$select_type_activite?>
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
    var menu_encours = 'menu_agenda_saisie';
    var id_div_container = 'div_container';
</script>
<script src="<?php echo base_url(); ?>assets/managing_ajax.js?v=0.0.2"></script>
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
            url_submit: "<?=site_url('C_agenda/save')?>",
            title_modal_add: 'Nouvelle activité',
            focus_add: 'objet',
            title_modal_edit: 'Edition activité',
            focus_edit: 'objet',
            url_edit: "<?=site_url('C_agenda/get_record')?>",
            url_delete: "<?=site_url('C_agenda/delete')?>",
        });

        $.fn.afterAdd = function (args) {
            $('#id_agenda').val('');
        };

        $.fn.afterEdit = function (args) {
        };

        $.fn.afterSave = function (args) {
            if (args.data.status == 'success') {
                window.location = "<?=site_url('C_agenda/saisie')?>"
            }
        };

        $.fn.afterDelete = function (args) {
            if (args.data.status == 'success') {
                window.location = "<?=site_url('C_agenda/saisie')?>"
            }
        };
    });
</script>
</body>
</html>