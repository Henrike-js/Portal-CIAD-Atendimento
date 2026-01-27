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
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Histórico</title>

    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+Pro:wght@400;600;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">

    <!-- CSS principal -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="page">

    <!-- TOPO -->
    <header class="topbar">
        <div class="topbar-inner">
            <div class="logo-wrapper">
                <img src="logo.png" alt="Logo" class="logo-sisp-img">
            </div>

            <div class="clock-wrapper">
                <div class="clock-time"><?= date('H:i') ?></div>
                <div class="clock-date"><?= date('d/m/Y') ?></div>
            </div>
        </div>
    </header>

    <!-- CONTEÚDO -->
    <main class="main">
        <div class="main-inner">

            <div class="page-header">
                <h1>Editar Histórico</h1>
                <p>Chamada nº <?= $registro['id'] ?></p>
            </div>

           <form method="post" style="width:100%">

    <input type="hidden" name="id" value="<?= $registro['id'] ?>">

    <!-- HISTÓRICO ATUAL -->
    <label class="label-block">HISTÓRICO ATUAL</label>
    <textarea readonly
        class="textarea-wide"
        style="white-space:pre-wrap;">
<?= htmlspecialchars($registro['historico']) ?>
    </textarea>

    <input type="hidden" name="historico_existente"
           value="<?= htmlspecialchars($registro['historico']) ?>">

    <!-- NOVO REGISTRO -->
    <label class="label-block">Adicionar novo</label>
    <textarea name="novo_texto"
        class="textarea-wide"
        placeholder="Digite o complemento..."></textarea>

    <div class="actions-row">
        <button type="submit" class="pill pill-active">Salvar</button>
        <a href="lista_chamadas.php" class="pill">Cancelar</a>
    </div>

</form>

        </div>
    </main>

    <!-- RODAPÉ -->
    <footer class="footer">
        © <?= date('Y') ?> – Sistema Integrado
    </footer>

</div>
</body>
</html>

<?php
$conn->close();
?>

