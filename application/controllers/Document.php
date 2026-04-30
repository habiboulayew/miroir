<?php
/**
 * Contrôleur Document - Gestion des documents PDF dans Miroir
 *
 * Ce contrôleur gère la génération, l'affichage et la protection des documents PDF.
 * Il utilise la bibliothèque FPDI/FPDIProtection pour appliquer des filigranes et
 * des protections par mot de passe aux documents PDF existants.
 *
 * Fonctionnalités principales :
 * - Affichage des documents avec filigrane personnalisé
 * - Protection par mot de passe (généré automatiquement ou email utilisateur)
 * - Génération en temps réel avec barre de progression
 * - Cache des documents générés pour éviter les recalculs
 *
 * Technologies utilisées :
 * - CodeIgniter 3 (framework PHP)
 * - FPDI/FPDIProtection (manipulation PDF)
 * - Sessions PHP pour le stockage temporaire des données de filigrane
 * - AJAX pour la génération asynchrone avec progression
 *
 * @author [Votre nom]
 * @version 1.1
 * @since 2026-04-22
 */

defined('BASEPATH') OR exit('No direct script access allowed');

// Import des bibliothèques FPDI pour la manipulation PDF
use setasign\Fpdi\Fpdi;
use setasign\FpdiProtection\FpdiProtection;

// ====================================================================
//  CLASSE DOCUMENT — CONTRÔLEUR PRINCIPAL
// ====================================================================
/**
 * Classe Document - Contrôleur principal pour la gestion des documents
 *
 * Hérite de MY_Controller qui gère la vérification de session automatique.
 * Ce contrôleur ne gère que l'affichage et la génération des PDF personnalisés.
 */
class Document extends MY_Controller
{
    /**
     * Constructeur de la classe
     *
     * Charge les modèles nécessaires pour accéder aux données des documents
     * et des profils utilisateur.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_document_profil', 'document_profil');
        $this->load->model('M_document', 'document');
    }

    // ================================================================
    //  _GENERER_USER_PASS — Génération du mot de passe utilisateur
    // ================================================================
    /**
     * Génère un mot de passe pour la protection PDF
     *
     * Cette méthode crée un mot de passe fixe complexe pour protéger les PDF.
     * Le mot de passe inclut l'ID du document pour l'unicité.
     * Fonctionne uniquement avec FpdiProtection (pas de shell) — les caractères
     * spéciaux ne posent aucun problème car tout reste en PHP pur.
     *
     * @param int $id L'identifiant du document
     * @return string Le mot de passe généré
     */
    private function _generer_user_pass($id)
    {
        // Mot de passe fixe complexe avec incorporation de l'ID du document
        // Chaque document a un mot de passe unique grâce à l'injection de $id
        return "sA95HF:m@#w#1!è$ %e_ yvzm{}[]()/\\'`~,;:.ee129{$id}@#$%Gj9@#$%";

        // Version alternative avec hash SHA-256 (commentée)
        // return 'MIR-' . strtoupper(substr(hash('sha256', 'miroir_sel_' . $id), 0, 20));
    }

    // ================================================================
    //  INDEX - Point d'entrée principal pour l'affichage des documents
    //  URL : /document/index/{id}  ou  /document/{id}
    // ================================================================
    /**
     * Méthode principale d'affichage des documents
     *
     * Cette méthode gère deux cas :
     * 1. Sans ID : Affiche un PDF par défaut et charge le menu utilisateur
     * 2. Avec ID : Prépare les données pour la génération AJAX du PDF personnalisé
     *
     * La génération réelle du PDF se fait via AJAX dans la méthode generer().
     */
    public function index()
    {
        // Récupération de l'ID du document depuis les arguments de l'URL
        $args = func_get_args();
        $id   = !empty($args[0]) ? $args[0] : '';

        // Initialisation des données à passer à la vue
        $data['id']        = $id;
        $data['password']  = '0';  // Par défaut, pas de mot de passe
        $data['file_path'] = '';   // Chemin du fichier PDF à afficher

        // ── CAS 1 : Aucun ID fourni → Affichage du PDF par défaut ─────────
        if (empty($id)) {
            // Récupération des documents accessibles par le profil de l'utilisateur
            $menu_data = $this->document_profil
                             ->get_document_profil($this->session->id_profil);
            $this->session->set_userdata('data_menu', $menu_data);

            // Affichage du PDF par défaut avec un paramètre cache-busting
            $data['file_path'] = base_url("DATA/default.pdf?12345");

            // Chargement de la vue principale avec les données
            $this->load->view('acceuil', $data);
            return;
        }

        // ── CAS 2 : ID fourni → Préparation pour génération AJAX ──────────
        // Chargement des informations du document depuis la base de données
        $this->document->id_document = $id;
        $this->document->get_record();

        // Récupération du type de protection du document
        $data['password']  = $this->document->password;
        $id_personnel      = $this->session->id_personnel;

        // Construction du nom de fichier selon la présence de protection/filigrane
        if ($this->document->password != '0' || $this->document->filigrane != '0') {
            // Format: Titre_IDPersonnel_IDDocument.pdf (avec protection)
            $data['file_path'] = base_url(
                "DATA/" . ucfirst(to_tolower($this->document->titre))
                . "_$id_personnel" . "_$id.pdf"
            );
        } else {
            // Format: Titre.pdf (sans protection)
            $data['file_path'] = base_url(
                "DATA/" . ucfirst(to_tolower($this->document->titre)) . ".pdf"
            );
        }

        // Chargement de la vue avec les données préparées
        // La vue déclenchera la génération AJAX si nécessaire
        $this->load->view('acceuil', $data);
    }

    // ================================================================
    //  GENERER - Génération du PDF personnalisé via AJAX
    //  URL    : /document/generer/{id}
    //  Retour : JSON { success, file_path, password, cached }
    // ================================================================
    /**
     * Génère un PDF personnalisé avec filigrane et protection
     *
     * Cette méthode est appelée via AJAX depuis la vue JavaScript.
     *
     * IMPORTANT : Le filigrane est dessiné APRÈS useTemplate() pour qu'il
     * apparaisse AU-DESSUS de tout le contenu importé (fonds colorés inclus).
     *
     * @param string $id L'identifiant du document à générer
     */
    public function generer($id = '')
    {
        // ── VALIDATION DES PARAMÈTRES ──────────────────────────────────────
        if (empty($id)) {
            $this->_json_response(['error' => 'ID manquant'], 400);
            return;
        }

        // ── CHARGEMENT DES DONNÉES DU DOCUMENT ────────────────────────────
        $this->document->id_document = $id;
        $this->document->get_record();

        $id_personnel = $this->session->id_personnel;

        // ── CALCUL DES CHEMINS DE FICHIERS ─────────────────────────────────
        // Chemin absolu du fichier source (toujours ID.pdf)
        $file = FCPATH . "DATA/$id.pdf";

        // Construction du nom de fichier de destination selon la protection
        if ($this->document->password != '0' || $this->document->filigrane != '0') {
            $nom_fichier = ucfirst(to_tolower($this->document->titre))
                         . "_$id_personnel" . "_$id.pdf";
        } else {
            $nom_fichier = ucfirst(to_tolower($this->document->titre)) . ".pdf";
        }

        // Chemins absolus et relatifs du fichier de destination
        $file_user = FCPATH . "DATA/" . $nom_fichier;
        $file_path = base_url("DATA/" . $nom_fichier);

        // Fichier de progression pour la barre de chargement
        $prog_file = FCPATH . "DATA/.prog_$id.json";
        @unlink($prog_file); // Suppression du fichier de progression précédent

        // ── VÉRIFICATION DU CACHE ──────────────────────────────────────────
        // Si le fichier personnalisé existe déjà, pas besoin de le régénérer
        if (file_exists($file_user)) {
            $this->_json_response([
                'success'   => true,
                'file_path' => $file_path,
                'password'  => $this->document->password,
                'cached'    => true  // Indique que c'est un fichier en cache
            ]);
            return;
        }

        // ── VÉRIFICATION DE L'EXISTENCE DU FICHIER SOURCE ─────────────────
        if (!file_exists($file)) {
            $this->_json_response([
                'error' => 'Fichier source introuvable : ' . basename($file)
            ], 404);
            return;
        }

        // ================================================================
        //  CONFIGURATION PHP — Paramètres essentiels pour les gros PDF
        // ================================================================
        ini_set('memory_limit', '-1');  // Supprime la limite mémoire PHP
        set_time_limit(0);              // Supprime le timeout d'exécution PHP
        ignore_user_abort(true);        // Continue même si le navigateur ferme

        // ================================================================
        //  INITIALISATION DES VARIABLES DE SESSION
        // ================================================================
        /**
         * Les données sont stockées en session car elles seront lues
         * par la classe PDF_extend lors du dessin des filigranes.
         * Réinitialisation complète pour éviter les résidus d'une
         * génération précédente.
         */

        // Réinitialisation des variables de filigrane
        $_SESSION['filigrane_texte']           = null;
        $_SESSION['filigrane_indentification'] = null;
        $_SESSION['filigrane_confidentiel']    = null;
        $_SESSION['filigrane_fonction']        = null;

        // Réinitialisation des variables de protection
        $_SESSION['pdf_protect']    = false;
        $_SESSION['pdf_user_pass']  = null;
        $_SESSION['pdf_owner_pass'] = null;

        // ── CONFIGURATION DU FILIGRANE ─────────────────────────────────────
        if ($this->document->filigrane == '1') {
            // Texte personnalisé du filigrane
            $_SESSION['filigrane_texte'] = $this->document->filigrane_texte;

            // Filigrane d'identification (nom + prénom de l'utilisateur)
            if ($this->document->filigrane_indentification == '1') {
                $_SESSION['filigrane_indentification'] = $_SESSION['prenom'] . ' ' . $_SESSION['nom'];
                $_SESSION['filigrane_fonction']        = $_SESSION['fonction'];
            }

            // Filigrane de confidentialité
            if ($this->document->filigrane_confidentiel == '1') {
                $_SESSION['filigrane_confidentiel'] =
                    "Veuillez respecter l'obligation de confidentialité de ce document.";
            }
        }

        // ── CONFIGURATION DE LA PROTECTION ────────────────────────────────
        if ($this->document->password != '0') {
            $_SESSION['pdf_protect'] = true;

            // Type de mot de passe selon la configuration du document
            if ($this->document->password == '1') {
                // Mot de passe généré automatiquement, unique par document
                $_SESSION['pdf_user_pass'] = $this->_generer_user_pass($id);
            } elseif ($this->document->password == '2') {
                // Mot de passe = email de connexion de l'utilisateur
                $_SESSION['pdf_user_pass'] = $this->session->email_connexion;
            } else {
                // Aucun mot de passe utilisateur
                $_SESSION['pdf_user_pass'] = "";
            }

            // Mot de passe propriétaire (pour les permissions avancées)
            $_SESSION['pdf_owner_pass'] = "sA95HF:m@#$%@#$%eyvz@#$%Gj9@#$%";
        }

        // Initialisation de la progression puis libération du verrou de session.
        // IMPORTANT : session_write_close() doit être appelé APRÈS avoir écrit
        // toutes les variables $_SESSION nécessaires à PDF_extend
        $this->_prog($prog_file, 5, 'Initialisation…');
        session_write_close();

        // ================================================================
        //  GÉNÉRATION DU PDF AVEC FPDI
        // ================================================================
        /**
         * Stratégie de dessin du filigrane :
         *
         * ANCIENNE APPROCHE — PROBLÈME :
         *   AddPage() → Header() dessine le filigrane → useTemplate() importe
         *   le contenu PAR-DESSUS → les fonds colorés cachent le filigrane.
         *
         * NOUVELLE APPROCHE — CORRECTE :
         *   AddPage() → useTemplate() importe le contenu → _dessiner_filigrane()
         *   dessine le filigrane PAR-DESSUS → toujours visible sur tout fond.
         *
         * Header() est donc volontairement laissé vide dans PDF_extend.
         */
        try {
            // Création d'une nouvelle instance PDF avec protection
            $pdf = new PDF_extend('P', 'mm', 'A4', true);

            // ── APPLICATION DE LA PROTECTION PAR MOT DE PASSE ─────────────
            // La protection DOIT être appliquée AVANT l'import des pages
            if ($_SESSION['pdf_protect']) {
                $pdf->setProtection(
                    array(),                     // Permissions (vide = lecture seule)
                    $_SESSION['pdf_user_pass'],  // Mot de passe utilisateur (pour ouvrir)
                    $_SESSION['pdf_owner_pass'], // Mot de passe propriétaire (pour modifier)
                    3                            // Niveau de chiffrement (128 bits)
                );
            }

            // Chargement du fichier source et comptage des pages
            $pageCount = $pdf->setSourceFile($file);
            $this->_prog($prog_file, 10,
                "Document chargé — $pageCount page(s) détectée(s)…");

            // ── CONSTRUCTION DU LIBELLÉ DE PROGRESSION ─────────────────────
            // Message dynamique selon les traitements actifs pour ce document
            // Exemples : "Filigrane + Protection", "Filigrane", "Protection"
            $operations = [];
            if ($this->document->filigrane != '0') $operations[] = 'Filigrane';
            if ($this->document->password  != '0') $operations[] = 'Protection';
            $libelle = !empty($operations)
                ? implode(' + ', $operations)
                : 'Traitement';

            // ── TRAITEMENT PAGE PAR PAGE ────────────────────────────────────
            /**
             * Ordre des opérations par page (CRUCIAL) :
             * 1. importPage()        → charge la page source en mémoire
             * 2. AddPage()           → crée la nouvelle page (Header() vide appelé ici)
             * 3. useTemplate()       → copie le contenu source (fonds colorés inclus)
             * 4. _dessiner_filigrane() → filigrane dessiné PAR-DESSUS le contenu
             *
             * Cet ordre garantit que le filigrane est toujours visible,
             * même sur les blocs avec fond coloré (blocs de code, tableaux...).
             */
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

                // 1. Import de la page depuis le PDF source
                $templateId = $pdf->importPage($pageNo);

                // 2. Récupération des dimensions et orientation de la page
                $size = $pdf->getTemplateSize($templateId);
                $_SESSION['size_orientation'] = $size['orientation'];

                // 3. Création d'une nouvelle page dans le PDF de destination
                $pdf->AddPage($size['orientation'], $size);

                // 4. Copie du contenu de la page source (fonds colorés inclus)
                $pdf->useTemplate($templateId);

                // 5. Filigrane dessiné PAR-DESSUS le contenu importé
                //    → toujours visible, même sur les blocs avec fond coloré
                $pdf->_dessiner_filigrane();

                // Mise à jour de la progression (plage 10% → 80%)
                $pct = 10 + intval(($pageNo / $pageCount) * 70);
                $this->_prog($prog_file, $pct,
                    "$libelle — page $pageNo / $pageCount");
            }

            // Sauvegarde finale du PDF généré sur le disque
            $pdf->Output($file_user, 'F');

            // Finalisation de la progression
            $this->_prog($prog_file, 100, 'Terminé');

        } catch (Exception $e) {
            // En cas d'erreur FPDI/FPDF, retourner un message d'erreur JSON
            $this->_json_response([
                'error' => 'Erreur génération PDF : ' . $e->getMessage()
            ], 500);
            return;
        }

        // ── RÉPONSE DE SUCCÈS ──────────────────────────────────────────────
        $this->_json_response([
            'success'   => true,
            'file_path' => $file_path,
            'password'  => $this->document->password,
            'cached'    => false  // Génération fraîche (pas depuis le cache)
        ]);
    }

    // ================================================================
    //  PROGRESSION - Endpoint AJAX pour la barre de progression
    //  URL    : /document/progression/{id}
    //  Retour : JSON { pct, msg }
    // ================================================================
    /**
     * Point d'accès AJAX pour récupérer l'état de progression
     *
     * Appelée toutes les secondes par JavaScript pendant la génération.
     * session_write_close() est appelé en premier pour libérer immédiatement
     * le verrou de session — sans cela, les appels de polling seraient
     * bloqués derrière le verrou de generer().
     *
     * @param string $id L'identifiant du document en cours de génération
     */
    public function progression($id = '')
    {
        // Libération immédiate du verrou de session pour éviter les blocages
        session_write_close();

        // Validation du paramètre ID
        if (empty($id)) {
            $this->_json_response(['pct' => 0, 'msg' => ''], 400);
            return;
        }

        // Chemin du fichier de progression
        $prog_file = FCPATH . "DATA/.prog_$id.json";

        // Si le fichier n'existe plus, la génération est terminée
        if (!file_exists($prog_file)) {
            $this->_json_response(['pct' => 100, 'msg' => 'Terminé']);
            return;
        }

        // Lecture et décodage des données de progression
        $data = json_decode(file_get_contents($prog_file), true);

        // Retour des données (avec valeur par défaut si décodage échoue)
        $this->_json_response($data ?: ['pct' => 0, 'msg' => 'En attente…']);
    }

    // ================================================================
    //  _PROG — Écriture du fichier de progression
    // ================================================================
    /**
     * Écrit l'état de progression dans un fichier JSON
     *
     * Crée/met à jour un fichier temporaire caché (.prog_{id}.json)
     * contenant le pourcentage et le message de l'étape en cours.
     *
     * @param string $fichier Chemin absolu du fichier de progression
     * @param int    $pct     Pourcentage de progression (0-100)
     * @param string $msg     Message descriptif de l'étape en cours
     */
    private function _prog($fichier, $pct, $msg)
    {
        file_put_contents($fichier, json_encode([
            'pct' => $pct,
            'msg' => $msg,
        ], JSON_UNESCAPED_UNICODE));
    }

    // ================================================================
    //  _JSON_RESPONSE - Helper pour les réponses JSON
    // ================================================================
    /**
     * Envoie une réponse JSON formatée avec code HTTP
     *
     * Utilise le système Output de CI3 (pas echo direct) pour respecter
     * le cycle de vie du framework. _display() force l'envoi immédiat
     * et exit() arrête PHP pour éviter tout traitement parasite de CI3.
     *
     * @param array $data   Données à encoder en JSON
     * @param int   $status Code HTTP de la réponse (défaut: 200)
     */
    private function _json_response(array $data, int $status = 200)
    {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));

        // Envoi immédiat de la réponse et arrêt de l'exécution
        $this->output->_display();
        exit;
    }

} // ← FIN classe Document


// ====================================================================
//  CLASSE PDF_EXTEND — Extension FPDI avec filigranes et protection
// ====================================================================
/**
 * Classe PDF_extend - Extension de FpdiProtection avec filigranes
 *
 * Hérite de FpdiProtection → Fpdi → FPDF.
 *
 * CHOIX ARCHITECTURAL : Header() est volontairement vide.
 * Le filigrane est dessiné via _dessiner_filigrane() appelée manuellement
 * APRÈS useTemplate() dans la boucle du contrôleur. Cela garantit que
 * le filigrane apparaît toujours AU-DESSUS du contenu importé,
 * y compris les zones avec fond coloré (blocs de code, tableaux...).
 */
class PDF_extend extends FpdiProtection
{
    /** @var float Angle de rotation actuel (0 = pas de rotation active) */
    var $angle = 0;

    // ================================================================
    //  MÉTHODES DE ROTATION — Base du système de filigrane diagonal
    // ================================================================

    /**
     * Rotation du système de coordonnées PDF
     *
     * Applique une transformation matricielle PDF pour faire pivoter
     * le texte des filigranes. Utilise les commandes PDF bas niveau
     * (opérateur 'q/Q' pour save/restore le contexte graphique).
     *
     * @param float $angle Angle de rotation en degrés
     * @param float $x     Coordonnée X du centre de rotation (-1 = position courante)
     * @param float $y     Coordonnée Y du centre de rotation (-1 = position courante)
     */
    function Rotate($angle, $x = -1, $y = -1)
    {
        if ($x == -1) $x = $this->x;
        if ($y == -1) $y = $this->y;

        // Fermeture de la rotation précédente si active
        if ($this->angle != 0)
            $this->_out('Q');

        $this->angle = $angle;

        if ($angle != 0) {
            // Conversion degrés → radians et calcul de la matrice de transformation
            $angle *= M_PI / 180;
            $c  = cos($angle);
            $s  = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;

            // Application de la matrice de rotation via commande PDF bas niveau
            $this->_out(sprintf(
                'q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',
                $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy
            ));
        }
    }

    /**
     * Nettoyage du contexte graphique en fin de page
     *
     * Appelée automatiquement par FPDF après chaque page.
     * Réinitialise l'angle de rotation pour éviter les interférences
     * entre les pages successives.
     */
    function _endpage()
    {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }

    // ================================================================
    //  HEADER — Volontairement vide
    // ================================================================

    /**
     * Header volontairement vide — filigrane géré par _dessiner_filigrane()
     *
     * POURQUOI VIDE ?
     * Si on dessine le filigrane ici, FPDF l'applique lors de AddPage(),
     * AVANT que useTemplate() ne copie le contenu de la page source.
     * Le contenu importé (avec ses fonds colorés) s'affiche alors
     * PAR-DESSUS le filigrane, le rendant partiellement invisible.
     *
     * Solution : _dessiner_filigrane() est appelée manuellement
     * APRÈS useTemplate() dans la boucle de generer().
     */
    function Header()
    {
        // Volontairement vide — voir _dessiner_filigrane()
    }

    // ================================================================
    //  _DESSINER_FILIGRANE — Dessin explicite après le contenu importé
    // ================================================================

    /**
     * Dessine tous les filigranes sur la page courante
     *
     * Appelée manuellement depuis le contrôleur APRÈS useTemplate().
     * Garantit que le filigrane est dessiné AU-DESSUS de tout le
     * contenu importé, y compris les zones avec fond coloré.
     *
     * Lit les données de filigrane depuis $_SESSION (préparées par generer()).
     *
     * Filigranes appliqués selon l'orientation :
     * - Portrait  : texte à +45°, couleur rose pâle (255, 192, 203)
     * - Paysage   : texte à -45°, couleur grise (180, 180, 180)
     * - Confidentialité : toujours en rouge, centré en haut (toutes orientations)
     */
    public function _dessiner_filigrane()
    {
        $orientation = $_SESSION['size_orientation'];

        // ── ORIENTATION PORTRAIT ───────────────────────────────────────────
        if ($orientation == 'P') {

            // Filigrane texte principal (ex: "CONFIDENTIEL", "DRAFT"...)
            if (!empty($_SESSION['filigrane_texte'])) {
                $this->SetFont('Arial', 'B', 65);
                $this->SetTextColor(255, 192, 203); // Rose pâle
                $this->RotatedText(40, 180, $_SESSION['filigrane_texte'], 45);
            }

            // Filigrane d'identification : Prénom + Nom de l'utilisateur
            if (!empty($_SESSION['filigrane_indentification'])) {
                $this->SetFont('Arial', 'B', 55);
                $this->SetTextColor(255, 192, 203);
                $this->RotatedText(60, 190, $_SESSION['filigrane_indentification'], 45);
            }

            // Filigrane de fonction/poste de l'utilisateur
            if (!empty($_SESSION['filigrane_fonction'])) {
                $this->SetFont('Arial', 'B', 45);
                $this->SetTextColor(255, 192, 203);
                $this->RotatedText(80, 200, $_SESSION['filigrane_fonction'], 45);
            }

        // ── ORIENTATION PAYSAGE ────────────────────────────────────────────
        } else if ($orientation == 'L') {

            // Filigrane texte principal (gris en paysage, angle inversé)
            if (!empty($_SESSION['filigrane_texte'])) {
                $this->SetFont('Arial', 'B', 65);
                $this->SetTextColor(180, 180, 180); // Gris
                $this->RotatedText(90, 90, utf8_decode($_SESSION['filigrane_texte']), -45);
            }

            // Filigrane d'identification
            if (!empty($_SESSION['filigrane_indentification'])) {
                $this->SetFont('Arial', 'B', 55);
                $this->SetTextColor(255, 192, 203);
                $this->RotatedText(100, 170, $_SESSION['filigrane_indentification'], 45);
            }

            // Filigrane de fonction
            if (!empty($_SESSION['filigrane_fonction'])) {
                $this->SetFont('Arial', 'B', 45);
                $this->SetTextColor(255, 192, 203);
                $this->RotatedText(150, 170, $_SESSION['filigrane_fonction'], 45);
            }
        }

        // ── MENTION DE CONFIDENTIALITÉ (TOUTES ORIENTATIONS) ──────────────
        // Texte rouge centré en haut de page, affiché quelle que soit l'orientation
        if (!empty($_SESSION['filigrane_confidentiel'])) {
            $this->SetFont('Arial', 'B', 11);
            $this->SetTextColor(255, 0, 0); // Rouge
            $this->Cell(0, 0, utf8_decode($_SESSION['filigrane_confidentiel']), 0, 0, 'C');
        }
    }

    // ================================================================
    //  ROTATEDTEXT — Écriture de texte avec rotation
    // ================================================================

    /**
     * Écrit un texte avec un angle de rotation
     *
     * Méthode utilitaire qui combine Rotate() et Text() pour simplifier
     * l'ajout des filigranes diagonaux.
     * Séquence : active la rotation → écrit le texte → désactive la rotation.
     *
     * @param float  $x     Coordonnée X du texte (en mm)
     * @param float  $y     Coordonnée Y du texte (en mm)
     * @param string $txt   Texte à afficher
     * @param float  $angle Angle de rotation en degrés (45° = diagonal montant)
     */
    function RotatedText($x, $y, $txt, $angle)
    {
        $this->Rotate($angle, $x, $y);  // Activation de la rotation
        $this->Text($x, $y, $txt);      // Écriture du texte
        $this->Rotate(0);               // Désactivation de la rotation
    }

} // ← FIN classe PDF_extend