<?php
require 'config.php'; require 'includes/header.php';
$id=(int)($_GET['id']??0);$edit=$id>0;$erros=[];
$p=['codigo'=>'','nome'=>'','categoria_id'=>'','preco'=>'','estoque'=>'0','estoque_minimo'=>'5','validade'=>'','descricao'=>''];
if($edit){$st=$pdo->prepare('SELECT * FROM produtos WHERE id=?');$st->execute([$id]);$atual=$st->fetch();if(!$atual){flash('erro','Produto não encontrado.');redirecionar('produtos.php');}$p=$atual;}
$categorias=$pdo->query('SELECT * FROM categorias ORDER BY nome')->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST'){
 foreach($p as $k=>$v)$p[$k]=trim($_POST[$k]??'');
 if($p['codigo']==='')$erros[]='O código é obrigatório.';
 if($p['nome']==='')$erros[]='O nome é obrigatório.';
 if(!filter_var($p['categoria_id'],FILTER_VALIDATE_INT)||!in_array((int)$p['categoria_id'],array_column($categorias,'id'))) $erros[]='Selecione uma categoria válida.';
 if(!is_numeric($p['preco'])||(float)$p['preco']<=0)$erros[]='O preço deve ser numérico e maior que zero.';
 if(filter_var($p['estoque'],FILTER_VALIDATE_INT)===false||(int)$p['estoque']<0)$erros[]='O estoque deve ser um número inteiro igual ou maior que zero.';
 if(filter_var($p['estoque_minimo'],FILTER_VALIDATE_INT)===false||(int)$p['estoque_minimo']<0)$erros[]='O estoque mínimo deve ser um inteiro igual ou maior que zero.';
 if(!validar_data($p['validade']))$erros[]='Informe uma data de validade válida.';
 $dup=$pdo->prepare('SELECT id FROM produtos WHERE codigo=? AND id<>?');$dup->execute([$p['codigo'],$id]);if($dup->fetch())$erros[]='Já existe um produto com esse código.';
 if(!$erros){
  if($edit){$st=$pdo->prepare('UPDATE produtos SET codigo=?,nome=?,categoria_id=?,preco=?,estoque=?,estoque_minimo=?,validade=?,descricao=? WHERE id=?');$st->execute([$p['codigo'],$p['nome'],$p['categoria_id'],$p['preco'],$p['estoque'],$p['estoque_minimo'],$p['validade']?:null,$p['descricao'],$id]);flash('sucesso','Produto atualizado com sucesso.');}
  else{$st=$pdo->prepare('INSERT INTO produtos(codigo,nome,categoria_id,preco,estoque,estoque_minimo,validade,descricao) VALUES(?,?,?,?,?,?,?,?)');$st->execute([$p['codigo'],$p['nome'],$p['categoria_id'],$p['preco'],$p['estoque'],$p['estoque_minimo'],$p['validade']?:null,$p['descricao']]);flash('sucesso','Produto cadastrado com sucesso.');}
  redirecionar('produtos.php');
 }
}
?>
<div class="eyebrow">Catálogo / Produtos</div><h1 class="titulo"><?=$edit?'Editar produto':'Novo produto'?></h1><p class="subtitulo">Preencha os dados abaixo. Os campos marcados com * são obrigatórios.</p>
<?php if($erros): ?><div class="alert erro"><?php foreach($erros as $er): ?>• <?=e($er)?><br><?php endforeach ?></div><?php endif ?>
<form method="post" class="painel" style="padding:22px"><div class="form-grid">
<div class="campo"><label>Código *</label><input name="codigo" maxlength="30" required value="<?=e($p['codigo'])?>" placeholder="Ex.: MER010"></div>
<div class="campo"><label>Nome do produto *</label><input name="nome" maxlength="120" required value="<?=e($p['nome'])?>" placeholder="Nome do produto"></div>
<div class="campo"><label>Categoria *</label><select name="categoria_id" required><option value="">Selecione</option><?php foreach($categorias as $c): ?><option value="<?=$c['id']?>" <?=$p['categoria_id']==$c['id']?'selected':''?>><?=e($c['nome'])?></option><?php endforeach ?></select><?php if(!$categorias): ?><span class="ajuda">Cadastre uma categoria antes de cadastrar produtos.</span><?php endif ?></div>
<div class="campo"><label>Preço (R$) *</label><input type="number" name="preco" min="0.01" step="0.01" required value="<?=e($p['preco'])?>" placeholder="0,00"></div>
<div class="campo"><label>Quantidade em estoque *</label><input type="number" name="estoque" min="0" step="1" required value="<?=e($p['estoque'])?>"></div>
<div class="campo"><label>Estoque mínimo *</label><input type="number" name="estoque_minimo" min="0" step="1" required value="<?=e($p['estoque_minimo'])?>"></div>
<div class="campo"><label>Data de validade</label><input type="date" name="validade" value="<?=e($p['validade'])?>"><span class="ajuda">Opcional. Produtos vencidos serão sinalizados.</span></div>
<div class="campo full"><label>Descrição</label><textarea name="descricao" rows="3" placeholder="Informações adicionais..." maxlength="1000"><?=e($p['descricao'])?></textarea></div>
</div><div class="form-acoes"><button class="btn" <?=$categorias?'':'disabled'?>>Salvar produto</button><a class="btn sec" href="produtos.php">Cancelar</a></div></form>
<?php require 'includes/footer.php'; ?>
