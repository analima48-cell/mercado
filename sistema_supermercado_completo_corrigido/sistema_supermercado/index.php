<?php
require 'config.php'; require 'includes/header.php';
$total=(int)$pdo->query('SELECT COUNT(*) FROM produtos')->fetchColumn();
$cats=(int)$pdo->query('SELECT COUNT(*) FROM categorias')->fetchColumn();
$baixo=(int)$pdo->query('SELECT COUNT(*) FROM produtos WHERE estoque <= estoque_minimo')->fetchColumn();
$vencidos=(int)$pdo->query('SELECT COUNT(*) FROM produtos WHERE validade IS NOT NULL AND validade < CURDATE()')->fetchColumn();
$recentes=$pdo->query('SELECT p.*,c.nome categoria FROM produtos p JOIN categorias c ON c.id=p.categoria_id ORDER BY p.id DESC LIMIT 6')->fetchAll();
?>
<div class="eyebrow">Visão geral</div><h1 class="titulo">Bom dia! 👋</h1><p class="subtitulo">Acompanhe os principais indicadores e a situação do seu supermercado.</p>
<div class="grid">
<div class="card"><div class="stat-icon">▦</div><div class="stat"><?=$total?></div><div class="stat-label">Produtos cadastrados</div></div>
<div class="card"><div class="stat-icon">▤</div><div class="stat"><?=$cats?></div><div class="stat-label">Categorias</div></div>
<div class="card"><div class="stat-icon">!</div><div class="stat"><?=$baixo?></div><div class="stat-label">Produtos com estoque baixo</div></div>
<div class="card"><div class="stat-icon">◷</div><div class="stat"><?=$vencidos?></div><div class="stat-label">Produtos vencidos</div></div>
</div>
<div class="secao"><h2>Produtos adicionados recentemente</h2><a class="btn" href="produto_form.php">+ Novo produto</a></div>
<div class="painel"><table class="tabela"><thead><tr><th>Código</th><th>Produto</th><th>Categoria</th><th>Preço</th><th>Estoque</th><th>Situação</th></tr></thead><tbody>
<?php foreach($recentes as $p): ?><tr><td><?=e($p['codigo'])?></td><td><strong><?=e($p['nome'])?></strong></td><td><?=e($p['categoria'])?></td><td><?=dinheiro($p['preco'])?></td><td><?=e($p['estoque'])?></td><td><?php if($p['validade'] && $p['validade']<date('Y-m-d')): ?><span class="badge vencido">Vencido</span><?php elseif($p['estoque']<=$p['estoque_minimo']): ?><span class="badge baixo">Estoque baixo</span><?php else: ?><span class="badge ok">Normal</span><?php endif ?></td></tr><?php endforeach ?>
<?php if(!$recentes): ?><tr><td colspan="6" class="vazio">Nenhum produto cadastrado.</td></tr><?php endif ?></tbody></table></div>
<div class="secao"><h2>Acesso rápido</h2></div><div class="grid"><a class="card" href="produtos.php" style="text-decoration:none;color:inherit"><b>📦 Produtos</b><p class="stat-label">Consultar, pesquisar e editar produtos.</p></a><a class="card" href="categorias.php" style="text-decoration:none;color:inherit"><b>🗂 Categorias</b><p class="stat-label">Organizar os departamentos.</p></a><a class="card" href="estoque.php" style="text-decoration:none;color:inherit"><b>⚠ Estoque</b><p class="stat-label">Ver alertas e validade.</p></a><a class="card" href="categoria_form.php" style="text-decoration:none;color:inherit"><b>＋ Nova categoria</b><p class="stat-label">Cadastrar uma categoria.</p></a></div>
<?php require 'includes/footer.php'; ?>
