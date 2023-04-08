<?php

Header('Access-Control-Allow-Origin : *');
Header('Access-Control-Allow-Headers : *');
Header('Access-Control-Allow-Methods : GET, POST, PUT, DELETE, PATCH, OPTIONS');


require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../src/Models/Db.php';

use App\Models\DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;


$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);


// Action sur la table immo_admins

// Creer un admin
$app->post('/api/admin/create', function (Request $request, Response $response, array $args) {

});

// modifier un admin 

$app->put('/api/admin/update/{id}', function (Request $request, Response $response, array $args) {
    $idadmin = $request->getAttribute('id');

    $data = $request->getParsedBody();

    $usernameadmin = $data['usernameadmin'];
    $emailadmin = $data['emailadmin'];
    $teladmin = $data['teladmin'];
    $passwordadmin = password_hash($data['passwordadmin'], PASSWORD_DEFAULT);
    $dateadmin = $data['dateadmin'];

    $sql = "UPDATE immo_admins SET usernameadmin = :usernameadmin, emailadmin = :emailadmin, teladmin = :teladmin, passwordadmin = :passwordadmin , dateadmin = :dateadmin WHERE idadmin = :idadmin";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':usernameadmin', $usernameadmin);
        $stmt->bindParam(':emailadmin', $emailadmin);
        $stmt->bindParam(':teladmin', $teladmin);
        $stmt->bindParam(':dateadmin', $dateadmin);
        $stmt->bindParam(':passwordadmin', $passwordadmin);
        $stmt->bindParam(':idadmin', $idadmin);

        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "Profil mis à jour avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Prrofil introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// supprimer un admin  

$app->delete('/api/admin/delete/{id}', function (Request $request, Response $response, array $args) {
    $idadmin = $args['id'];
    $sql = "DELETE FROM immo_admins WHERE idadmin = :idadmin";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idadmin', $idadmin);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "Profil supprimée avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Profil introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// select par id
$app->get('/api/admin/get/{id}', function (Request $request, Response $response, array $args) {

    $idadmin = $request->getAttribute('id');

    $sql = "SELECT * FROM immo_admins WHERE idadmin = :idadmin";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idadmin', $idadmin);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        if ($admin) {
            $response->getBody()->write(json_encode($admin));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Profil introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});


// selectionner tous les admins
$app->get('/api/admin/get', function (Request $request, Response $response, array $args) {

    $sql = "SELECT * FROM immo_admins ";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $admin = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $response->getBody()->write(json_encode($admin));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// table immo_portfolio

// Créer un portfolio
$app->post('/api/portfolio/create', function (Request $request, Response $response, array $args) {
    // Récupérer les données du formulaire d'inscription
    $data = $request->getParsedBody();

    $imgport = $data["imgport"];
    $titreport = $data["titreport"];
    $dateport = $data["dateport"];

    if (!isset($imgport) || !isset($titreport) || !isset($dateport)) {
        $error = array(
            "message" => "Tous les champs obligatoires doivent être fournis"
        );
        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(400);
    }

    // Enregistrement dans la base de données
    $sql = "INSERT INTO immo_portfolios(imgport, titreport, dateport) VALUES (:imgport, :titreport, :dateport)";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':imgport', $imgport);
        $stmt->bindParam(':titreport', $titreport);
        $stmt->bindParam(':dateport', $dateport);

        $stmt->execute();
        $lastInsertId = $conn->lastInsertId();
        $db = null;

        $responseBody = array(
            "id_port" => $lastInsertId,
            "titreport" => $titreport
        );

        $response->getBody()->write(json_encode($responseBody));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(201);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// modifier un portfolio
$app->put('/api/portfolio/update/{id}', function (Request $request, Response $response, array $args) {
    $idport = $request->getAttribute('id');
    $data = $request->getParsedBody();

    $imgport = $data['imgport'];
    $titreport = $data['titreport'];
    $dateport = $data['dateport'];

    $sql = "UPDATE immo_portfolios SET imgport = :imgport, titreport = :titreport, dateport = :dateport WHERE idport = :idport";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':imgport', $imgport);
        $stmt->bindParam(':titreport', $titreport);
        $stmt->bindParam(':dateport', $dateport);
        $stmt->bindParam(':idport', $idport);

        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "Portfolio mis à jour avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Portfolio introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// supprimer un portfolio

$app->delete('/api/portfolio/delete/{id}', function (Request $request, Response $response, array $args) {
    $idport = $args['id'];
    $sql = "DELETE FROM immo_portfolios WHERE idport = :idport";
    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idport', $idport);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "Portfolio supprimé avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Portfolio introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// sélectionner un portfolio par ID
$app->get('/api/portfolio/get/{id}', function (Request $request, Response $response, array $args) {
    $idport = $request->getAttribute('id');

    $sql = "SELECT * FROM immo_portfolios WHERE idport = :idport";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idport', $idport);
        $stmt->execute();
        $portfolio = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        if ($portfolio) {
            $response->getBody()->write(json_encode($portfolio));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Portfolio introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// sélectionner tous les portfolios
$app->get('/api/portfolio/get', function (Request $request, Response $response, array $args) {
    $sql = "SELECT * FROM immo_portfolios ";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $portfolios = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $response->getBody()->write(json_encode($portfolios));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});


// Table produits

// Creer un produit
$app->post('/api/produit/create', function (Request $request, Response $response, array $args) {
    // Récupérer les données du formulaire
    $data = $request->getParsedBody();

    $typeprod = $data["typeprod"];
    $imgprod = $data["imgprod"];
    $titreprod = $data["titreprod"];
    $descriptionprod = $data["descriptionprod"];
    $dateprod = $data["dateprod"];

    if (!isset($typeprod) || !isset($imgprod) || !isset($titreprod) || !isset($descriptionprod) || !isset($dateprod)) {
        $error = array(
            "message" => "Tous les champs obligatoires doivent être fournis"
        );
        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(400);
    }

    // Enregistrement dans la base de données
    $sql = "INSERT INTO immo_produits(typeprod, imgprod, titreprod, descriptionprod, dateprod) VALUES (:typeprod, :imgprod, :titreprod, :descriptionprod, :dateprod)";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':typeprod', $typeprod);
        $stmt->bindParam(':imgprod', $imgprod);
        $stmt->bindParam(':titreprod', $titreprod);
        $stmt->bindParam(':descriptionprod', $descriptionprod);
        $stmt->bindParam(':dateprod', $dateprod);

        $stmt->execute();
        $lastInsertId = $conn->lastInsertId();
        $db = null;

        $responseBody = array(
            "id_prod" => $lastInsertId,
            "typeprod" => $typeprod
        );

        $response->getBody()->write(json_encode($responseBody));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(201);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }

});

// Modifier un produit
$app->put('/api/produits/update/{id}', function (Request $request, Response $response, array $args) {
    $idprod = $request->getAttribute('id');
    $data = $request->getParsedBody();
    $typeProd = $data['typeProd'];
    $imgProd = $data['imgProd'];
    $titreProd = $data['titreProd'];
    $descriptionProd = $data['descriptionProd'];
    $dateProd = $data['dateProd'];

    $sql = "UPDATE immo_produits SET typeProd = :typeProd, imgProd = :imgProd, titreProd = :titreProd, descriptionProd = :descriptionProd, dateProd = :dateProd WHERE idprod = :idprod";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':typeProd', $typeProd);
        $stmt->bindParam(':imgProd', $imgProd);
        $stmt->bindParam(':titreProd', $titreProd);
        $stmt->bindParam(':descriptionProd', $descriptionProd);
        $stmt->bindParam(':dateProd', $dateProd);
        $stmt->bindParam(':idprod', $idprod);

        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "Produit mis à jour avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Produit introuvable."
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

//supprimer un produit
$app->delete('/api/produit/delete/{id}', function (Request $request, Response $response, array $args) {
    $idprod = $request->getAttribute('id');

    $sql = "DELETE FROM immo_produits WHERE idprod = :idprod";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':idprod', $idprod);

        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "Produit supprimé avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Produit introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// sélectionner un produit par ID
$app->get('/api/produit/get/{id}', function (Request $request, Response $response, array $args) {
    $idprod = $request->getAttribute('id');
    $sql = "SELECT * FROM immo_produits WHERE idprod = :idprod";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idprod', $idprod);
        $stmt->execute();
        $produit = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        if ($produit) {
            $response->getBody()->write(json_encode($produit));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Produit introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// selectionner ton les produits
$app->get('/api/produit/get', function (Request $request, Response $response, array $args) {
    $sql = "SELECT * FROM immo_produits";
    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $produits = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $response->getBody()->write(json_encode($produits));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});


// Action sur la table immo_projets

// Creer un projet
$app->post('/api/projets/create', function (Request $request, Response $response, array $args) {
    // Récupérer les données du formulaire de création de projet
    $data = $request->getParsedBody();

    $typeproj = $data["typeproj"];
    $imgproj = $data["imgproj"];
    $titreproj = $data["titreproj"];
    $descriptionproj = $data["descriptionproj"];
    $dateproj = $data["dateproj"];

    if (!isset($typeproj) || !isset($imgproj) || !isset($titreproj) || !isset($descriptionproj) || !isset($dateproj)) {
        $error = array(
            "message" => "Tous les champs obligatoires doivent être fournis"
        );
        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(400);
    }

    // Enregistrement dans la base de données
    $sql = "INSERT INTO immo_projets(typeproj, imgproj, titreproj, descriptionproj, dateproj) VALUES (:typeproj, :imgproj, :titreproj, :descriptionproj, :dateproj)";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':typeproj', $typeproj);
        $stmt->bindParam(':imgproj', $imgproj);
        $stmt->bindParam(':titreproj', $titreproj);
        $stmt->bindParam(':descriptionproj', $descriptionproj);
        $stmt->bindParam(':dateproj', $dateproj);

        $stmt->execute();
        $lastInsertId = $conn->lastInsertId();
        $db = null;

        $responseBody = array(
            "id_proj" => $lastInsertId,
            "typeproj" => $typeproj
        );

        $response->getBody()->write(json_encode($responseBody));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(201);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// Modifier un projet

$app->put('/api/projets/update/{id}', function (Request $request, Response $response, array $args) {
    $idproj = $request->getAttribute('id');

    $data = $request->getParsedBody();

    $typeproj = $data['typeproj'];
    $imgproj = $data['imgproj'];
    $titreproj = $data['titreproj'];
    $descriptionproj = $data['descriptionproj'];
    $dateproj = $data['dateproj'];

    $sql = "UPDATE immo_projets SET typeproj = :typeproj, imgproj = :imgproj, titreproj = :titreproj, descriptionproj = :descriptionproj, dateproj = :dateproj WHERE idproj = :idproj";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':typeproj', $typeproj);
        $stmt->bindParam(':imgproj', $imgproj);
        $stmt->bindParam(':titreproj', $titreproj);
        $stmt->bindParam(':descriptionproj', $descriptionproj);
        $stmt->bindParam(':dateproj', $dateproj);
        $stmt->bindParam(':idproj', $idproj);

        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "Projet mis à jour avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Projet introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// supprimer un projet

$app->delete('/api/projets/delete/{id}', function (Request $request, Response $response, array $args) {
    $idproj = $args['id'];
    $sql = "DELETE FROM immo_projets WHERE idproj = :idproj";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idproj', $idproj);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "Projet supprimé avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Projet introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// select par id
$app->get('/api/projet/get/{id}', function (Request $request, Response $response, array $args) {
    $idproj = $request->getAttribute('id');

    $sql = "SELECT * FROM immo_projets WHERE idproj = :idproj";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idproj', $idproj);
        $stmt->execute();
        $projet = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        if ($projet) {
            $response->getBody()->write(json_encode($projet));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Projet introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// selectionner tous les projets
$app->get('/api/projet/get', function (Request $request, Response $response, array $args) {
    $sql = "SELECT * FROM immo_projets ";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $projet = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $response->getBody()->write(json_encode($projet));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});


// Action sur la table immo_realisations

// Créer une réalisation
$app->post('/api/realisation/create', function (Request $request, Response $response, array $args) {

    // Récupérer les données du formulaire de création de réalisation
    $data = $request->getParsedBody();

    $typeReal = $data["typeReal"];
    $imgReal = $data["imgReal"];
    $titreReal = $data["titreReal"];
    $descriptionReal = $data["descriptionReal"];
    $dateReal = $data["dateReal"];

    if (!isset($typeReal) || !isset($imgReal) || !isset($titreReal) || !isset($descriptionReal) || !isset($dateReal)) {
        $error = array(
            "message" => "Tous les champs obligatoires doivent être fournis"
        );
        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(400);
    }

    // Enregistrement dans la base de données
    $sql = "INSERT INTO immo_realisations(typeReal, imgReal, titreReal, descriptionReal, dateReal) VALUES (:typeReal, :imgReal, :titreReal, :descriptionReal, :dateReal)";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':typeReal', $typeReal);
        $stmt->bindParam(':imgReal', $imgReal);
        $stmt->bindParam(':titreReal', $titreReal);
        $stmt->bindParam(':descriptionReal', $descriptionReal);
        $stmt->bindParam(':dateReal', $dateReal);

        $stmt->execute();
        $lastInsertId = $conn->lastInsertId();
        $db = null;

        $responseBody = array(
            "id_realisation" => $lastInsertId,
            "titreReal" => $titreReal
        );

        $response->getBody()->write(json_encode($responseBody));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(201);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }

});

// Modifier une réalisation

$app->put('/api/realisation/update/{id}', function (Request $request, Response $response, array $args) {
    $idReal = $request->getAttribute('id');
    $data = $request->getParsedBody();

    $typeReal = $data['typeReal'];
    $imgReal = $data['imgReal'];
    $titreReal = $data['titreReal'];
    $descriptionReal = $data['descriptionReal'];
    $dateReal = $data['dateReal'];

    $sql = "UPDATE immo_realisations SET typeReal = :typeReal, imgReal = :imgReal, titreReal = :titreReal, descriptionReal = :descriptionReal, dateReal = :dateReal WHERE idReal = :idReal";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':typeReal', $typeReal);
        $stmt->bindParam(':imgReal', $imgReal);
        $stmt->bindParam(':titreReal', $titreReal);
        $stmt->bindParam(':descriptionReal', $descriptionReal);
        $stmt->bindParam(':dateReal', $dateReal);
        $stmt->bindParam(':idReal', $idReal);

        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "Réalisation mise à jour avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Réalisation introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// supprimer une réalisation

$app->delete('/api/realisations/delete/{id}', function (Request $request, Response $response, array $args) {
    $idreal = $args['id'];
    $sql = "DELETE FROM immo_realisations WHERE idreal = :idreal";
    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idreal', $idreal);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "Réalisation supprimée avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Réalisation introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// select par id
$app->get('/api/realisation/get/{id}', function (Request $request, Response $response, array $args) {
    $idreal = $request->getAttribute('id');

    $sql = "SELECT * FROM immo_realisations WHERE idreal = :idreal";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idreal', $idreal);
        $stmt->execute();
        $realisation = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        if ($realisation) {
            $response->getBody()->write(json_encode($realisation));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Réalistion introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// selectionner toutes les réalisations
$app->get('/api/realisation/get', function (Request $request, Response $response, array $args) {
    $sql = "SELECT * FROM immo_realisations ";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $realisations = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $response->getBody()->write(json_encode($realisations));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// Action sur la table immo_videotheques

// Ajouter une vidéo
$app->post('/api/videotheques/create', function (Request $request, Response $response, array $args) {
    // Récupérer les données du formulaire d'ajout
    $data = $request->getParsedBody();

    $linkvideo = $data["linkvideo"];
    $titrevideo = $data["titrevideo"];
    $datevideo = $data["datevideo"];

    if (!isset($linkvideo) || !isset($titrevideo) || !isset($datevideo)) {
        $error = array(
            "message" => "Tous les champs obligatoires doivent être fournis"
        );
        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(400);
    }

    // Enregistrement dans la base de données
    $sql = "INSERT INTO immo_videotheques(linkvideo, titrevideo, datevideo) VALUES (:linkvideo, :titrevideo, :datevideo)";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':linkvideo', $linkvideo);
        $stmt->bindParam(':titrevideo', $titrevideo);
        $stmt->bindParam(':datevideo', $datevideo);

        $stmt->execute();
        $lastInsertId = $conn->lastInsertId();
        $db = null;

        $responseBody = array(
            "id_video" => $lastInsertId,
            "titrevideo" => $titrevideo
        );

        $response->getBody()->write(json_encode($responseBody));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(201);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// Modifier une vidéo dans la table immo_videotheques

$app->put('/api/videos/update/{id}', function (Request $request, Response $response, array $args) {
    $idvideo = $request->getAttribute('id');
    $data = $request->getParsedBody();

    $linkvideo = $data['linkvideo'];
    $titrevideo = $data['titrevideo'];
    $datevideo = $data['datevideo'];

    $sql = "UPDATE immo_videotheques SET linkvideo = :linkvideo, titrevideo = :titrevideo, datevideo = :datevideo WHERE idvideo = :idvideo";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':linkvideo', $linkvideo);
        $stmt->bindParam(':titrevideo', $titrevideo);
        $stmt->bindParam(':datevideo', $datevideo);
        $stmt->bindParam(':idvideo', $idvideo);

        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "Vidéo mise à jour avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Vidéo introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// supprimer une vidéotheque

$app->delete('/api/videotheque/delete/{id}', function (Request $request, Response $response, array $args) {
    $idvideo = $args['id'];
    $sql = "DELETE FROM immo_videotheques WHERE idvideo = :idvideo";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idvideo', $idvideo);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "Vidéothèque supprimée avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Vidéothèque introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// select par id
$app->get('/api/videotheque/get/{id}', function (Request $request, Response $response, array $args) {
    $idvideo = $request->getAttribute('id');

    $sql = "SELECT * FROM immo_videotheques WHERE idvideo = :idvideo";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idvideo', $idvideo);
        $stmt->execute();
        $videotheque = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        if ($videotheque) {
            $response->getBody()->write(json_encode($videotheque));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Videotheque introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// selectionner toutes les videotheques
$app->get('/api/videotheque/get', function (Request $request, Response $response, array $args) {
    $sql = "SELECT * FROM immo_videotheques ";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $videotheques = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $response->getBody()->write(json_encode($videotheques));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

$app->run();