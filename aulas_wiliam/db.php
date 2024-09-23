<?php
// Conexão com o banco de dados MySQL
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "aulinhas";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se ocorreu algum erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão:
     " . $conn->connect_error);
}
?>