<?php

// Connexion à la base de données
try{

    // Appel des variables
    if(
        isset($_POST['name']) &&
        isset($_POST['species']) &&
        isset($_POST['birthdate'])
    ){

        // Bloc des vérifs

        // Vérif nom
        if(!preg_match('/^[a-z\'\- áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ ]{3,50}$/i', $_POST['name'])){
            $errors[] = 'Nom de l\'animal invalide';
        }
        // Vérif race
        if(!preg_match('/^[a-z\'\- áàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ ]{2,55}$/i', $_POST['species'])){
            $errors[] = 'Nom de l\'espèce invalide';
        }
        // Vérif date de naissance
        if(!preg_match('/^([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))$/', $_POST['birthdate'])){
            $errors[] = 'Date de naissance invalide';
        }
        // Si pas d'erreurs
        if(!isset($errors)){
            $bdd = new PDO('mysql:host=localhost;dbname=animals;charset=utf8', 'root', '');
            //Affichage des erreurs SQL si il y en a
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Requête préparée avec un marqueur, pour éviter les injections SQL
            $response = $bdd->prepare("INSERT INTO animals(name, species, birthdate) VALUES (?,?,?)");
            $response->execute([
                $_POST['name'],
                $_POST['species'],
                $_POST['birthdate']
            ]);
            // Si l'insertion a réussi (rowCount retournera 1), alors on crée un message de succès, sinon message d'erreur
            if($response->rowCount() > 0){
                $successMessage = 'Votre animal a bien été ajouté.';
            }else{
                $errors[] = 'Problème avec la BDD, veuillez réessayer.';
            }
            // Fermeture de la requête
            $response->closeCursor();
        }
    }
} catch(Exception $e){
    die('Il y a un problème sur la BDD : ' . $e->getMessage());
}

// Connexion à la base de données
try{
    $bdd = new PDO('mysql:host=localhost;dbname=animals;charset=utf8', 'root', '');
    //Affichage des erreurs SQL si il y en a
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(Exception $e){
    die('Il y a un problème sur la BDD : ' . $e->getMessage());
}

// Requête
$response = $bdd->query("SELECT * FROM animals");
$animals = $response->fetchAll(PDO::FETCH_ASSOC);

// Fermeture de la requête
$response->closeCursor();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        table, th, td {
            border: 1px solid black;
        }
    </style>
    <title>Ajouter un animal</title>
</head>
<body>
    <h1>Ajouter un animal</h1>
    <?php
    // Si le tableau $errors existe, alors on rentre dans cette condition
    if(isset($errors)){

        //On parcourt le tableau $errors pour afficher un msg d'erreur pour chaque erreur stockée dans l'array
        foreach($errors as $error){
            echo '<p style="color:red;">' . $error . '</p>';
        }
    }

    // Si le message de succès existe, on l'affiche
    if(isset($successMessage)){
        echo $successMessage;
    } else {

    ?>
    
    <form action="" method="POST">
        <label for="name">Nom :</label>
        <input type="text" name="name" placeholder="Truc">
    <br>
        <label for="species">Race :</label>
        <input type="text" name="species" placeholder="Dragon">
    <br>
        <label for="birthdate">Date de naissance :</label>
        <input type="date" name="birthdate" placeholder="29/09/2000">
    <br>
    <input type="submit">
    </form>
    <?php
    }
    ?>

    <h2>Tous nos animaux</h2>

    <?php

    // Si la BDD est vide, message d'erreur, sinon affichage du tableau
    if(empty($animals)){
        echo '<h3 style="color:red;">Oups ! Il n\'y a aucun animal à afficher.</h3>';
    } else {
        ?>
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Espèce</th>
                        <th>Date de naissance</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach($animals as $animal){
                    echo
                    "<tr>
                        <td>" . htmlspecialchars($animal['name']) . "</td>
                        <td>" . htmlspecialchars($animal['species']) . "</td>
                        <td>" . htmlspecialchars(date("d-m-Y", strtotime($animal['birthdate']))) . "</td>
                    </tr>";
                }
                ?>
                </tbody>
            </table>
    <?php
    }
    ?>

</body>
</html>