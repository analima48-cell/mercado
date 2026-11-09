<?php
require 'config.php';require 'includes/header.php';$id=(int)($_GET['id']??0);$st=$pdo->prepare('SELECT * FROM produtos WHERE id=?');$st->execute([$id]);$p=$st->fetch();
if(!$p){echo '<div class="alert erro">Produto não encontrado.</div><a class="btn" href="produtos.php">Voltar</a>';require 'includes/footer.php';exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){$del=$pdo->prepare('DELETE FROM produtos WHERE id=?');$del->execute([$id]);flash('sucesso','Produto excluído com sucesso.');redirecionar('produtos.php');}
?>
<div class="eyebrow">Catálogo / Exclusão</div><h1 class="titulo">Excluir produto</h1><div class="painel" style="padding:22px;max-width:620px"><div class="alert erro">Tem certeza de que deseja excluir este produto? Esta ação não poderá ser desfeita.</div><p><strong><?=e($p['nome'])?></strong> · Código <?=e($p['codigo'])?></p><form method="post"><div class="form-acoes"><button class="btn danger">Sim, excluir</button><a class="btn sec" href="produtos.php">Cancelar</a></div></form></div>
<?php require 'includes/footer.php'; ?>
