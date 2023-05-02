<?php

//Create connetion 
$servename = "localhost";
$username = "root";
$password = "1000005206";
$dbname = "flist";

$conn = new mysqli($servename, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "SELECT *FROM flist";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        //Prendere una dato a caso
        $result->data_seek(rand(0, $result->num_rows - 1));
        $row = $result->fetch_assoc();
        $titolo_casuale = $row['titolo'];
        $regista_casuale = $row['regista'];
        print "<h1> Film consigliato:</h1>" . $titolo_casuale . ($regista_casuale ? "($regista_casuale)" : "");
    }

    ?>
    <form method="POST" , action="$_SERVER['PHP_MYSELF']">
        <input type="text" name="titolo">
        <input type="text" name="regista">
        <input type="submit" name="trova" value="trova" />
        <input type="submit" name="wlist" value="Guarda il film presenti sulla wlist" />
    </form>
<?php
} else {
    //POST
    if (isset($_POST['trova'])) {
        //Cerco il film
        $titolo = $_POST['titolo'];
        $regista = $_POST['regista'];
        if ($titolo == "" && $regista == "") {
            print " <br><br><br><h1>Inserisci alemeno un criterio di ricerca</h1>";
            print ' <br><br> <a href="$_SERVER[PHP_MYSELF]" > Torna indieto </a>';
        } else {
            $sql = "SELECT * FROM flist WHERE ";
            $and = false;
            if ($titolo != "") {
                $sql .= " titolo ='$titolo' ";
                $and = true;
            }
            if ($regista != "") {
                $sql .= ($and ? "AND " : "") . "regista ='$regista'";
            }
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                print "<br><br><h1>Film richiest";
                print $result->num_rows == 1 ? "o" : "i";
                print ":</h1><br>";
                while ($row = $result->fetch_assoc()) {
                    print $row["titolo"] . " " . ($row["regista"] ? "($regista)" : " ") . "<br>";
                    print ' <br><br> <a href="$_SERVER[PHP_MYSELF]" > Torna indieto </a>';
                }
            } else {
                $sql = "SELECT * FROM wlist WHERE";
                $and = false;
                if ($titolo != "") {
                    $sql .= " titolo = '$titolo' ";
                    $and = true;
                }

                if ($regista != "") {
                    $sql .= ($and ? "AND " : "") . "regista ='$regista'";
                }
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    print "<br><br><h1>" . ($titolo != "" ? "film" : "regista") . " non trovato:</h1><br><i>" . ($titolo != "" ? "$titolo" : "$regista") . "</i> e' gia' presente nella whish list!";
                    print ' <br><br> <a href="$_SERVER[PHP_MYSELF]" > Torna indieto </a>';
                } else {
                    print "<br>";
                    print "<br>";
                    print "Vuoi aggiungerlo alla wish list ?";
                    ?>
                    <form method="POST" action="$_SERVER[PHP_MYSELF]">
                        <input type="text" name="titolo" readonly="readonly" value="<?php print $titolo; ?> ">
                        <input type="text" name="regista" readonly="readonly" value="<?php print $regista; ?> ">
                        <br>
                        <br>
                        <input type="submit" name="si" value="si">
                        <input type="submit" name="no" value="no">

                    </form>
                <?php
                }
            }
        }

    }
}

if (isset($_POST['si'])) {
    $titolo = $_POST['titolo'];
    $regista = $_POST['regista'];
    $sql = "INSERT INTO wlist (titolo,regista)  VALUES ('$titolo','$regista')";
    $conn->query($sql);
    if ($regista == "") {
        $descrizione = "del regista $regista";
    } else {
        $descrizione = "";
    }
    print "Il film $titolo" . $descrizione . " e stato inserito nella wlist";
    print "<br><br>";
    print ' <br><br> <a href="$_SERVER[PHP_MYSELF]" > Torna indieto </a>';

    //  header("location: $_SERVER[PHP_MYSELF]");

}

if (isset($_POST['no'])) {
    print ' <br><br> <a href="$_SERVER[PHP_MYSELF]" > Torna indieto </a>';
}

if (isset($_POST['wlist'])) {
    $sql = "SELECT * FROM wlist";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        ?>
        <b>
            <form action="$_SERVER[PHP_MYSELF]" method="POST">
                <input type="submit" name="elimina" value="Vuoi svuotare la wlists">
            </form>
        </b>
        <?php
         print " <b>La wlist è :</b>";
        print "<br>";
        print "<br>";

        while ($row = $result->fetch_assoc()) {
            print "<br>" . $row['titolo'] . "" . ($row['regista'] ? "(" . $row['regista'] . ")" : "");
        }

        print '<br><br> <a href="$_SERVER[PHP_MYSELF]"> Torna indietro </a>';

    } else {
        print 'La wlist è vuota';
        print ' <br><br> <a href=" $_SERVER[PHP_MYSELF]" > Torna indietro</a>';
    }

    //  header("location: $_SERVER[PHP_MYSELF]");

}


if(isset($_POST['elimina'])){
    $sql = "DELETE FROM wlist";
    $result=$conn->query($sql);
    print 'La wlist è stata svuotata';
    print ' <br><br> <a href=" $_SERVER[PHP_MYSELF]" > Torna indietro</a>';
}

?>