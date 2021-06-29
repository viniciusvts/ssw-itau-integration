<?php

/**
 * Endpoint para o envio do formulário
 * @author Vinicius de Santana
 */
function ssw_itaui_form (WP_REST_Request $request){
  $SSW_ITAUI = new ssw_itaui_wp();
  $leads = $request->get_params();

  $resp = new WP_REST_Response( array('nada ainda') );
  
  return $resp;
}

function ssw_itaui_genPdfBoleto(WP_REST_Request $request){
  
  $params = $request->get_params();
  // coisas que não envio para o RD:
  // A url para redirecionar vem do endpoint
  $urlToRedirect = $params['urlredirect'];
  unset($params['urlredirect']);
  // Vou enviar email para:
  if(isset($params['sendtoemail'])) {
    $sendtoemail = $params['sendtoemail'];
    unset($params['sendtoemail']);
  } else {
    $sendtoemail = get_bloginfo('admin_email');
  }
  // Título do email:
  if(isset($params['emailsubject'])) {
    $emailsubject = $params['emailsubject'];
    unset($params['emailsubject']);
  } else {
    $emailsubject = 'Geração de boleto, dados do usuário';
  }
  
  // envia email depois de excluir url e identificador
  $to = $sendtoemail;
  $subject = $emailsubject;
  $message = '';
  foreach ($params as $key => $value) {
    $message .= $key . ': ' . $value . '<br/>';
  }
  $headers = array('Content-Type: text/html; charset=UTF-8');
  $wpmail = wp_mail( $to, $subject, $message, $headers );
  // end send email
  // sanitize inputs
  $pattern = '/(\.|-|,|\/)/';
  $sanitized_cpf_cnpj_pagador = preg_replace($pattern, '', $params['cpf_cnpj_pagador']);
  $sanitized_cep_pagador = preg_replace($pattern, '', $params['cep_pagador']);
  $pagador = array(
    "cpf_cnpj_pagador" => $sanitized_cpf_cnpj_pagador,
    "nome_pagador" => $params['nome_pagador'],
    "logradouro_pagador" => $params['logradouro_pagador'],
    "bairro_pagador" => $params['bairro_pagador'],
    "cidade_pagador" => $params['cidade_pagador'],
    "uf_pagador" => $params['uf_pagador'],
    "cep_pagador" => $sanitized_cep_pagador,
    "grupo_email_pagador" => array(
        "email_pagador" => $params['email'],
    ),
  );
  // valor do boleto, caso venha virgula substitui por ponto
  $vlrb = str_replace(',', '.', $params['valor_boleto']);
  $valorDoBoleto = floatval($vlrb);
  $vencimento = new DateTime();
  $vencimento->modify('+5 days'); // configura o vencimento para mais 5 dias

  $SSW_ITAUI = new ssw_itaui_wp();
  $boleto = $SSW_ITAUI->registerBoleto($valorDoBoleto, $vencimento, $pagador, true);

  if(!$boleto->pagador) return "Houve um erro ao emitir o boleto, tente novamente.";

  $boletoLink = getBoletoLink($boleto);
  wp_redirect($boletoLink);
  exit;
}
/**
 * Função registra os endpoints
 * @author Vinicius de Santana
 */
function SSW_ITAUI_registerapi(){
    $sswuriapi = 'ssw-itaui-integration/v1';
    // endpoint para envio dos dados do form para gerar boleto
    register_rest_route($sswuriapi,
      '/form',
      array(
        'methods' => 'POST',
        'callback' => 'ssw_itaui_form',
      )
    );
    // endpoint para envio dos dados do form para gerar boleto
    register_rest_route($sswuriapi,
      '/genPdfBoleto',
      array(
        'methods' => 'POST',
        'callback' => 'ssw_itaui_genPdfBoleto',
        'args' => array(
          'email' => array(
            'required' => true,
            'description' => 'Email do lead',
          ),
          'urlredirect' => array(
            'required' => true,
            'description' => 'Depois da conversão, redirecionar para esse endereço',
          ),
          'emailsubject' => array(
            'required' => false,
            'description' => 'Assunto do email que será enviado',
          ),
          'sendtoemail' => array(
            'required' => false,
            'description' => 'Um email ou emails separados por virgula para o qual os dados serão enviados',
          ),
          'cpf_cnpj_pagador' => array(
            'required' => true,
            'description' => 'cpf ou cnpj do pagador',
          ),
          'nome_pagador' => array(
            'required' => true,
            'description' => 'nome do pagador',
          ),
          'logradouro_pagador' => array(
            'required' => true,
            'description' => 'endereço do pagador',
          ),
          'bairro_pagador' => array(
            'required' => true,
            'description' => 'bairro do pagador',
          ),
          'cidade_pagador' => array(
            'required' => true,
            'description' => 'cidade pagador',
          ),
          'uf_pagador' => array(
            'required' => true,
            'description' => 'Estado do pagador',
          ),
          'cep_pagador' => array(
            'required' => true,
            'description' => 'cep do pagador',
          ),
          'valor_boleto' => array(
            'required' => true,
            'description' => 'valor do boleto',
          ),
        )
      )
    );
}
  
add_action('rest_api_init', 'SSW_ITAUI_registerapi');
  