<?php
/*
    Prosty skrypt do importu bazy danych IMDB
    Wymaga obecności 3 plików w tym samym katalogu:
    actors.list
    actresses.list
    genres.list
    Lista dostępna na stronie: http://www.imdb.com/interfaces
*/

// PLIK 

// ROZMIAR POJEDŃCZEGO KROKU:
$STEP_CNT = 1000;
// TABLICA Z gatunkami filmów - które ignoruje przy imporcie:
$filtered_genries = array ('Documentary', 'Biography', 'Reality-TV', 'News', 'Talk-Show', 'Game-Show');
// Możliwe opcje:
/*
+--------------+----------+
| genre        | count(*) |
+--------------+----------+
| Short        |   445310 |
| Drama        |   291323 |
| Comedy       |   217545 |
| Documentary  |   190893 |
| Adult        |    65490 |
| Action       |    56678 |
| Romance      |    56216 |
| Thriller     |    53979 |
| Animation    |    49747 |
| Family       |    46624 |
| Crime        |    40826 |
| Horror       |    40087 |
| Music        |    39530 |
| Adventure    |    35252 |
| Fantasy      |    30274 |
| Sci-Fi       |    26754 |
| Mystery      |    25637 |
| Biography    |    22281 |
| History      |    19055 |
| Sport        |    18876 |
| Musical      |    15849 |
| Western      |    14321 |
| War          |    14022 |
| Reality-TV   |    12088 |
| News         |     9756 |
| Talk-Show    |     8283 |
| Game-Show    |     4705 |
| Film-Noir    |      595 |
| Commercial   |        1 |
| Erotica      |        1 |
| Experimental |        1 |
| Lifestyle    |        1 |
+--------------+----------+
*/


mysql_connect_me ();
mysql_create_tables ();


// KROK ZERO - Liczymy rekordy w pliku, oraz importujemy tymczasową tabelkę z gatunkami filmów


$genries = array ();
//$count_all_genries = 0;
$count_all_records = 0;
$count_all_connections = 0;
$actor_files = imdb_open_actors ();
$genre_files = imdb_open_genries ();
while (($genre = imdb_get_genre ($genre_files)) !== FALSE) {
    $genries[] = $genre;
    $count_all_genries++;
    if ($count_all_genries % $STEP_CNT == 0) {
        add_genries ($genries);
        $genries = array ();
        print ("ST 0. ADDING GENRIES:\t $count_all_genries\n");
    }
}
print ("ST 0. ADDING GENRIES:\t $count_all_genries\n");

while (($actor = imdb_get_actor ($actor_files)) !== FALSE) {
    $count_all_records++;
    $count_all_connections += count ($actor['movies']);
    if ($count_all_records % $STEP_CNT == 0) {
        print ("ST 0.\tREKORDÓW: \tAKTORÓW: $count_all_records\t\tPOŁĄCZEŃ: $count_all_connections\n");
    }
}
print ("ST 0.\tREKORDÓW: \tAKTORÓW: $count_all_records\t\tPOŁĄCZEŃ: $count_all_connections\n");


// PIERWSZY KROK - ŁADUJEMY DANE DOT. AKTORÓW I FILMÓW, BEZ POWIĄZAŃ MIĘDZY NIMI


$file = imdb_open_actors ();
$licznik = 0;
$actors = array ();
$movies = array ();
$added_actors_count = 0;
$all_movies_cnt = 0;
while (($actor = imdb_get_actor ($file)) !== FALSE) {
    $actors[] = $actor['name'];
    $movies =  array_merge ($movies, $actor['movies']);
    $licznik++;
    if ($licznik % $STEP_CNT == 0 OR $licznik == $count_all_records) {
        $actors_cnt = count ($actors);
        $added_actors_count += $actors_cnt;
        add_actors ($actors);

        $movies = array_unique ($movies);
        $movies_cnt = count ($movies);

        $existing_movies = get_movies ($movies);
        $existing_movies_cnt = count ($existing_movies);

        foreach ($existing_movies AS $existing_movie) {
            $position = array_search ($existing_movie, $movies);
            unset ($movies[$position]);
        }

        // Sprawdzamy i odrzucamy te z filmów, które sa powiązane z zakazanymi gatunkami:
        $dropped_by_genree = 0;
        $genries = get_genries ($movies);
        foreach ($movies AS $mid => $movie) {
            if (is_title_forbidden ($movie)) {
                unset ($movies[$mid]);
                $dropped_by_genree++;
                continue;
            }
            if (isset ($genries[$movie])) {
                foreach ($filtered_genries AS $f_genree) {
                    if (array_search ($f_genree, $genries[$movie]) !== FALSE) {
                        unset ($movies[$mid]);
                        $dropped_by_genree++;
                        break;
                    }
                }
            }
        }

        $movies_add_cnt = count ($movies);
        $all_movies_cnt += $movies_add_cnt;
        add_movies ($movies);
        $progress = round  (($added_actors_count * 100) / $count_all_records, 2);
        $progress = number_format ((float) $progress, 2, '.', '');

        print ("ST 1.\tACTORS: PACK: $actors_cnt\tADDED: $added_actors_count\t ALL: $count_all_records \t($progress%) MOVIES: PACK: $movies_cnt\t EXISTS: $existing_movies_cnt\t BAD_GENREE: $dropped_by_genree\t ADDED: $movies_add_cnt \tALL: $all_movies_cnt\n");

        $actors = array ();
        $movies = array ();
   }
}


// KROK DRUGI - SPRZĄTAMY ŚMIECI


// zdublowane nazwy aktorów
print ("ST 2.\tEXECUTING CLEANUP #1\n");
$actors = make_query ("SELECT * FROM `actors` GROUP BY `name` HAVING count(*) > 1");
print ("ST 2.\tDUPLICATE ACTORS NAMES: " . count ($actors) . "\n");
if ( ! empty ($actors))
    make_query ("DELETE FROM `actors` WHERE `name` IN ('" . implode ("','", array_map (function ($actor) {return mysql_real_escape_string ($actor['name']);}, $actors)) . "') AND `id` NOT IN (" . implode (',', array_map (function ($actor) {return $actor['id'];}, $actors)) . ")");
// zdublowane nazwy filmów
$movies = make_query ("SELECT id FROM `movies` group by `title` having count(*) > 1");
print ("ST 2.\tDUPLICATE MOVIES NAMES: " . count ($movies) . "\n");
if ( ! empty ($movies))
    make_query ("DELETE FROM `movies` WHERE `id` IN (" . implode (',', $ids) . ")");
// usuwmy tabele z gatunkami filmowymi
make_query ("DROP TABLE `genries`");
print ("ST 2.\tDROPPED TABLE `GENRIES`\n");
print ("ST 2.\tCLEANUP DONE\n");


// KROK TRZECI - DODAJEMY POWIĄZANIA AKTOR -> FILM


$pack = array ();
$movies = array ();
$actors = array ();
$licznik = 0;
$a2m_skipped = 0;
$a2m_cnt = 0;
$a2m_to_add = 0;
$a2m_cnt_all = 0;
$file = imdb_open_actors ();

while (($actor = imdb_get_actor ($file)) !== FALSE) {
    $pack[$actor['name']] = array_unique ($actor['movies']);
    $actors[] = $actor['name'];
    $movies = array_merge ($movies, $actor['movies']);
    $licznik++;
    if ($licznik % $STEP_CNT == 0 OR $licznik == $count_all_records) {
        $movies = array_unique ($movies);
        $movies_pack = get_movies ($movies);
        $actors_pack = get_actors ($actors);
        $insert = array ();

        foreach ($pack AS $actor_name => $actor_movies) {
            $a2m_cnt += count ($actor_movies);
            $actor_id = array_search ($actor_name, $actors_pack);
            if ( ! $actor_id) {
                $a2m_skipped += count ($actor_movies);
                continue;
            }
            $insert[$actor_id] = array ();
            foreach ($actor_movies AS $movie) {
                $movie_id = array_search ($movie, $movies_pack);
                if ($movie_id) {
                    $a2m_to_add++;
                    $insert[$actor_id][] = $movie_id;
                } else {
                    $a2m_skipped++;
                }
            }
        }
        add_a2m ($insert);

        $a2m_cnt_all += $a2m_cnt;
        $progress = round  (($licznik * 100) / $count_all_records, 2);
        $progress = number_format ( (float) $progress, 2, '.', '');
        print ("ST 3.\tACTORS: $licznik\t ALL: $count_all_records \t($progress%) \tA2M: CURRENT: $a2m_cnt\t TO_ADD: $a2m_to_add\t SKIPPED: $a2m_skipped\tADDED: $a2m_cnt_all\t ALL: $count_all_connections\n");

        $a2m_cnt = 0;
        $a2m_to_add = 0;
        $a2m_skipped = 0;
        $pack = array ();
        $movies = array ();
        $actors = array ();
    }
}


// KROK CZWARTY - SPRZĄTAMY FILMY / AKTORÓW BEZ POWIĄZAŃ
print ("ST 4.\tEXECUTING CLEANUP #2\n");
// dodanie indeksu na `a2m`;
make_query ("ALTER IGNORE TABLE  `imdb`.`a2m` ADD UNIQUE  `a2m` (  `actor_id` ,  `movie_id` );");
print ("ST 4.\tADDED INDEX ON `a2m`\n");
// zrzucenie indeksu UNIQUE
make_query ("ALTER TABLE a2m DROP INDEX `a2m`");
print ("ST 4.\tDROPPED INDEX ON `a2m`\n");
// dodanie indeksu na actors
make_query ("ALTER TABLE `imdb`.`a2m` ADD INDEX `actor_id` (`actor_id`)");
print ("ST 4.\tADDED INDEX ON `a2m`.`actor_id`\n");
// dodanie indeksu na movies
make_query ("ALTER TABLE `imdb`.`a2m` ADD INDEX `movie_id` (`movie_id`)");
print ("ST 4.\tADDED INDEX ON `a2m`.`movie_id`\n");
// aktorzy bez linków
$actors = make_query ("SELECT `id` FROM `actors` LEFT JOIN `a2m` ON `actors`.`id` = `a2m`.`actor_id` WHERE `a2m`.`actor_id` IS NULL");
print ("ST 4.\tNO LINK ACTORS: " . count ($actors) . "\n");
if ( ! empty ($actors))
    make_query ("DELETE FROM `actors` WHERE `id` IN (" . implode (',', array_map (function ($actor) {return $actor['id']; }, $actors)) . ")");
// filmy bez linków
$movies = make_query ("SELECT `id` FROM `movies` LEFT JOIN `a2m` ON `movies`.`id` = `a2m`.`movie_id` WHERE `a2m`.`movie_id` IS NULL");
print ("ST 4.\tNO LINK MOVIES: " . count ($movies) . "\n");
if ( ! empty ($movies))
    make_query ("DELETE FROM `movies` WHERE `id` IN (" . implode (',', array_map (function ($movie) {return $movie['id']; }, $movies)) . ")");
print ("ST 4.\tCLEANUP DONE\n");


/*

    BLOK Z FUNKCJAMI

*/

function mysql_connect_me () {
    mysql_connect ('localhost', 'root', 't6t4hh894b');
    mysql_select_db ('imdb');
    mb_internal_encoding ("UTF-8");
    if (mysql_error ())
        die ('CONNECT_MYSQL ' . mysql_error ());
}


function mysql_create_tables () {

    make_query ("
        DROP TABLE IF EXISTS `cache`;
    ");

    make_query ("
        CREATE TABLE `cache` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `actor1` int(11) NOT NULL,
            `actor2` int(11) NOT NULL,
            `used_cnt` int(11) NOT NULL,
            `last_used` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `data` varchar(256) DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `actors` (`actor1`,`actor2`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8
    ");
    make_query ("
        DROP TABLE IF EXISTS `genries`;
    ");
    make_query ("
        CREATE TABLE `genries` (
            `title` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
            `genre` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
            KEY `title` (`title`(255))
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
    ");
    make_query ("
        DROP TABLE IF EXISTS `actors`;
    ");
    make_query ("
        CREATE TABLE `actors` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(128) NOT NULL,
            PRIMARY KEY (`id`),
            FULLTEXT KEY `name` (`name`)
        ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;
    ");
    make_query ("
        DROP TABLE IF EXISTS `movies`;
    ");
    make_query ("
        CREATE TABLE `movies` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(256) NOT NULL,
            PRIMARY KEY (`id`),
            Key `title` (`title`)
        ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_bin;
    ");
    make_query ("
        DROP TABLE IF EXISTS `a2m`;
    ");
    make_query ("
        CREATE TABLE `a2m` (
            `actor_id` int(11) NOT NULL,
            `movie_id` int(11) NOT NULL
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8
    ");
    return TRUE;
}


function make_query ($query) {
    $sql = mysql_query ($query);
    if (mysql_error ())
        die ("EXECUTING QUERY $query " . mysql_error (). "\n");
    if (substr ($query, 0, 6) != 'SELECT') {
        return $sql;
    }
        $ret = array ();
    while ($row = mysql_fetch_assoc ($sql)) {
        $ret[] = $row;
    }
    return $ret;
}


function imdb_open_actors () {
    $actors = @fopen ('actors.list', 'r');
    if ( ! $actors) {
        die ("CANT OPEN ACTORS.LIST\n");
    }
    $actresses = @fopen ('actresses.list', 'r');
    if ( ! $actresses)
        die ("CANT OPEN ACTRESS.LIST\n");

    // przewijamy pliki do odpowiednich pozycji:
    while (($line = fgets($actors, 4096)) !== false) {
        if (strpos ($line, 'THE ACTORS LIST') !== FALSE) {
            for ($x = 0; $x < 4; $x++)
                $tmp = fgets($actors, 4096);
            break;
        }
    }

    while (($line = fgets($actresses, 4096)) !== false) {
        if (strpos ($line, 'THE ACTRESSES LIST') !== FALSE) {
            for ($x = 0; $x < 4; $x++)
                $tmp = fgets($actresses, 4096);
            break;
        }
    }

    // ustawiamy domyślny do pobiernia danych:
    $GLOBALS['actors_file'] = 'actresses';

    return array ('actors' => $actors, 'actresses' => $actresses);
}


function imdb_get_actor ($files) {

    $bufor = fgets ($files[$GLOBALS['actors_file']], 4096);
    if (strpos ($bufor, '----------') !== FALSE) {
        // Mamy koniec pliku
        if ($GLOBALS['actors_file'] == 'actresses') {
            $GLOBALS['actors_file'] = 'actors';
            return imdb_get_actor ($files);
        }
        return FALSE;
    }

    $aktor = explode ("\t", $bufor);
    $imie = get_name ($aktor[0]);

    if (! isset ($aktor[1]))
        return FALSE;

    $movie = end ($aktor);
    $filmy = array ();
    if (strpos ($movie, '(archive footage)') === FALSE)
        $filmy[] = get_title ($movie);

    while (($movie = fgets ($files[$GLOBALS['actors_file']], 4096)) !== false) {
        if ($movie == "\n")
            break;
        if (strpos ($movie, '(archive footage)') !== FALSE)
            continue;

        $filmy[] = get_title ($movie);
    }
    return array (
        'name'      => $imie,
        'movies'    => $filmy,
        );
}


function imdb_open_genries () {
    $genres = @fopen ('genres.list', 'r');
    if ( ! $genres) {
        die ("CANT OPEN MOVIES.LIST\n");
    }
    while (($line = fgets ($genres, 4096)) !== false) {
        if (strpos ($line, 'THE GENRES LIST') !== FALSE) {
            for ($x = 0; $x < 2; $x++)
                $tmp = fgets ($genres, 4096);
            break;
        }
    }

    return $genres;
}


function imdb_get_genre ($genres) {
    if ( ! ($bufor = @fgets ($genres, 4096)))
        return FALSE;

    $tmp = explode ("\t", $bufor);
    $title = get_title ($tmp[0]);
    $title = $title['title'];
    $genre = trim (end ($tmp));
    return array ('title' => $title, 'genre' => $genre);
}


function get_title ($film) {
    $tmp = explode ('(', trim ($film));
    if (( ! isset ($tmp[1])) OR (! ($tt = (int) $tmp[1]) != 0)) {
        $tytul = trim ($tmp[0]);
        $rok = '';
    } else {
        $tytul = trim ($tmp[0]);
        $rok = (int) $tmp[1];
        $rok = " ($rok)";
    }
    // pozbywamy się cudzyslowia:
    if (strpos ($tytul, '"') === 0)
        $tytul = substr ($tytul, 1);
    if (substr ($tytul, -1, 1) == '"')
        $tytul = substr ($tytul, 0, strlen ($tytul) - 1);

    return ($tytul . $rok);
}


function get_name ($actor) {
    $imie = explode ('(', $actor);
    if (isset ($imie[1])) {
        $suffix = ' (' . $imie[1];
    } else {
        $suffix = '';
    }
    $imie = explode (',', $imie[0]);
    $imie = implode (' ', array_map (function ($part) {return trim ($part);}, array_reverse ($imie)));

    return $imie . $suffix;
}

// Funkcja przyjmuje
function add_a2m ($a2m) {
    $query = "";
    $insert = array ();

    foreach ($a2m AS $actor_id => $movies_ids) {
        foreach ($movies_ids AS $movie_id) {
            $insert[] = "($actor_id, $movie_id)";
        }
    }
    $insert = implode (',', $insert);
    mysql_query ("INSERT INTO `a2m` (`actor_id`, `movie_id`) VALUES $insert");
    if (mysql_error ())
        die ("INSERTING A2M" . mysql_error () . "\n");
}


function add_actors ($actors) {
    if (empty ($actors))
        return;
    $insert = "INSERT INTO `actors` (`name`) VALUES ('" . implode ("'),('", array_map (function ($name) {return mysql_real_escape_string ($name);}, $actors)) . "')";
    mysql_query ($insert);
    if (mysql_error ())
        die (" INSERTING ACTORS DATA:\n " . mysql_error () . "\n");
}


function add_movies ($movies) {
    if (empty ($movies))
        return;
    $insert = "INSERT INTO `movies` (`title`) VALUES ('" . implode ("'),('", array_map (function ($title) {return mysql_real_escape_string ($title);}, $movies)) . "')";
    mysql_query ($insert);
    if (mysql_error ())
        die (" INSERTING MOVIES DATA:\n " . mysql_error () . "\n");
}

function add_genries ($genries) {
    if (empty ($genries))
        return;
    $insert = array ();
    foreach ($genries AS $genrie)
        $insert[] = '(\'' . mysql_real_escape_string ($genrie['title']) . '\',\'' . mysql_real_escape_string ($genrie['genre']) . '\')';

    $insert = "INSERT INTO `genries` (`title`, `genre`) VALUES " . implode (',', $insert);
    mysql_query ($insert);
    if (mysql_error ())
        die (" INSERTING MOVIES DATA:\n " . mysql_error () . "\n");
}


// przyjmuje jako parametr tablice z tytulami filmow
// zwraca tablice array (id => tytul)
function get_movies ($movies) {
    $query = "SELECT * FROM `movies` WHERE `title` IN ('" . implode ("','", array_map (function ($title) {return mysql_real_escape_string ($title);}, $movies)) . "')";
    $sql = mysql_query ($query);
    if (mysql_error ())
        die ("SEARCHING MOVIES: \n" . mysql_error () . "\n");
    $ret = array ();
    while ($row = mysql_fetch_assoc ($sql)) {
        $ret[$row['id']] = $row['title'];
    }
    return $ret;
}


// przyjmuje jako parametr tablice z imionami aktorów
// zwraca tablice array (id => imie)
function get_actors ($actors) {
    $query = "SELECT * FROM `actors` WHERE `name` IN ('" . implode ("','", array_map (function ($name) {return mysql_real_escape_string ($name);}, $actors)) . "')";
    $sql = mysql_query ($query);
    if (mysql_error ())
        die ("SEARCHING ACTORS: \n" . mysql_error () . "\n");
    $ret = array ();
    while ($row = mysql_fetch_assoc ($sql)) {
        $ret[$row['id']] = $row['name'];
    }
    return $ret;
}


// Przyjmuje tablie z tytułami filmów
// Zwraca tablice => tytul => tablica gatunków
function get_genries ($movies) {
    $query = "SELECT `title`, group_concat(distinct(genre)) AS `genries` FROM `genries` WHERE `title` IN ('" . implode ("','", array_map (function ($title) {return mysql_real_escape_string ($title);}, $movies)) . "') GROUP BY `title`";
    $sql = mysql_query ($query);
    if (mysql_error ())
        die ("SEARCHING MOVIE GENRIES: \n" . mysql_error () . "\n");
    $ret = array ();
    while ($row = mysql_fetch_assoc ($sql)) {
        $ret[$row['title']] = explode (',', $row['genries']);
    }
    return $ret;
}


// funkcja sprawdza czy tutuł jest prawidłowy
// Czy nie jest rozdaniem nagrod, talk-showem czy czymś takim
function is_title_forbidden ($title) {
    // czy jest to rozdanie nagród
    $forbidden = array (
        'Awards',
    );

    foreach ($forbidden AS $no) {
        if (strpos ($title, $no) !== FALSE)
            return TRUE;
    }
    return FALSE;
}

?>
