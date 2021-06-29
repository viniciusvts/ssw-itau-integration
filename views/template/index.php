<?php
$SSW_ITAUI = new ssw_itaui_wp();
include SSW_ITAUI_PATH."/views/template/header.php";
?>
<h2>Home</h2>
<p>Status da integração:</p>
<?php
if($SSW_ITAUI->hasClientId()){ echo '<p>Client ID ok</p>'; }
else{
    echo '<p>Insira o Client ID. <a href="';
    menu_page_url(SSW_ITAUI_PLUGIN_SLUG.'-auth');
    echo '">Aqui</a></p>';
}

if($SSW_ITAUI->hasClientSecret()){ echo '<p>Client Secret ok</p>'; }
else{
    echo '<p>Insira o Client Secret. <a href="';
    menu_page_url(SSW_ITAUI_PLUGIN_SLUG.'-auth');
    echo '">Aqui</a></p>';
}

if($SSW_ITAUI->hasCode()){ echo '<p>Chave ok</p>'; }
else{
    echo '<p>Insira o Itau-chave. <a href="';
    menu_page_url(SSW_ITAUI_PLUGIN_SLUG.'-auth');
    echo '">Aqui</a></p>';
}
include SSW_ITAUI_PATH."/views/template/footer.php";
?>
<a href="<?php echo $boletoLink ?>">link para o boleto</a>