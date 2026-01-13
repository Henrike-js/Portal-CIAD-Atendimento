<?php
$host   = "localhost:3306";
$user   = "root";
$pass   = "Admin123";
$dbname = "banco_de_chamadas";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}


// ---- SALVAR ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'];
    $historico_existente = $_POST['historico_existente'];
    $novo_texto = trim($_POST['novo_texto']);

    // evita salvar vazio
    if ($novo_texto !== "") {

        $data = date("d/m/Y H:i");

        // concatena (sem apagar o antigo)
        $historico_final =
            $historico_existente .
            "\n\n---- Adicionado em $data ----\n" .
            $novo_texto;

        $sql = "UPDATE registros_chamadas 
                SET historico = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $historico_final, $id);

        if ($stmt->execute()) {
            header("Location: lista_chamadas.php?ok=1");
            exit;
        } else {
            echo "Erro ao atualizar: " . $stmt->error;
        }
    }
}


// ---- CARREGAR REGISTRO ----
$id = $_GET['id'];

$sql = "SELECT id,historico 
        FROM registros_chamadas 
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$registro = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Editar Histórico</title>

<style>
body{font-family:Arial;background:#f4f4f4;padding:20px}
form{background:white;padding:20px;border-radius:8px;max-width:700px}
textarea{width:100%;border:1px solid #ccc;border-radius:6px;padding:10px}
small{color:#666}
button{padding:10px 18px;background:#16325C;color:white;border:0;border-radius:6px;cursor:pointer}
a{margin-left:10px}
</style>

</head>
<body>

<h2>Editar Histórico – Chamada #<?= $registro['id'] ?></h2>

<form method="post">

    <input type="hidden" name="id" value="<?= $registro['id'] ?>">

    <label>Histórico atual (não pode alterar)</label><br>
    <textarea readonly rows="8"><?= htmlspecialchars($registro['historico']) ?></textarea>

    <input type="hidden" name="historico_existente" 
           value="<?= htmlspecialchars($registro['historico']) ?>">

    <br><br>

    <label>Adicionar novo registro</label>
    <small>(o texto será anexado ao histórico)</small>

    <textarea name="novo_texto" rows="6" placeholder="Digite o complemento..."></textarea>

    <br><br>

    <button type="submit">Salvar</button>
    <a href="lista_chamadas.php">Cancelar</a>

</form>

</body>
</html>

<?php
$conn->close();
?>

