<?php

$host = 'localhost:3307';
$dbname = 'dailytrip_0';
$username = 'root';  // Remplacez par votre nom d'utilisateur
$password = '';      // Remplacez par votre mot de passe
$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

try {
  $pdo = new PDO($dsn, $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo 'Connection failed: ' . $e->getMessage();
  exit;
}


function getItems(PDO $pdo, string $table)
{
  // on écrit la requête SQL
  $query = "SELECT * FROM $table";

  // on réalise une préparation de la requête (statement)
  $stmt = $pdo->prepare($query);

  // on exécute la requête
  $stmt->execute();

  // on  retourne les données sous forme d'un tableau associatif
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
};

function getTrips(PDO $pdo)
{
  // on écrit la requête SQL
  $query = "SELECT t.title, t.cover, c.name, l.distance, l.duration
  FROM trips t 
  INNER JOIN category c on t.category_id = c.id 
  INNER JOIN localisation l on t.localisation_id = l.id";

  // on réalise une préparation de la requête (statement)
  $stmt = $pdo->prepare($query);

  // on exécute la requête
  $stmt->execute();

  // on  retourne les données sous forme d'un tableau associatif
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
};


$trips = getTrips($pdo);
// $reviews = getItems($pdo, "review");
// $ratings = getItems($pdo, "rating");
// var_dump($trips);
// die();
// var_dump(count($trips));
// var_dump(count($reviews));
// var_dump(count($ratings));
?>

<article class="article-list-trips grid grid-cols-6 gap-6 mx-10">
  <?php foreach ($trips as $trip):  ?>
    <div class="relative w-full h-[400px] bg-green-500 rounded-lg">
      <p class="absolute -top-4 right-0 bg-black text-white py-1 px-2 rounded-lg"><?= $trip['name'] ?></p>
      <img src=<?= $trip['cover'] ?> alt=<?= $trip['title'] ?> class="w-full h-full rounded-lg">
      <div class="absolute text-white z-1 bottom-0 p-3 bg-gradient-to-b from-black to-black w-full h-[100px] rounded-b-lg flex flex-col justify-between ">
        <h1 class=" font-bold text-white truncate overflow-hidden"><?= $trip['title'] ?></h1>
        <h4 class="italic text-white"><?= $trip['duration'] ?></h4>
        <p class="italic text-[14px]"><?= $trip['distance'] ?> dam</p>
      </div>
    </div>
  <?php endforeach; ?>

</article>