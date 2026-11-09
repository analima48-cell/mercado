<?php
require 'config.php';require 'includes/header.php';$categorias=$pdo->query('SELECT c.*,COUNT(p.id) total FROM categorias c LEFT JOIN produtos p ON p.categoria_id=c.id GROUP BY c.id ORDER BY c.nome')->fetchAll();
?>
<div class="eyebrow">Organização</div><h1 class="titulo">Categorias</h1><p class="subtitulo">Organize os produtos por departamento.</p><div class="secao"><h2>Categorias cadastradas</h2><a class="btn" href="categoria_form.php">+ Nova categoria</a></div>
<div class="painel"><table class="tabela"><thead><tr><th>Nome</th><th>Descrição</th><th>Produtos vinculados</th><th>Ações</th></tr></thead><tbody><?php foreach($categorias as $c): ?><tr><td><strong><?=e($c['nome'])?></strong></td><td><?=e($c['descricao']?:'—')?></td><td><?=e($c['total'])?></td><td><div class="acoes"><a class="btn small" href="categoria_form.php?id=<?=$c['id']?>">Editar</a><a class="btn small danger" href="categoria_excluir.php?id=<?=$c['id']?>">Excluir</a></div></td></tr><?php endforeach ?><?php if(!$categorias): ?><tr><td colspan="4" class="vazio">Nenhuma categoria cadastrada.</td></tr><?php endif ?></tbody></table></div>
<?php require 'includes/footer.php'; ?>
