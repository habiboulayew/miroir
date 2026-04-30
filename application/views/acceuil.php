<!-- ====================================================================
     VUE D'ACCUEIL — Affichage du PDF avec barre de progression
     ====================================================================

     Cette vue gère l'affichage complet du document PDF généré par le
     contrôleur Document. Elle inclut :
     - Une barre latérale de navigation
     - Un overlay de chargement avec barre de progression
     - Un système de polling AJAX pour suivre la génération
     - L'affichage du PDF via la librairie PDFObject
     - Un bouton de copie de mot de passe si nécessaire

     ==================================================================== -->
<!doctype html>
<html lang="fr">
<head>
    <?php $this->load->view('layout/header') ?>

    <!-- PDFObject — une seule inclusion -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfobject/2.2.7/pdfobject.min.js"></script>

    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/plugins/notifications/notification.css" rel="stylesheet">
    <script src="<?php echo base_url(); ?>assets/plugins/notifyjs/dist/notify.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/notifications/notify-metro.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/notifications/notifications.js"></script>

    <!-- ════════════════════════════════════════════════════════════
         STYLES CSS — Overlay de chargement et barre de progression
         ════════════════════════════════════════════════════════════ -->
    <style>
        /* ── OVERLAY SEMI-TRANSPARENT (écran de chargement) ──────────── */
        #loading_overlay {
            display: none; /* Masqué par défaut */
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.93); /* Semi-transparent blanc */
            z-index: 9999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        /* Affiche l'overlay quand .actif est ajouté via JavaScript */
        #loading_overlay.actif {
            display: flex;
        }

        /* ── BOÎTE DE CONTENU (centrale) ─────────────────────────────── */
        #loading_box {
            background: #ffffff;
            border-radius: 14px;
            padding: 45px 55px;
            box-shadow: 0 6px 40px rgba(0, 0, 0, 0.13); /* Ombre douce */
            text-align: center;
            min-width: 420px;
            max-width: 520px;
        }

        /* Icône PDF (rouge) */
        #loading_box .fa-file-pdf-o {
            font-size: 56px;
            color: #e74c3c;
            margin-bottom: 18px;
            display: block;
        }

        /* Titre principal */
        #loading_box h5 {
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 6px;
            color: #2c3e50;
        }

        /* Sous-titre explicatif */
        #loading_box .sous_titre {
            color: #95a5a6;
            font-size: 13px;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        /* Séparateur visuel */
        #loading_box hr {
            border-color: #ecf0f1;
            margin: 20px 0;
        }

        /* ── BARRE DE PROGRESSION ────────────────────────────────────── */
        /* Conteneur de la barre (fond gris) */
        #barre_container .progress {
            height: 26px;
            border-radius: 20px;
            background: #ecf0f1;
            overflow: hidden;
        }

        /* Barre elle-même (bleu dégradé avec animation) */
        #barre_progress {
            height: 26px;
            line-height: 26px;
            border-radius: 20px;
            background: linear-gradient(90deg, #2e93ff 0%, #0056b3 100%); /* Dégradé bleu */
            color: #ffffff;
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            width: 0%; /* Commence à 0%, augmente via JavaScript */
            transition: width 0.5s ease; /* Animation fluide */
            min-width: 2em;
        }

        /* Message d'étape (Initialisation, Compression, etc.) */
        #barre_etape {
            margin-top: 10px;
            font-size: 12px;
            color: #7f8c8d;
            min-height: 18px;
            font-style: italic;
        }

        /* ── CONTENEUR DU PDF ────────────────────────────────────────── */
        #div_container {
            height: 800px; /* Hauteur fixe pour le PDF */
            width: 100%;
        }

    </style>
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

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap
                        align-items-center pt-3 pb-2 mb-3 border-bottom">

                <?php if (!empty($id) && !empty($password) && $password == '1'): ?>
                    <button id="btn_copy_password"
                        class="btn btn-primary"
                        style="font-weight:bolder; background-color:#2e93ff; display:none;">
                        <span data-feather="unlock"></span>
                        Cliquer ici pour copier le mot de passe<br>
                        à utiliser pour accéder au fichier
                    </button>

                <?php elseif (!empty($id) && !empty($password) && $password == '2'): ?>
                    <button id="btn_copy_password"
                            class="btn btn-primary"
                            style="font-weight:bolder; background-color:#2e93ff; display:none;">
                        <span data-feather="unlock"></span>
                        Cliquer ici pour copier le mot de passe<br>
                        Mot de passe : <?= $this->session->email_connexion ?>
                    </button>

                <?php else: ?>
                    <h1 class="h2">Espace de travail</h1>
                <?php endif ?>

                <?php $this->load->view('layout/btn_logout') ?>
            </div>

            <!-- Zone d'affichage du PDF -->
            <div id="div_container"></div>

        </main>
    </div>
</div>


<!-- ════════════════════════════════════════════════════════════════
     OVERLAY DE CHARGEMENT — Affiche pendant la génération du PDF

     Contient :
     - Icône PDF (Font Awesome)
     - Titre et texte explicatif
     - Barre de progression animée
     - Message d'étape (« Initialisation… », « Filigrane… », etc.)

     Devient visible quand JavaScript ajoute la classe "actif".
     ════════════════════════════════════════════════════════════════ -->
<div id="loading_overlay">
    <div id="loading_box">

        <!-- Icône PDF en rouge -->
        <i class="fa fa-file-pdf-o"></i>

        <!-- Titre principal -->
        <h5>Préparation du document</h5>
        <!-- Explication pour l'utilisateur sur ce qui se passe -->
        <p class="sous_titre">
            Application du filigrane et de la protection en cours…<br>
            Veuillez patienter, cela peut prendre quelques secondes.
        </p>

        <hr>

        <!-- Conteneur de la barre de progression et statut -->
        <div id="barre_container">
            <!-- La barre elle-même (largeur 0-100%, animée) -->
            <div class="progress">
                <div id="barre_progress"
                     class="progress-bar progress-bar-striped active"
                     role="progressbar"
                     aria-valuemin="0"
                     aria-valuemax="100">
                    0%
                </div>
            </div>
            <!-- Message d'étape actualisé en temps réel -->
            <div id="barre_etape">Initialisation…</div>
        </div>

    </div>
</div>


<!-- ════════════════════════════════════════════════════════════════
     MOT DE PASSE — Préparation pour transmission à JavaScript

     Le mot de passe est :
     - Défini selon le type de protection (type 1 = généré, type 2 = email)
     - Codé en base64 pour éviter les problèmes d'échappement HTML/JS
     - Décodé côté JavaScript (atob) pour usage en copie-presse

     Trois cas :
     1. password='1' : mot de passe généré (complexe)
     2. password='2' : email de l'utilisateur
     3. autres : pas de mot de passe, bouton masqué
     ════════════════════════════════════════════════════════════════ -->
<script>
<?php
    // ── Calcul du mot de passe côté PHP ──────────────────────────
    if ($password == '1') {
        // Mot de passe généré par _generer_user_pass() du contrôleur
        $pwd_php = "sA95HF:m@#w#1!è$ %e_ yvzm{}[]()/\\'`~,;:.ee129{$id}@#$%Gj9@#$%";
    } elseif ($password == '2') {
        // Utilise l'email connecté comme mot de passe
        $pwd_php = $this->session->email_connexion;
    } else {
        // Pas de mot de passe (accès libre)
        $pwd_php = '';
    }
    
?>
// Décodage en base64 → chaîne lisible pour le presse-papiers
var pwd_clair = atob('<?= base64_encode($pwd_php) ?>');

/**
 * Copie le texte dans le presse-papiers (fallback pour navigateurs anciens)
 *
 * Cette fonction crée un champ texte temporaire invisible, le sélectionne,
 * exécute la commande "copy" (ancienne API), puis le supprime.
 * Utilisée si l'API navigator.clipboard n'est pas disponible.
 *
 * @param string texte - Le texte à copier
 */
function _copier_fallback(texte) {
    // Crée un champ texte invisible
    var tmp = document.createElement('textarea');
    tmp.style.position = 'fixed';
    tmp.style.opacity  = '0';
    tmp.value = texte;
    document.body.appendChild(tmp);
    
    // Sélectionne et copie le contenu
    tmp.select();
    document.execCommand('copy');
    document.body.removeChild(tmp);
    
    // Notification de succès
    $.Notification.autoHideNotify('success', 'top right', 'Alerte', 'Mot de passe copié avec succès !');
}
</script>

<?php $this->load->view('layout/footer') ?>

<!-- ════════════════════════════════════════════════════════════════
     SCRIPT PRINCIPAL — Gestion de la génération PDF et affichage

     Responsabilités :
     1. Initialiser les variables (id, password, file_path)
     2. Configurer les boutons et événements
     3. Déclencher l'AJAX de génération du PDF
     4. Gérer la barre de progression via polling
     5. Gérer les timeouts et la vérification finale
     6. Afficher le PDF une fois prêt

     Les appels AJAX sont lancés après jQuery (chargé par footer)
     Polling toutes les 1s pour la progression
     Fallback de vérification en cas de timeout
     ════════════════════════════════════════════════════════════════ -->
<script>
$(document).ready(function () {
    // ── Récupération des variables PHP (sécurisées avec addslashes) ──
    var id        = '<?= isset($id)        ? addslashes($id)        : "" ?>';
    var password  = '<?= isset($password)  ? addslashes($password)  : "0" ?>';
    var file_path = '<?= isset($file_path) ? addslashes($file_path) : "" ?>';

    // ════════════════════════════════════════════════════════════════
    //  BOUTON COPIE MOT DE PASSE
    //  - Utilise l'API moderne (navigator.clipboard) en priorité
    //  - Fallback sur l'ancienne API (execCommand) si indisponible
    // ════════════════════════════════════════════════════════════════
    $('#btn_copy_password').on('click', function () {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            // API moderne (Chrome, Firefox, Edge récents)
            navigator.clipboard.writeText(pwd_clair).then(function () {
                $.Notification.autoHideNotify('success', 'top right', 'Alerte', 'Mot de passe copié avec succès !');
            }).catch(function () {
                // Fallback en cas d'erreur
                _copier_fallback(pwd_clair);
            });
        } else {
            // Fallback pour navigateurs anciens (IE, vieux Safari)
            _copier_fallback(pwd_clair);
        }
    });

    // ════════════════════════════════════════════════════════════════
    //  VÉRIFICATION DE LA LIBRAIRIE PDFOBJECT
    //  PDFObject affiche les PDF dans le navigateur
    // ════════════════════════════════════════════════════════════════
    if (typeof PDFObject === 'undefined') {
        console.error('PDFObject non chargé !');
        $.Notification.autoHideNotify('error', 'top right', 'Erreur', 'Librairie PDF manquante.');
        return;
    }

    // ════════════════════════════════════════════════════════════════
    //  CAS 1 : SANS ID — Affiche un PDF pré-généré ou par défaut
    //  (pas besoin de génération AJAX)
    // ════════════════════════════════════════════════════════════════
    if (!id) {
        if (file_path) {
            PDFObject.embed(file_path, '#div_container');
        }
        return; // Sort du script ready
    }

    // ════════════════════════════════════════════════════════════════
    //  CAS 2 : AVEC ID — Génération AJAX du PDF
    //
    //  Flux :
    //  1. Affiche overlay + barre de progression
    //  2. Lance polling (toutes les 1s) pour la progression
    //  3. Appel AJAX principal à document/generer/{id}
    //  4. En cas de timeout → vérification différée du fichier
    //  5. Une fois prêt → affiche le PDF
    // ════════════════════════════════════════════════════════════════
    $('#loading_overlay').addClass('actif');
    _avancerBarre(5, 'Démarrage de la génération…');

    var pollingTimer = null; // Polling toutes les 1s
    var verifTimer   = null; // Vérification en cas de timeout

    // ── POLLING EN ARRIÈRE-PLAN (toutes les 1s) ─────────────────
    /**
     * Interroge régulièrement le serveur pour connaître l'avancement.
     * Le serveur lit le fichier {id}.prog (JSON avec pct et msg)
     * Mise à jour auto de la barre sans bloquer l'appel principal.
     */
    pollingTimer = setInterval(function () {
        $.ajax({
            url:      '<?= site_url("document/progression/") ?>' + id,
            type:     'GET',
            dataType: 'json',
            timeout:  10000,
            success: function (prog) {
                if (prog && prog.pct !== undefined) {
                    // Limite à 99% (100% = finition) pour éviter les faux "prêts"
                    _avancerBarre(Math.min(prog.pct, 99), prog.msg);
                }
            }
            // Erreur polling ignorée silencieusement (réseau temporairement indisponible)
        });
    }, 1000);

    // ════════════════════════════════════════════════════════════════
    //  APPEL AJAX PRINCIPAL — Génère le PDF côté serveur
    // ════════════════════════════════════════════════════════════════
    /**
     * Déclenche la génération du PDF via document/generer/{id}.
     * 
     * Timeout long (10 min) car la génération PDF peut être
     * complexe et gourmande en ressources (gros fichiers, etc.)
     * 
     * Le contrôleur retourne :
     * - success: true/false
     * - pct/msg: progression actuelle
     * - en_cours: true si le traitement est toujours en cours
     * - file_path: chemin final du PDF
     */
    $.ajax({
        url:      '<?= site_url("document/generer/") ?>' + id,
        type:     'GET',
        dataType: 'json',
        timeout:  600000, // 10 minutes (traitement PDF complexe)

        success: function (resp) {
            // ── GESTION DES ERREURS ──────────────────────────────────
            if (!resp.success) {
                clearInterval(pollingTimer);
                clearTimeout(verifTimer);
                _erreur(resp.error || 'Erreur inconnue');
                return;
            }

            // ── CAS : Traitement toujours en cours ──────────────────
            /**
             * Le traitement principal a terminé, mais des opérations
             * complémentaires sont peut-être en cours.
             * Le fichier n'est pas encore totalement accessible.
             * → Lance une vérification différée (fallback de timeout).
             */
            if (resp.en_cours === true) {
                _avancerBarre(95, 'Finalisation en cours...');
                _verifierApresTimeout();
                return;
            }

            // ── CAS : SUCCÈS — Fichier prêt ──────────────────────
            clearInterval(pollingTimer);
            clearTimeout(verifTimer);
            _succes(resp.file_path, resp.cached);
        },

        error: function (xhr, status) {
            clearInterval(pollingTimer);

            // ── GESTION DU TIMEOUT ───────────────────────────────────
            /**
             * Le serveur ne répond pas dans les 10 min.
             * Le traitement PDF est peut-être toujours en cours.
             * 
             * Fallback : Polling différé toutes les 2s pendant 5 min max.
             */
            if (status === 'timeout') {
                _avancerBarre(95, 'Finalisation en cours, veuillez patienter…');
                _verifierApresTimeout();
                return;
            }

            // ── AUTRE ERREUR RÉSEAU ──────────────────────────────────
            // (500 server error, connection refused, etc.)
            var msg = 'Erreur serveur lors de la génération du document.';
            try {
                var r = JSON.parse(xhr.responseText);
                if (r.error) msg = r.error;
            } catch (e) {}
            _erreur(msg);
        }
    });

    // ════════════════════════════════════════════════════════════════
    //  VÉRIFICATION APRÈS TIMEOUT
    //
    //  Contexte : AJAX principal a timeouté, mais le traitement PDF
    //             pourrait toujours être actif en arrière-plan.
    //  
    //  Stratégie : Polling différé toutes les 2s pendant 5 minutes max.
    //              Une fois à 100% ou fichier trouvé → affiche.
    // ════════════════════════════════════════════════════════════════
    function _verifierApresTimeout() {
        var tentatives    = 0;
        var maxTentatives = 150; // ~5 minutes (150 * 2s = 300s)

        verifTimer = setInterval(function () {
            tentatives++;

            $.ajax({
                url:      '<?= site_url("document/progression/") ?>' + id,
                type:     'GET',
                dataType: 'json',
                timeout:  10000,

                success: function (prog) {
                    // Si progression < 100%, traitement toujours actif
                    if (prog && prog.pct !== undefined && prog.pct < 100) {
                        // Actualise la barre et continue
                        _avancerBarre(Math.min(prog.pct, 99), prog.msg);
                        return; // Polling continue
                    }

                    // pct = 100 ou .prog absent → fichier prêt
                    clearInterval(verifTimer);
                    _verifierFichierPret(); // Vérification HEAD finale
                },

                error: function () {
                    // Fichier .prog probablement supprimé = terminé
                    clearInterval(verifTimer);
                    _verifierFichierPret();
                }
            });

            // Sécurité : arrête si polling dépasse 5 minutes
            if (tentatives >= maxTentatives) {
                clearInterval(verifTimer);
                _erreur('La génération a pris trop de temps. Veuillez réessayer.');
            }

        }, 2000); // Polling toutes les 2 secondes
    }

    // ════════════════════════════════════════════════════════════════
    //  VÉRIFICATION FINALE — Requête HTTP HEAD
    //
    //  Lightweight check : teste juste si le fichier existe
    //  sans le télécharger entièrement.
    // ════════════════════════════════════════════════════════════════
    function _verifierFichierPret() {
        $.ajax({
            url:     file_path,
            type:    'HEAD', // Léger : juste les headers, pas le body
            timeout: 15000,

            success: function () {
                // Fichier trouvé → affiche le PDF
                _succes(file_path, false);
            },

            error: function () {
                // Fichier toujours absent après 5 min → erreur
                _erreur('Le document n\'a pas pu être généré. Veuillez réessayer.');
            }
        });
    }

    // ════════════════════════════════════════════════════════════════
    //  SUCCÈS — Finalisation et affichage du PDF
    //
    //  À ce stade :
    //  - Barre → 100%
    //  - Message de succès
    //  - Attente 700ms (transition visuelle)
    //  - Masque overlay
    //  - Affiche bouton mot de passe si nécessaire
    //  - Intègre le PDF avec PDFObject
    // ════════════════════════════════════════════════════════════════
    function _succes(fp, cached) {
        // Barre à 100% avec message adapté (cache ou nouvellement généré)
        _avancerBarre(100, cached
            ? 'Document chargé depuis le cache ✓'
            : 'Document généré avec succès ✓'
        );
        // Arrête l'animation de la barre
        $('#barre_progress').removeClass('active');

        // Attente pour transition visuelle
        setTimeout(function () {
            // Masque l'overlay de chargement
            $('#loading_overlay').removeClass('actif');

            // Affiche le bouton de copie mot de passe si protégé
            if (password === '1' || password === '2') {
                $('#btn_copy_password').fadeIn(400);
            }

            // Intègre le PDF dans le conteneur
            PDFObject.embed(fp, '#div_container');

        }, 700); // Délai pour la transition CSS
    }

    // ════════════════════════════════════════════════════════════════
    //  FONCTIONS UTILITAIRES
    // ════════════════════════════════════════════════════════════════

    /**
     * Met à jour la barre de progression et le message d'étape
     * @param int pct        Pourcentage (0-100)
     * @param string msg     Message d'étape explicatif
     */
    function _avancerBarre(pct, msg) {
        // Actualise la largeur, le texte et les attributs aria
        $('#barre_progress')
            .css('width', pct + '%')
            .text(pct + '%')
            .attr('aria-valuenow', pct); // Pour accessibilité
        // Message d'étape (« Initialisation… », « Compression… », etc.)
        $('#barre_etape').text(msg);
    }

    /**
     * Affiche une notification d'erreur et nettoie les timers
     * @param string msg  Message d'erreur à afficher
     */
    function _erreur(msg) {
        // Arrête tous les timers actifs
        clearInterval(pollingTimer);
        clearInterval(verifTimer);
        // Masque l'overlay de chargement
        $('#loading_overlay').removeClass('actif');
        // Notification (coin haut droit, auto-disparition)
        $.Notification.autoHideNotify('error', 'top right', 'Erreur', msg);
    }

}); // Fin du $(document).ready)
</script>

</body>
</html>