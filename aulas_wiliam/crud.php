<?php
$host = "localhost"; 
$user = "root"; 
$password = "root"; 
$dbname = "aulinhas"; 

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

if (isset($_POST['add_professor'])) {
    $nome = $conn->real_escape_string($_POST['nome_professores']);
    $email = $conn->real_escape_string($_POST['email_professores']);
    
    $sql = "INSERT INTO professores (nome_professores, email_professores) VALUES ('$nome', '$email')";
    if ($conn->query($sql) === TRUE) {
        echo "Novo professor adicionado com sucesso!";
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['add_aula'])) {
    $professor_id = (int) $_POST['professores_id'];
    $titulo = $conn->real_escape_string($_POST['titulo_aulas']);
    $descricao = $conn->real_escape_string($_POST['descricao_aulas']);
    $data = $_POST['data_aulas'];
    $horario = $_POST['horario_aulas'];
    
    $sql = "INSERT INTO aulas (professores_id, titulo_aulas, descricao_aulas, data_aulas, horario_aulas)
            VALUES ('$professor_id', '$titulo', '$descricao', '$data', '$horario')";
    if ($conn->query($sql) === TRUE) {
        echo "Nova aula adicionada com sucesso!";
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['mov_aula'])) {
    $id_aulas = (int) $_POST['id_aulas'];
    $novo_dia = $_POST['novo_dia_semana'];
    
    $sql = "UPDATE aulas SET data_aulas = '$novo_dia' WHERE id_aulas = '$id_aulas'";
    if ($conn->query($sql) === TRUE) {
        echo "Aula movimentada para " . $novo_dia . " com sucesso!";
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }
}

if (isset($_POST['delete_professor'])) {
    $professor_id = (int) $_POST['id_professores'];
    
    $sql_check = "SELECT * FROM aulas WHERE professores_id='$professor_id'";
    $result_check = $conn->query($sql_check);
    
    if ($result_check->num_rows > 0) {
        echo "Não é possível excluir o professor. Existem aulas associadas.";
    } else {
        $sql = "DELETE FROM professores WHERE id_professores='$professor_id'";
        if ($conn->query($sql) === TRUE) {
            echo "Professor excluído com sucesso!";
        } else {
            echo "Erro: " . $sql . "<br>" . $conn->error;
        }
    }
}
if (isset($_POST['delete_aula'])) {
    $id_aulas = (int) $_POST['id_aulas'];

    $sql = "DELETE FROM aulas WHERE id_aulas='$id_aulas'";
    if ($conn->query($sql) === TRUE) {
        echo "Aula excluída com sucesso!";
    } else {
        echo "Erro: " . $sql . "<br>" . $conn->error;
    }
}

$result_professores = $conn->query("SELECT * FROM professores");

$result_aulas = $conn->query("SELECT aulas.id_aulas, aulas.titulo_aulas, aulas.data_aulas, aulas.horario_aulas, professores.nome_professores 
                              FROM aulas 
                              INNER JOIN professores ON aulas.professores_id = professores.id_professores");

$conn->close();
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Aulas e Professores</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Sistema gerenciador de Aulas</h1>
    
<h2>Adicionar Professor</h2>
    <form method="post" action="">
        Nome: <input type="text" name="nome_professores" required><br>
        Email: <input type="email" name="email_professores" required><br>
        <input type="submit" name="add_professor" value="Adicionar Professor">
    </form>

    <h2>Adicionar Aula</h2>
    <form method="post" action="">
        Professor: 
        <select name="professores_id" required>
            <?php while($row = $result_professores->fetch_assoc()) { ?>
                <option value="<?php echo $row['id_professores']; ?>"><?php echo $row['nome_professores']; ?></option>
            <?php } ?>
        </select><br>
        Título: <input type="text" name="titulo_aulas" required><br>
        Descrição: <textarea name="descricao_aulas" required></textarea><br>
        Data: <input type="date" name="data_aulas" required><br>
        Horário: <input type="time" name="horario_aulas" required><br>
        <input type="submit" name="add_aula" value="Adicionar Aula">
    </form>

    <h2>Movimentar Aula no Diário</h2>
    <form method="post" action="">
        Aula: 
        <select name="id_aulas" required>
            <?php while($row = $result_aulas->fetch_assoc()) { ?>
                <option value="<?php echo $row['id_aulas']; ?>"><?php echo $row['titulo_aulas']; ?></option>
            <?php } ?>
        </select><br>
        Novo dia: 
        <select name="novo_dia_semana" required>
            <option value="2024-09-23">Segunda-feira</option>
            <option value="2024-09-24">Terça-feira</option>
            <option value="2024-09-25">Quarta-feira</option>
            <option value="2024-09-26">Quinta-feira</option>
            <option value="2024-09-27">Sexta-feira</option>
        </select><br>
        <input type="submit" name="mov_aula" value="Movimentar Aula">
    </form>

    <h2>Lista de Aulas</h2>
    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Data</th>
                <th>Horário</th>
                <th>Professor</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result_aulas->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['titulo_aulas']; ?></td>
                <td><?php echo $row['data_aulas']; ?></td>
                <td><?php echo $row['horario_aulas']; ?></td>
                <td><?php echo $row['nome_professores']; ?></td>
                <td>
                    <form method="post" action="">
                        <input type="hidden" name="id_aulas" value="<?php echo $row['id_aulas']; ?>">
                        <input type="submit" name="delete_aula" value="Excluir Aula">
                    </form>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <h2>Excluir Professor</h2>
    <form method="post" action="">
        Professor: 
        <select name="id_professores" required>
            <?php while($row = $result_professores->fetch_assoc()) { ?>
                <option value="<?php echo $row['id_professores']; ?>"><?php echo $row['nome_professores']; ?></option>
            <?php } ?>
        </select><br>
        <input type="submit" name="delete_professor" value="Excluir Professor">
    </form>
</body>
</html>
