<?php
/* Meet AJAX
 * hepl-mmi/meet-ajax
 *
 * by leny
 * started @ 18/12/2015
 */

$sDataFilePath = sys_get_temp_dir() . "/buddies.json";
$aBuddies = [];

// clean data
if ( isset( $_GET[ "clean" ] ) ) {
    file_put_contents( $sDataFilePath, json_encode( $aBuddies ) );
}

// load json data
if ( file_exists( $sDataFilePath ) ) {
    $sBuddiesFileContent = file_get_contents( $sDataFilePath );
    $aBuddies = json_decode( $sBuddiesFileContent, true );
}

// parse form
$bHasErrors = false;
if ( count( $_POST ) > 0 ) {
    $sName = trim( $_POST[ "name" ] );
    $sDescription = trim( $_POST[ "description" ] );
    if ( $sName && $sDescription ) {
        $aBuddy = array();
        $aBuddy[ "name" ] = $sName;
        $aBuddy[ "description" ] = $sDescription;
        $aBuddies[] = $aBuddy;
        file_put_contents( $sDataFilePath, json_encode( $aBuddies ) );
    } else {
        $bHasErrors = true;
    }
}

if ( !empty( $_SERVER[ "HTTP_X_REQUESTED_WITH" ] ) && strtolower( $_SERVER[ "HTTP_X_REQUESTED_WITH" ] ) === "xmlhttprequest" ) {
    $aResponse = array();
    $aResponse[ "name" ] = $sName;
    $aResponse[ "description" ] = $sDescription;
    $aResponse[ "avatar" ] = "http://api.adorable.io/avatars/90/" . $sName . ".png";
    $aResponse[ "alt" ] = "Avatar de " . $sName;
    header( "Content-Type: application/json" );
    die( json_encode( $aResponse ) );
}

?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <title lang="en">Meet AJAX</title>

        <link rel="stylesheet" href="css/bootstrap.min.css" />
    </head>
    <body>
        <main class="container">
            <header class="row page-header">
                <h1 class="col-md-12">
                    <span lang="en">Meet AJAX</span>
                    <small>Votre nouveau super-coupaing</small>
                </h1>
            </header>
            <section class="content row">
                <h2 class="hidden">
                    Un exercice haut en couleurs…
                </h2>

                <div class="col-md-4">
                    <h3>
                        L'album des coupaings
                    </h3>

                    <div class="row" id="buddies-container">
                        <?php if( count( $aBuddies ) ): ?>
                            <?php foreach( $aBuddies as $aBuddy ): ?>
                                <div class="col-md-4">
                                    <div class="thumbnail" title="<?= $aBuddy[ "description" ]; ?>">
                                        <img src="http://api.adorable.io/avatars/90/<?= $aBuddy[ "name" ]; ?>.png" alt="Avatar de <?= $aBuddy[ "name" ]; ?>" width="90" height="90" />
                                        <div class="caption">
                                            <strong><?= $aBuddy[ "name" ]; ?></strong>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-md-12">
                                <div class="well">
                                    Vous n'avez pas encore ajouté de coupaing !
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <h3>Ajouter un coupaing</h3>
                    <form action="./" method="post">
                        <fieldset>
                            <?php if( $bHasErrors ): ?>
                                <div class="alert alert-danger">
                                    <strong>Oops !</strong>
                                    On dirait que vous avez oublié de remplir chaque champs du formulaire !
                                </div>
                            <?php endif; ?>
                            <div class="form-group<?= $bHasErrors && !$sName ? " has-error" : "" ?>">
                                <label class="control-label" for="name">Nom:</label>
                                <input type="text" class="form-control" name="name" placeholder="Nom du coupaing" value="<?= $bHasErrors ? $sName : "" ?>" />
                            </div>
                            <div class="form-group<?= $bHasErrors && !$sDescription ? " has-error" : "" ?>">
                                <label class="control-label" for="description">Description:</label>
                                <textarea class="form-control" name="description" placeholder="Description du coupaing"><?= $bHasErrors ? $sDescription : "" ?></textarea>
                                <p class="help-block">Faites court, on manque de place !</p>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-info">Ajouter un coupaing</button>
                            </div>
                        </fieldset>
                    </form>
                    <hr />
                    <h3>Supprimer les coupaings</h3>
                    <p>Ils seront perdus à jamais, mais vous pourrez toujours en créer d'autres…</p>
                    <div class="text-center">
                        <a href="./?clean" class="btn btn-danger">Supprimer les coupaings</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h3>Qu'est-ce qui se passe ici ?</h3>
                    <p>Sur cette page, un script PHP affiche une série de <em>coupaings</em>, stockés dans un fichier json.</p>
                    <p>Un formulaire permet d'ajouter un nouveau coupaing et de la sauvegarder, toujours via PHP.</p>
                    <hr />
                    <h3>Qu'est-ce qu'on va faire ?</h3>
                    <p>
                        Chaque ajout de coupaing entraîne un rechargement de la page, c'est quand même pas très cool. Grâce à <strong>jQuery</strong>, nous allons faire cette opération en AJAX.<br />
                        Ça va nous demander de modifier <em>un peu</em> le code PHP.
                    </p>
                </div>
            </section>
            <hr />
            <footer class="row">
                <div class="col-md-6 col-md-offset-6 text-right">
                    <small>MMI 2015-2016 - <a href="https://github.com/hepl-mmi/meet-ajax">hepl-mmi/meet-ajax</a></small>
                </div>
            </footer>
        </main>

        <script type="text/template" id="form-error-message">
            <div class="alert alert-danger">
                <strong>Oops !</strong>
                On dirait que vous avez oublié de remplir chaque champs du formulaire !
            </div>
        </script>

        <script type="text/template" id="buddy-element">
            <div class="col-md-4">
                <div class="thumbnail" title="">
                    <img src="" alt="" width="90" height="90" />
                    <div class="caption">
                        <strong></strong>
                    </div>
                </div>
            </div>
        </script>

        <script src="js/jquery.min.js"></script>
        <script src="js/script.js"></script>
    </body>
</html>
