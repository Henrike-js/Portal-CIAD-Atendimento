<?php
$host   = "localhost:3306";
$user   = "root";
$pass   = "Admin123";
$dbname = "banco_de_chamadas";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Chamada não encontrada.");
}

$sql = "SELECT * FROM registros_chamadas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$registro = $result->fetch_assoc();

if (!$registro) {
    die("Registro não localizado.");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Visualizar Chamada</title>

<style>
body{font-family:Arial;background:#f4f4f4;padding:20px}
.container{
    background:white;
    padding:20px;
    border-radius:10px;
    max-width:950px;
    margin:auto;
}
h2{margin-bottom:10px}
.section{
    margin-top:18px;
    padding:12px;
    border:1px solid #ddd;
    border-radius:8px;
}
.section h3{
    margin:0 0 8px 0;
    font-size:17px;
}
.row{margin-bottom:6px}
.label{font-weight:bold}
pre{
    background:#f7f7f7;
    padding:10px;
    border-radius:6px;
    white-space:pre-wrap;
}
a{display:inline-block;margin-top:12px}
</style>
</head>

<body>

<div class="container">

<h2>Chamada #<?= $registro['id'] ?></h2>

<!-- TELEATENDENTE -->
<div class="section">
<h3>Teleatendente</h3>

<div class="row"><span class="label">Matrícula:</span> <?= htmlspecialchars($registro['matricula']) ?></div>
<div class="row"><span class="label">Nome:</span> <?= htmlspecialchars($registro['nome_teleatendente']) ?></div>
<div class="row">
    <span class="label">Data / Hora:</span>
    <?= htmlspecialchars($registro['data_atendimento']) ?>
    <?= htmlspecialchars($registro['hora_atendimento']) ?>
</div>
</div>

<!-- DESTINO / LOCAL -->
<div class="section">
<h3>Destino e Local da Chamada</h3>

<div class="row"><span class="label">Destino do Serviço:</span> <?= htmlspecialchars($registro['destino_servico']) ?></div>

<div class="row">
    <span class="label">Endereço:</span><br>
    <?= htmlspecialchars($registro['logradouro_chamada']) ?>
    <?= $registro['numero_chamada'] ? ', '.htmlspecialchars($registro['numero_chamada']) : '' ?>
    <?= $registro['complemento_chamada'] ? ' - '.htmlspecialchars($registro['complemento_chamada']) : '' ?><br>
    <?= htmlspecialchars($registro['bairro_chamada']) ?> -
    <?= htmlspecialchars($registro['municipio_chamada']) ?>
</div>

<div class="row"><span class="label">Telefone da ocorrência:</span> <?= htmlspecialchars($registro['telefone_chamada']) ?></div>
</div>

<!-- SOLICITANTE -->
<div class="section">
<h3>Solicitante</h3>

<div class="row"><span class="label">Nome:</span> <?= htmlspecialchars($registro['nome_solicitante']) ?></div>

<div class="row">
    <span class="label">Endereço:</span><br>
    <?= htmlspecialchars($registro['endereco_solicitante']) ?>
    <?= $registro['numero_solicitante'] ? ', '.htmlspecialchars($registro['numero_solicitante']) : '' ?>
    <?= $registro['complemento_solicitante'] ? ' - '.htmlspecialchars($registro['complemento_solicitante']) : '' ?><br>
    <?= htmlspecialchars($registro['bairro_solicitante']) ?> -
    <?= htmlspecialchars($registro['municipio_solicitante']) ?>
</div>

<div class="row"><span class="label">Telefone:</span> <?= htmlspecialchars($registro['telefone_solicitante']) ?></div>
</div>

<!-- HISTÓRICO -->
<div class="section">
<h3>Histórico / Natureza</h3>

<div class="row"><span class="label">Código natureza:</span> <?= htmlspecialchars($registro['codigo_natureza']) ?></div>

<pre><?= htmlspecialchars($registro['historico']) ?></pre>
</div>

<a href="lista_chamadas.php">⬅ Voltar</a>

</div>

</body>
</html>

<?php
$conn->close();
?>
