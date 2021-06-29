<?php
// +----------------------------------------------------------------------+
// | BoletoPhp - Versão Beta                                              |
// +----------------------------------------------------------------------+
// | Este arquivo está disponível sob a Licença GPL disponível pela Web   |
// | em http://pt.wikipedia.org/wiki/GNU_General_Public_License           |
// | Você deve ter recebido uma cópia da GNU Public License junto com     |
// | esse pacote; se não, escreva para:                                   |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Originado do Projeto BBBoletoFree que tiveram colaborações de Daniel |
// | William Schultz e Leandro Maniezo que por sua vez foi derivado do	  |
// | PHPBoleto de João Prado Maia e Pablo Martins F. Costa				        |
// | 														                                   			  |
// | Se vc quer colaborar, nos ajude a desenvolver p/ os demais bancos :-)|
// | Acesse o site do Projeto BoletoPhp: www.boletophp.com.br             |
// +----------------------------------------------------------------------+

// +----------------------------------------------------------------------+
// | Equipe Coordenação Projeto BoletoPhp: <boletophp@boletophp.com.br>   |
// | Desenvolvimento Boleto Itaú: Glauber Portella                        |
// +----------------------------------------------------------------------+



// +----------------------------------------------------------------------+
// | Editado por Vinicius de Santana <vinicius.vts@gmail.com              |
// | Para uso no plugin para wordpress                                    |
// +----------------------------------------------------------------------+

if (!isset($_GET['boleto'])){
    echo 'Não é possível gerar o boleto';
    die;
}

// DADOS DO BOLETO PARA O SEU CLIENTE
$boletoDataJson = base64_decode($_GET['boleto']);
$boletoData = json_decode($boletoDataJson);

$dadosboleto["nosso_numero"] = $boletoData->nosso_numero;  // Nosso numero - REGRA: Máximo de 8 caracteres!
$dadosboleto["numero_documento"] = $boletoData->numero_documento;	// Num do pedido ou nosso numero
$dadosboleto["data_vencimento"] = $boletoData->data_vencimento; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = $boletoData->data_documento; // Data de emissão do Boleto
$dadosboleto["data_processamento"] = $boletoData->data_processamento; // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $boletoData->valor_boleto;
// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] =  $boletoData->sacado;
$dadosboleto["endereco1"] =  $boletoData->endereco1;
$dadosboleto["endereco2"] =  $boletoData->endereco2;

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = $boletoData->demonstrativo1;
$dadosboleto["demonstrativo2"] = $boletoData->demonstrativo2;
$dadosboleto["demonstrativo3"] = $boletoData->demonstrativo3;
$dadosboleto["instrucoes1"] = $boletoData->instrucoes1;
$dadosboleto["instrucoes2"] = $boletoData->instrucoes2;
$dadosboleto["instrucoes3"] = $boletoData->instrucoes3;
$dadosboleto["instrucoes4"] = $boletoData->instrucoes4;

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = $boletoData->quantidade;
$dadosboleto["valor_unitario"] = $boletoData->valor_unitario;
$dadosboleto["aceite"] = $boletoData->aceite;
$dadosboleto["especie"] = $boletoData->especie;
$dadosboleto["especie_doc"] = $boletoData->especie_doc;


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


// DADOS DA SUA CONTA - ITAÚ
$dadosboleto["agencia"] = $boletoData->agencia;
$dadosboleto["conta"] = $boletoData->conta;
$dadosboleto["conta_dv"] = $boletoData->conta_dv;

// DADOS PERSONALIZADOS - ITAÚ
$dadosboleto["carteira"] = $boletoData->carteira;

// SEUS DADOS
$dadosboleto["identificacao"] = $boletoData->identificacao;
$dadosboleto["cpf_cnpj"] = $boletoData->cpf_cnpj;
$dadosboleto["endereco"] = $boletoData->endereco;
$dadosboleto["cidade_uf"] = $boletoData->cidade_uf;
$dadosboleto["cedente"] = $boletoData->cedente;

// NÃO ALTERAR!
include("funcoes_itau.php"); 
include("layout_itau.php");
?>