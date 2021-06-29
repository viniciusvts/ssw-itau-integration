<?php
$SSW_ITAUI = new ssw_itaui_wp();
// verifica se há posts para configurar variáveis
if(isset($_POST['client_id'])){ $SSW_ITAUI->setClientId($_POST['client_id']); }
if(isset($_POST['client_secret'])){ $SSW_ITAUI->setClientSecret($_POST['client_secret']); }
if(isset($_POST['code'])){ $SSW_ITAUI->setCode($_POST['code']); }
// inicia a página
include SSW_ITAUI_PATH."/views/template/header.php";
?>
<h1>Autorização</h1>
<p>Insira as informações do itaú aqui</p>
<!-- Client ID -->
<?php
$placeholder = $SSW_ITAUI->hasClientId() ?
            'Informação salva, insira uma nova para sobrescrever' :
            'Insira a informação';
?>
<form method="POST" action="<?php $_SERVER['HTTP_REFERER'] ?>" class="authdata">
    <label for="client_id">Cliente ID</label>
    <input type="text" name="client_id" required
    placeholder="<?php echo $placeholder; ?>">
    <input type="submit" value="Atualizar">
</form>
<!-- Client Secret -->
<?php
$placeholder = $SSW_ITAUI->hasClientSecret() ?
            'Informação salva, insira uma nova para sobrescrever' :
            'Insira a informação';
?>
<form method="POST" action="<?php $_SERVER['HTTP_REFERER'] ?>" class="authdata">
    <label for="client_secret">Cliente Secret</label>
    <input type="text" name="client_secret" required
    placeholder="<?php echo $placeholder; ?>">
    <input type="submit" value="Atualizar">
</form>
<!-- Itaú chave -->
<?php
$placeholder = $SSW_ITAUI->hasCode() ?
            'Informação salva, insira uma nova para sobrescrever' :
            'Insira a informação';
?>
<form method="POST" action="<?php $_SERVER['HTTP_REFERER'] ?>" class="authdata">
    <label for="code">Itaú-chave</label>
    <input type="text" name="code" required
    placeholder="<?php echo $placeholder; ?>">
    <input type="submit" value="Atualizar">
</form>
<?php
include SSW_ITAUI_PATH."/views/template/footer.php";
?>