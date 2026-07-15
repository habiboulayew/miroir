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
                <h1 class="h2">Gestion des paticipants</h1>
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
                                    <th>IEN</th>
                                    <th>Matricule</th>
                                    <th>Prénom</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Structure</th>
                                    <th>Fonction</th>
                                    <th>Mot de passe</th>
                                    <th>Etat</th>
                                    <th style="width: 1%"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($all_data as $value) { ?>
                                    <tr>
                                        <td><?= $value->ien; ?></td>
                                        <td><?= $value->matricule; ?></td>
                                        <td><?= $value->prenom; ?></td>
                                        <td><?= $value->nom; ?></td>
                                        <td><?= $value->email; ?></td>
                                        <td><?= $value->code_str; ?></td>
                                        <td><?= $value->fonction; ?></td>
                                        <td><?= $value->password; ?></td>
                                        <td><?= $value->etat == '0' ? "<span class='text-danger'>Inactif</span>" : "<span class='text-success'>Actif</span>"; ?></td>
                                        <td class="actions" style="width: 1%; text-align: center; white-space: nowrap">
                                            <a href="#" class="btn_edit" id='<?php echo $value->id_personnel; ?>'>
                                                <button type="button" class="btn btn-sm btn-primary"><span
                                                            data-feather="edit"></span> Modifier
                                                </button>
                                            </a>
                                            &nbsp;
                                            <a href="#" class="btn_delete" id='<?php echo $value->id_personnel; ?>'>
                                                <button type="button" class="btn btn-sm btn-danger"><span
                                                            data-feather="trash"></span> Supprimer
                                                </button>
                                            </a>
                                            
                                            &nbsp;
                                            <a href="#" class="btn_reinitialise" id='<?php echo $value->id_personnel; ?>'>
                                                <button type="button" class="btn btn-sm btn-warning"><span
                                                            data-feather="lock"></span> Reinitialiser le mot de passe
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
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modal_formLabel">Title</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="id_personnel" name="id_personnel"/>
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-12 div_search_ien">
                                                <div class="form-group">
                                                    <div class="col-md-8" style="float: left;">
                                                        <label for="ien_search">Entrer l'IEN ou l'email pro à
                                                            rechercher</label>
                                                        <input name="" type="text" id="ien_search" class="form-control"
                                                               placeholder="IEN / Email pro">
                                                    </div>
                                                    <div class="col-md-2" style="float: left">
                                                        <label style="color: white">_</label><br>
                                                        <a href="#" id="btnSearchIEN"
                                                           class="btn btn-success">Rechercher</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 clearfix div_search_ien">
                                                <div class="cssload-thecube" id="div_loading" style="display: none;">
                                                    <div class="cssload-cube cssload-c1"></div>
                                                    <div class="cssload-cube cssload-c2"></div>
                                                    <div class="cssload-cube cssload-c4"></div>
                                                    <div class="cssload-cube cssload-c3"></div>
                                                </div>
                                                <hr>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-12">Matricule<span
                                                                class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        <input name="matricule" id="matricule"
                                                               class="form-control" type="text" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-12">Prénom<span
                                                                class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        <input name="prenom" id="prenom"
                                                               class="form-control" type="text" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-12">Nom<span class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        <input name="nom" id="nom"
                                                               class="form-control" type="text" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-12">Téléphone</label>
                                                    <div class="col-md-12">
                                                        <input name="telephone" id="telephone"
                                                               class="form-control" type="text">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-12">Email<span
                                                                class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        <input name="email" id="email"
                                                               class="form-control" type="text" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-12">Structure<span
                                                                class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        <select name="code_str" id="code_str" class="form-control"
                                                                required>
                                                            <option value=""></option>
                                                            <option value="MEN">MEN</option>
                                                            <option value="MEFPA">MEFPA</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label col-md-12">Fonction<span
                                                                class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        <input name="fonction" id="fonction"
                                                               class="form-control" type="text" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-12">Profil<span
                                                                class="text-danger">*</span></label>
                                                    <div class="col-md-12">
                                                        <select name="id_profil" id="id_profil"
                                                                class="form-control" required>
                                                            <?= $select_profil ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-12">Mot de passe</label>
                                                    <div class="col-md-12">
                                                        <input name="password" id="password"
                                                               class="form-control" type="password" value="drh@2020">
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
                                                    <label class="control-label col-md-12">IEN</label>
                                                    <div class="col-md-12">
                                                        <input name="ien" id="ien"
                                                               class="form-control" type="text">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-12">Email professionel</label>
                                                    <div class="col-md-12">
                                                        <input name="email_pro" id="email_pro"
                                                               class="form-control" type="text">
                                                    </div>
                                                </div>
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
    var menu_encours = 'menu_personnel';
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
            url_submit: "<?=site_url('C_personnel/save')?>",
            title_modal_add: 'Nouveau paticipant',
            focus_add: 'ien_search',
            title_modal_edit: 'Edition de paticipant',
            focus_edit: 'matricule',
            url_edit: "<?=site_url('C_personnel/get_record')?>",
            url_delete: "<?=site_url('C_personnel/delete')?>",
        });

        $.fn.afterAdd = function (args) {
            $('#id_personnel').val('');
            $('.div_search_ien').show();
        };

        $.fn.afterEdit = function (args) {
            $('.div_search_ien').hide();
        };

        $.fn.afterSave = function (args) {
            if (args.data.status == 'success') {
                window.location = "<?=site_url('C_personnel')?>"
            }
        };

        $.fn.afterDelete = function (args) {
            if (args.data.status == 'success') {
                window.location = "<?=site_url('C_personnel')?>"
            }
        };

        $('#btnSearchIEN').click(function () {
            $('#matricule,#prenom,#nom,#telephone,#email,#code_str,#fonction,#id_profil,#password,#ien,#email_pro').val('');
            var lval = $('#ien_search').val();
            if (lval == "") {
                alert("Veillez indiquer une valeur de recherche !!!");
            }
            else {
                $('#div_loading').show();
                $.ajax({
                    type: "POST",
                    url: "<?=site_url('C_personnel/getSearchIEN')?>",
                    data: { value: lval },
                    dataType: "json",
                    success: function (data) {
                        if (data.code == '0') {
                            $('#code_str').val('MEN');
                            if (data.record.code_compte == undefined) {
                                $('#ien').val(data.record.ien);
                                $('#nom').val(data.record.nom);
                                $('#prenom').val(data.record.prenom);
                                $('#email_pro').val(data.record.email_pro);
                                $('#email').val(data.record.email);
                            }
                            else {
                                $('#ien').val(data.record.code_compte);
                                $('#nom').val(data.record.nom_compte);
                                $('#prenom').val(data.record.prenom + " " + data.record.nom);
                                $('#email_pro').val(data.record.mail_compte);
                                $('#email').val(data.record.email_pro);
                            }

                            $('#telephone').val(data.record.tel);
                            if(data.record.matricule != undefined && data.record.matricule != '')
                                $('#matricule').val(data.record.matricule);
                            else
                                $('#matricule').val(data.record.ien);
                        }
                        else
                            alert("Aucun enrégistrement trouvé !!!");
                        $('#div_loading').hide();
                    },
                    error: function() {
                        alert('Error adding / update data');
                    }
                });
            }
        });


$(document).on('click', '.btn_reinitialise', function (e) {
    e.preventDefault();
    var id_personnel = $(this).attr('id');

    swal({
        title: "Confirmer la réinitialisation",
        text: "Un nouveau mot de passe sera généré et envoyé par SMS à cet utilisateur. Continuer ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#f0ad4e",
        confirmButtonText: "Oui, réinitialiser",
        cancelButtonText: "Annuler"
    }, function (isConfirm) {
        if (isConfirm) {
            $.ajax({
                type: "POST",
                url: "<?=site_url('C_personnel/reset_password')?>",
                data: { id_personnel: id_personnel },
                dataType: "json",
                success: function (data) {
                    if (data.status == 'success') {
                        swal({
                            title: "Succès",
                            text: data.message,
                            type: "success"
                        }, function () {
                            // Recharge la page une fois le swal fermé, pour voir l'état à jour
                            window.location = "<?=site_url('C_personnel')?>";
                        });
                    } else {
                        swal("Erreur", data.message, "error");
                    }
                },
                error: function () {
                    swal("Erreur", "Une erreur est survenue lors de la réinitialisation.", "error");
                }
            });
        }
    });
});

    });
</script>
</body>
</html>