<?php
if(!function_exists('getBoletoLink')){
    /**
     * Organiza os dados e gera um link para a geração de boleto
     * @param array $apiResponse - os dados retornados pela api do itaú
     */
    function getBoletoLink($apiReponse){
        $boletoData = array(
            'nosso_numero' => substr($apiReponse->nosso_numero, 0, -1),
            'numero_documento' => substr($apiReponse->nosso_numero, 0, -1),
            'data_vencimento' => date("d/m/Y", strtotime($apiReponse->vencimento_titulo)),
            'data_documento' =>  date("d/m/Y", strtotime($apiReponse->data_emissao)),
            'data_processamento' =>  date("d/m/Y", strtotime($apiReponse->data_processamento)),
            'valor_boleto' => number_format($apiReponse->valor_titulo, 2, ',', ''),
            'sacado' => $apiReponse->pagador->nome_razao_social_pagador,
            'endereco1' => $apiReponse->pagador->logradouro_pagador,
            'endereco2' => $apiReponse->pagador->cidade_pagador . ' ' .
                            $apiReponse->pagador->uf_pagador . ' ' .
                            $apiReponse->pagador->cep_pagador,
            'demonstrativo1' => $apiReponse->local_pagamento,
            'demonstrativo2' => '',
            'demonstrativo3' => '',
            'instrucoes1' => $apiReponse->lista_texto_informacao_cliente_beneficiario[0]->texto_informacao_cliente_beneficiario,
            'instrucoes2' => $apiReponse->lista_texto_informacao_cliente_beneficiario[1]->texto_informacao_cliente_beneficiario,
            'instrucoes3' => $apiReponse->lista_texto_informacao_cliente_beneficiario[2]->texto_informacao_cliente_beneficiario,
            'instrucoes4' => $apiReponse->lista_texto_informacao_cliente_beneficiario[3]->texto_informacao_cliente_beneficiario,
            'quantidade' => '',
            'valor_unitario' => '',
            'aceite' => '',
            'especie' => $apiReponse->moeda->sigla_moeda,
            'especie_doc' => $apiReponse->especie_documento,
            'agencia' => $apiReponse->beneficiario->agencia_beneficiario,
            'conta' => $apiReponse->beneficiario->conta_beneficiario,
            'conta_dv' => $apiReponse->beneficiario->digito_verificador_conta_beneficiario,
            'carteira' => $apiReponse->tipo_carteira_titulo,
            'identificacao' => $apiReponse->beneficiario->nome_razao_social_beneficiario,
            'cpf_cnpj' => $apiReponse->beneficiario->cpf_cnpj_beneficiario,
            'endereco' => $apiReponse->beneficiario->logradouro_beneficiario,
            'cidade_uf' => $apiReponse->beneficiario->cidade_beneficiario.
                            '/'. $apiReponse->beneficiario->uf_beneficiario,
            'cedente' => $apiReponse->beneficiario->nome_razao_social_beneficiario,
        );
        $boletoDataJson = json_encode($boletoData);
        $boletoDataJsonb64 = base64_encode($boletoDataJson);
        $url = get_option('siteurl') . '/wp-content/plugins/ssw-itau-integration/api/boleto/boleto_itau.php?boleto=' . $boletoDataJsonb64;
        return $url;
    }
}