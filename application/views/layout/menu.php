<style type="text/css">
    .user-details {
        min-height: 80px;
        padding: 20px;
        position: relative;
    }

    .user-details img {
        position: relative;
        z-index: 9999;
    }

    .thumb-md {
        height: 48px;
        width: 48px;
    }

    .img-circle {
        border-radius: 50%;
    }

    .user-details .user-info {
        color: #444444;
        margin-left: 60px;
        position: relative;
        z-index: 99999;
    }

    .user-details .user-info a.dropdown-toggle {
        color: #494e54;
        display: block;
        font-family: 'Roboto', sans-serif;
        font-size: 16px;
        font-weight: 600;
        padding-top: 5px;
    }

    .dropdown, .dropup {
        position: relative;
    }
</style>
<div class="user-details">
    <div class="pull-left" style="float: left;">
        <img src="<?=base_url("images/male.png")?>" alt=""
             class="thumb-md img-circle" style="border: 2px solid #edf0f0;">
    </div>
    <div class="user-info">
        <div class="dropdown">
            <a href="#" class="dropdown-toggle  text-uppercase" aria-expanded="false"
               style="white-space: inherit;">
                <?=$this->session->prenom?> <?=$this->session->nom?></a>
        </div>
        <p class="text-muted m-0"><?=$this->session->fonction?></p>
    </div>
</div>
<?php $id = empty($id) ? '' : $id ?>
<?php if($this->session->sso == false): ?>
<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link <?= $id == 'password' ? 'active' : '' ?>"  href="<?= site_url('C_personnel/password') ?>">
            <span data-feather="alert-octagon"></span>
            Changement de mot de passe
        </a>
    </li>
</ul>
<?php endif; ?>
<hr>

<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
    <span>Documents et manuels</span>
    <span data-feather="plus-circle"></span>
</h6>
<?php $password = empty($password) ? '' : $password ?>
<ul class="nav flex-column mb-2">
    <?php if(!empty($this->session->data_menu)): ?>
        <?php foreach ($this->session->data_menu as $menu): ?>
            <li class="nav-item">
                
                <a class="nav-link <?= $id == $menu->id_document ? 'active' : '' ?>" href="<?= site_url("Document/index/$menu->id_document/$menu->password/") ?>">
                
<!--
<a class="nav-link <?//= $id == $menu->id_document ? 'active' : '' ?>" href="<?//= site_url("C_document/afficher_pdf/$menu->id_document") ?>">
-->
                    <?=($menu->password == '1' || $menu->password == '2')? '<span data-feather="lock"></span>' : '<span data-feather="file-text"></span>' ?>
                    <?=$menu->titre?>
                </a>
            </li>
        <?php endforeach ?>
    <?php endif; ?>
</ul>
<?php if($this->session->id_profil == '1'): ?>
<hr>
<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
    <span>Administration</span>
    <span data-feather="plus-circle"></span>
</h6>
<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link <?= $id == 'document' ? 'active' : '' ?>""  href="<?= site_url('C_document') ?>">
            <span data-feather="folder-plus"></span>
            Gestion des documents
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $id == 'personnel' ? 'active' : '' ?>"" href="<?= site_url('C_personnel') ?>">
            <span data-feather="user"></span>
            Gestion des participants
        </a>
    </li>
     <li class="nav-item">
        <a class="nav-link <?= $id == 'agenda_saisie' ? 'active' : '' ?>"" href="<?= site_url('C_agenda/saisie') ?>">
            <span data-feather="calendar"></span>
            Gestion du chronogramme
        </a>
    </li>
</ul>
<?php endif; ?>
<hr>
<h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
    <span>Agenda</span>
    <span data-feather="plus-circle"></span>
</h6>
<ul class="nav flex-column">
    <li class="nav-item">
        <a class="nav-link <?= $id == 'agenda' ? 'active' : '' ?>" href="<?= site_url('C_agenda') ?>">
            <span data-feather="folder-plus"></span>
            planning de activités du Mouvement national <?= date('Y') ?>
        </a>
    </li>
</ul>
