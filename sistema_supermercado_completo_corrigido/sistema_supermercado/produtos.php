<?php
require 'config.php'; require 'includes/header.php';
$q=trim($_GET['q']??''); $cat=(int)($_GET['categoria']??0);
$sql='SELECT p.*,c.nome categoria FROM produtos p JOIN categorias c ON c.id=p.categoria_id WHERE 1=1'; $args=[];
if($q!==''){ $sql.=' AND (p.nome LIKE ? OR p.codigo LIKE ?)'; $args[]="%$q%";$args[]="%$q%"; }
if($cat>0){$sql.=' AND p.categoria_id=?';$args[]=$cat;}
$sql.=' ORDER BY p.nome';$st=$pdo->prepare($sql);$st->execute($args);$produtos=$st->fetchAll();$categorias=$pdo->query('SELECT * FROM categorias ORDER BY nome')->fetchAll();
?>
<div class="eyebrow">Catálogo</div><h1 class="titulo">Produtos</h1><p class="subtitulo">Gerencie os produtos cadastrados no supermercado.</p>
<div class="secao"><h2>Todos os produtos <span class="badge"><?=count($produtos)?></span></h2><a class="btn" href="produto_form.php">+ Cadastrar produto</a></div>
<form class="filtro" method="get"><input type="search" name="q" placeholder="Buscar por nome ou código..." value="<?=e($q)?>"><select name="categoria"><option value="0">Todas as categorias</option><?php foreach($categorias as $c): ?><option value="<?=$c['id']?>" <?=$cat===$c['id']?'selected':''?>><?=e($c['nome'])?></option><?php endforeach ?></select><button class="btn">Buscar</button><a class="btn sec" href="produtos.php">Limpar</a></form>
<div class="painel"><table class="tabela"><thead><tr><th>Código</th><th>Produto</th><th>Categoria</th><th>Preço</th><th>Estoque</th><th>Validade</th><th>Ações</th></tr></thead><tbody>
<?php foreach($produtos as $p): ?><tr><td><?=e($p['codigo'])?></td><td><strong><?=e($p['nome'])?></strong></td><td><?=e($p['categoria'])?></td><td><?=dinheiro($p['preco'])?></td><td><span class="badge <?=$p['estoque']<=$p['estoque_minimo']?'baixo':'ok'?>"><?=$p['estoque']?></span></td><td><?=$p['validade']?date('d/m/Y',strtotime($p['validade'])):'—'?></td><td><div class="acoes"><a class="btn small sec" href="produto_detalhe.php?id=<?=$p['id']?>">Ver</a><a class="btn small" href="produto_form.php?id=<?=$p['id']?>">Editar</a><a class="btn small danger" href="produto_excluir.php?id=<?=$p['id']?>">Excluir</a></div></td></tr><?php endforeach ?>
<?php if(!$produtos): ?><tr><td colspan="7" class="vazio">Nenhum produto encontrado. Tente alterar os filtros.</td></tr><?php endif ?></tbody></table></div>
<?php require 'includes/footer.php'; ?>
