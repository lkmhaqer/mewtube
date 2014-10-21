<?php
$query = "SELECT episodes.name, episodes.filename
FROM ewwtube.episodes
WHERE name LIKE '%S04%'
UNION ALL
SELECT movies.title, movies.filename
FROM ewwtube.movies
WHERE title LIKE '%Zoo%'";
?>
