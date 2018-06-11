<?php

$chars = array(".","/","-", "*"); /* aqui indico os caracteres que desejo remover */
$cpfnovo = str_replace($chars, "", $_POST['cpf']); /* através de str_replace insiro somente os números invés de caracteres */
$documentocomprador = str_replace($chars, "", $_POST['cpf']); /* através de str_replace insiro somente os números invés de caracteres */
$pedidonumero = $usuario."_".$cpfnovo;

date_default_timezone_set('America/Sao_Paulo');
$dataatual = date('Y-m-d'); 

$datanovo = date('Y-m-d', strtotime($dataatual. ' + 2 days'));
date_default_timezone_set('America/Sao_Paulo');
$mes = date('m'); 
$nossonumero = $cpfnovo.$mes;

$documento = $cpfnovo;

	$arquivo = file_get_contents('json65.json');
	$arquivo = json_decode($arquivo);

	$merchantId = ""; //DIGITE SEU MERCHANTID
	$chaveSeguranca = ""; //CHAVE SEGURANCA
	


	$data_service_pedido = array(
		"numero" => "$pedidonumero",
		"valor" => $arquivo->pedido->valor,
		"descricao" => $arquivo->pedido->descricao);

	$data_service_comprador_endereco = array(
		"cep" => "$cep",
		"logradouro" => "$rua",
		"numero" => "$numero",
		"complemento" => "$complemento",
		"bairro" => "$bairro",
		"cidade" => "$cidade",
		"uf" => "$uf"
	);

	$data_service_comprador = array(
		"nome" => "$nome",
		"documento" => "$documentocomprador",
		"endereco" => $data_service_comprador_endereco,
		"ip" => $_SERVER["REMOTE_ADDR"],
		"user_agent" => $_SERVER["HTTP_USER_AGENT"]
	);

	$data_service_boleto_registro = null;

	$data_service_boleto_instrucoes = array(
		"instrucao_linha_1" => $arquivo->boleto->instrucoes->instrucao_linha_1,
		"instrucao_linha_2" => $arquivo->boleto->instrucoes->instrucao_linha_2,
		"instrucao_linha_3" => $arquivo->boleto->instrucoes->instrucao_linha_3,
		"instrucao_linha_4" => $arquivo->boleto->instrucoes->instrucao_linha_4,
		"instrucao_linha_5" => $arquivo->boleto->instrucoes->instrucao_linha_5,
		"instrucao_linha_6" => $arquivo->boleto->instrucoes->instrucao_linha_6,
		"instrucao_linha_7" => $arquivo->boleto->instrucoes->instrucao_linha_7,
		"instrucao_linha_8" => $arquivo->boleto->instrucoes->instrucao_linha_8,
		"instrucao_linha_9" => $arquivo->boleto->instrucoes->instrucao_linha_9,
		"instrucao_linha_10" => $arquivo->boleto->instrucoes->instrucao_linha_10,
		"instrucao_linha_11" => $arquivo->boleto->instrucoes->instrucao_linha_11,
		"instrucao_linha_12" => $arquivo->boleto->instrucoes->instrucao_linha_12
	);

	$data_service_boleto = array(
		"beneficiario" => "Sindecard O cartão benefício dos caminhoneiros",
		"carteira" => $arquivo->boleto->carteira,
		"nosso_numero" => "$cpfnovo",
		"data_emissao" => "$dataatual",
		"data_vencimento" => "$datanovo",
		"valor_titulo" => $arquivo->boleto->valor_titulo,
		//"http://www.sindecard.com.br/imagens/logo.png" => $arquivo->boleto->url_logotipo,
		"url_logotipo" => "http://sindecard.com.br/images/logo2.jpg",
		"mensagem_cabecalho" => $arquivo->boleto->mensagem_cabecalho,
		"tipo_renderizacao" => $arquivo->boleto->tipo_renderizacao,
		"instrucoes" => $data_service_boleto_instrucoes,
		"registro" => $data_service_boleto_registro);

	$data_service_request = array(
		"merchant_id" => $merchantId,
		"meio_pagamento" => "300",
		"pedido" => $data_service_pedido,
		"comprador" => $data_service_comprador,
		"boleto" => $data_service_boleto,
		"token_request_confirmacao_pagamento" => $arquivo->token_request_confirmacao_pagamento);

	$data_post = json_encode($data_service_request); 

	$url = "https://homolog.meiosdepagamentobradesco.com.br/apiboleto" . "/transacao"; //TESTES HOMOLOGACAO
	//$url = "https://meiosdepagamentobradesco.com.br/apiboleto/transacao"; //PRODUCAO
	//Configuracao do cabecalho da requisicao

	$mediaType = "application/json";
	$charSet = "UTF-8";

	$headers = array();
	$headers[] = "Accept: ".$mediaType;
	$headers[] = "Accept-Charset: ".$charSet;
	$headers[] = "Accept-Encoding: ".$mediaType;
	$headers[] = "Content-Type: ".$mediaType.";charset=".$charSet;

	$AuthorizationHeader = $merchantId.":".$chaveSeguranca;
	$AuthorizationHeaderBase64 = base64_encode($AuthorizationHeader);
	$headers[] = "Authorization: Basic ".$AuthorizationHeaderBase64; 
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_post);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$result = json_decode(curl_exec($ch));
	// print_r($result);
	$link = $result->boleto->url_acesso;
	//echo $link;
	header("Location: ".$link."");

mysql_close($conexao);


?>	
	