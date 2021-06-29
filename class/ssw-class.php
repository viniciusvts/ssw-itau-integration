<?php
if( !class_exists('ssw_itaui_wp') ){
    class ssw_itaui_wp {
        // propriedades
        private $client_id = '';
        private $client_secret = '';
        private $code = '';
        private $access_token = '';
        
        public function __construct(){
            $this->client_id = get_option(SSW_ITAUI_CLIENT_ID);
	        $this->client_secret = get_option(SSW_ITAUI_CLIENTE_SECRET);
	        $this->code = get_option(SSW_ITAUI_CODE);
	        $this->access_token = get_option(SSW_ITAUI_ACCESS_TOKEN);
        }
        
        // set properties
        public function setClientId($value){
            if (update_option(SSW_ITAUI_CLIENT_ID, $value)){
                $this->client_id = $value;
                return true;
            }
            return false;
        }
        public function setClientSecret($value){
            if (update_option(SSW_ITAUI_CLIENTE_SECRET, $value)){
                $this->client_secret = $value;
                return true;
            }
            return false;
        }
        public function setCode($value){
            if (update_option(SSW_ITAUI_CODE, $value)){
                $this->code = $value;
                return true;
            }
            return false;
        }
        private function setAccessToken($value){
            if (update_option(SSW_ITAUI_ACCESS_TOKEN, $value)){
                $this->access_token = $value;
                return true;
            }
            return false;
        }
        public function clearAll(){
            $this->setClientId('');
            $this->setClientSecret('');
            $this->setCode('');
            $this->setAccessToken('');
            $this->setRefreshToken('');
        }

        // get properties
        public function getClientId(){
            return $this->client_id;
        }
        public function getClientSecret(){
            return $this->client_secret;
        }
        public function getCode(){
            return $this->code;
        }

        // has properties
        public function hasClientId(){
            if($this->client_id) return true;
            return false;
        }
        public function hasClientSecret(){
            if($this->client_secret) return true;
            return false;
        }
        public function hasCode(){
            if($this->code) return true;
            return false;
        }
        public function hasAcessToken(){
            if($this->access_token) return true;
            return false;
        }

        public function refreshToken(){
            // se tem refresh token, atualiza o token
            $url = 'https://oauth.itau.com.br/identity/connect/token';
            $payload = 'grant_type=client_credentials'.
            '&scope='. 'readonly';
            $basicAuth = $this->getClientId() . ':' . $this->getClientSecret();
            $basicAuth_b64 = base64_encode($basicAuth);
            $headers = array('Authorization' => 'Basic ' . $basicAuth_b64);
            $resp = $this->post($url, $payload, $headers);
            if(!$resp->access_token){ return false; }
            return $this->setAccessToken($resp->access_token);
        }
        /**
         * registra o boleto
         *  $pagador = array(
         *      "cpf_cnpj_pagador" => "27119334000135",
         *      "nome_pagador" => "EMPREENDEDOR SIMPLES E LEGAL C",
         *      "logradouro_pagador" => "Av. Pedroso de Morais",
         *      "bairro_pagador" => "Pinheiros",
         *      "cidade_pagador" => "São Paulo",
         *      "uf_pagador" => "SP",
         *      "cep_pagador" => "05419000",
         *      "grupo_email_pagador" => array(
         *        "email_pagador" => "testebimworks@outlook.com",
         *      ),
         *  );
         * @param string $valor - valor do boleto in float ex 99.33
         * @param DateTime $vencimento - data do vencimento vindo de um input
         * @param array $pagador - dados do pagador
         * @param bool $test - true se for um teste
         */
        public function registerBoleto($valor, $vencimento, $pagador, $test = false){
            // valida pagador
            // nome_pagador são 30 caracteres
            $pagador['nome_pagador'] = substr($pagador['nome_pagador'], 0, 30);
            // logradouro_pagador são 40 caracteres
            $pagador['logradouro_pagador'] = substr($pagador['logradouro_pagador'], 0, 40);
            // bairro_pagador são 15 caracteres
            $pagador['bairro_pagador'] = substr($pagador['bairro_pagador'], 0, 15);
            // cidade_pagador são 20 caracteres
            $pagador['cidade_pagador'] = substr($pagador['cidade_pagador'], 0, 20);
            // uf_pagador são 2 caracteres
            $pagador['uf_pagador'] = substr($pagador['uf_pagador'], 0, 2);

            // valor de float para string sem pontos ou virgulas
            $valorString = strval($valor);
            $valorString = str_replace('.', '', $valorString);
            $valorString = str_replace(',', '', $valorString);
            // converter dateformat to string format
            $vencString = date('Y-m-d', $vencimento->getTimestamp());
            $payload = $this->generateDataToSendToApi($valorString, $vencString, $pagador, $test);
            
            // envia para a api
            $url = 'https://gerador-boletos.itau.com.br/router-gateway-app/public/codigo_barras/registro';
            //payload
            $pload = json_encode($payload);
            //headers
            $headers = array(
                'Accept' => 'application/vnd.itau',
                'access_token' => $this->access_token,
                'itau-chave' => $this->code,
                'identificador' => '04638015000130',
            );
            $resp = $this->post($url, $pload, $headers);
            // se tiver código e não for erro de validação continua
            // se for erro de validação não adianta solicitar outro token
            if(isset($resp->codigo) && $resp->codigo != 'validation_error'){ 
                // se erro de autenticação
                // atualizo o header e tento novamente
                if($this->refreshToken()){
                    //headers
                    $headers = array(
                        'Accept' => 'application/vnd.itau',
                        'access_token' => $this->access_token,
                        'itau-chave' => $this->code,
                        'identificador' => '04638015000130',
                    );
                    //envia
                    $resp = $this->post($url, $pload, $headers);
                }
            }
            return $resp;
        }
        /**
         * Gera os dados necessários para enviar para a api de boletos do itaú
         * @param string $valor - valor do boleto ex 999900, onde 00 == centavos
         * @param string $vencimento - vencimento do boleto ex 2021-07-20
         * @param array $pagador - dados do pagador
         * @param string $pagador[cpf_cnpj_pagador] - cpf ou cnpj do pagador
         * @param string $pagador[nome_pagador] - nome do pagador com no máximo 30 caracteres
         * @param string $pagador[logradouro_pagador] - logradouro do pagador
         * @param string $pagador[bairro_pagador] - bairro do pagador
         * @param string $pagador[cidade_pagador] - cidade do pagador
         * @param string $pagador[uf_pagador] - uf do pagador
         * @param string $pagador[cep_pagador] - cep do pagador só numeros
         * @param array $pagador[grupo_email_pagador] - grupo_email do pagador
         * @param string $pagador[grupo_email_pagador][][email_pagador] - email do pagador
         * 
         * @return array
         */
        private function generateDataToSendToApi($valor, $vencimento, $pagador, $test = false){
            /** dados do beneficiario */
            $beneficiario = array(
                "cpf_cnpj_beneficiario" => "04638015000130",
                "agencia_beneficiario" => "5190",
                "conta_beneficiario" => "0000036",
                "digito_verificador_conta_beneficiario" => "3"
            );
            $carteira = 109;
            $nosso_numero = $this->generateNossoNumeroRandom();
            $digito_verificador_nosso_numero =
                $this->generateOurNumberVerificateDigit($beneficiario['agencia_beneficiario'],
                    $beneficiario['conta_beneficiario'],
                    $carteira,
                    $nosso_numero);
            $dateTime = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
            $data_emissao = $dateTime->format('Y-m-d');
            $args = array(
                "tipo_ambiente"  => $test ? 1 : 2,
                "tipo_registro"  => 1,
                "tipo_cobranca"  => 1,
                "tipo_produto"  => "00006",
                "subproduto"  => "00008",
                "titulo_aceite"  => "N",
                "tipo_carteira_titulo"  => $carteira,
                "nosso_numero"  => $nosso_numero,
                "digito_verificador_nosso_numero"  => $digito_verificador_nosso_numero,
                "data_vencimento"  => $vencimento,
                "valor_cobrado"  => $valor,
                "especie"  => "17",
                "data_emissao"  => $data_emissao,
                "tipo_pagamento"  => 1,
                "indicador_pagamento_parcial"  => false,
                "beneficiario" => $beneficiario,
                "pagador" => $pagador,
                "moeda" => array(
                    "codigo_moeda_cnab" => "09",
                    "quantidade_moeda" => $valor,
                ),
                "juros" => array(
                    "tipo_juros" => 5,
                ),
                "multa" => array(
                    "tipo_multa" => 3
                ),
                "grupo_desconto" => array(
                    array(
                        "tipo_desconto" => 0
                    ),
                ),
                "recebimento_divergente" => array(
                    "tipo_autorizacao_recebimento" => "1"
                ),
            );
            return $args;
        }
        private function generateNossoNumeroRandom(){
            $characters = '0123456789';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < 8; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
        /**
         * Exemplo: AG/CONTA = 057/12345-7 CART/Nosso Número = 110/12345678-?
         * 0 0 5 7 1 2 3 4 5 1 1 0 1 2 3 4 5 6 7 8
         * 1 2 1 2 1 2 1 2 1 2 1 2 1 2 1 2 1 2 1 2
         * dac 8
         */
        private function generateOurNumberVerificateDigit($agencia_beneficiario,
        $conta_beneficiario,
        $carteira,
        $nosso_numero){
            // organiza os dados para o array de calculo
            // primeiro tranforma todos em array de char
            $agenciaCharArr = str_split($agencia_beneficiario);
            $contaCharArr = str_split($conta_beneficiario);
            $carteiraStr = strval($carteira);
            $carteiraCharArr = str_split($carteiraStr);
            $nossoNumCharArr = str_split($nosso_numero);
            $arrMerge = array_merge($agenciaCharArr,
                    $contaCharArr,
                    $carteiraCharArr,
                    $nossoNumCharArr);
            // inverte o array, o calculo é da esqueda para a direita
            $calc = array_reverse($arrMerge);
            // faz a soma seguinda a doc
            $total = 0;
            foreach ($calc as $key => $value) {
                // a base é 2 para par ou 0, e 1 para impar
                $base = ($key % 2) == 0 ? 2 : 1;
                $soma = intval($value) * $base;
                // se soma menor que 10 soma ao total
                if ($soma < 10) $total += $soma;
                else{
                    // se não soma os dois digitos e o valor resultante se soma ao total
                    $somaStr = strval($soma);
                    $somaArr = str_split($somaStr);
                    $soma = intval($somaArr[0]) + intval($somaArr[1]);
                    $total += $soma;
                }
            }
            // depois dessse rolê pega o resto da divisão por 10 do total
            $resto = $total % 10;
            // caso resto 0, retorna 0
            if($resto == 0) return 0;
            // DAC igual a 10 - $resto, e retorna
            $dac = 10 - $resto;
            return strval($dac);
        }

        //funções auxiliares
        private function post($url, $payload, $headers = []){
            $ch = curl_init($url);
            // Set the content type 
            $headersArray = array();
            $isPayloadJson = $this->isJson($payload);
            if ($isPayloadJson) $headersArray[] ='Content-Type:application/json';
            else $headersArray[] ='Content-Type:application/x-www-form-urlencoded';

            foreach ($headers as $key => $value) {
                $headersArray[] = $key.':'.$value;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headersArray);
            // payload
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            
            // Return response instead of outputting
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Execute the POST request
            $result = curl_exec($ch);
            // Close cURL resource
            curl_close($ch);
            //return
            return json_decode($result);
        }
        /**
         * get
         */
        // private function get($url, $headers = []){
        //     $ch = curl_init($url);
        //     // Set the content type to application/json
        //     $headersArray = array('Content-Type:application/json');
        //     foreach ($headers as $key => $value) {
        //         $headersArray[] = $key.':'.$value;
        //     }
        //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headersArray);
            
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //     $result = curl_exec($ch);
        //     // Close cURL resource
        //     curl_close($ch);
        //     //return
        //     return json_decode($result);
        // }
        /**
         * patch
         */
        // private function patch($url, $payload, $headers = []){
        //     $ch = curl_init($url);
        //     // Set the content type 
        //     $headersArray = array();
        //     $isPayloadJson = $this->isJson($payload);
        //     if ($isPayloadJson) $headersArray[] ='Content-Type:application/json';
        //     else $headersArray[] ='Content-Type:application/x-www-form-urlencoded';

        //     foreach ($headers as $key => $value) {
        //         $headersArray[] = $key.':'.$value;
        //     }
        //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headersArray);
        //     // payload
        //     curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            
        //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        //     // Return response instead of outputting
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //     $result = curl_exec($ch);
        //     // Close cURL resource
        //     curl_close($ch);
        //     //return
        //     return json_decode($result);
        // }

        function isJson($string) {
            return ((is_string($string) &&
                    (is_object(json_decode($string)) ||
                    is_array(json_decode($string))))) ? true : false;
        }
    }
}