<?php

/*
    inkdit_offer_url: constructs a URL that a user can visit to sign an offer
    with some prefilled information.

    $user_opts is an array that can contain the keys:
      redirect
      email
      first_name
      last_name

    $inputs is an array that can contains a key for each input field in the
    contract.

    See https://inkdit.desk.com/customer/portal/articles/685178 for a
    description of these options.

  inkdit_offer_url($offer_url,
    $private_key,
    array('email' => 'earl@example.org', 'redirect' => 'http://example.org/'),
    array('contract-input-1' => 'St. Louis'));
 */
function inkdit_offer_url($offer_url, $private_key, $user_opts, $inputs) {
  $query_string = inkdit_build_query($user_opts, $inputs);
  return _inkdit_offer_url($offer_url, $private_key, $query_string);
}

/*
    inkdit_verify_signing: verifies that the result parameters returned in the
    redirect are genuine.

  $query_string = $_SERVER['QUERY_STRING'];
  $result = inkdit_verify_signing($query_string, $private_key);

  // ensure that the signing was created recently (PHP 5.3+)
  $t = DateTime::createFromFormat(DateTime::ISO8601, $result['signed_at']);
  if((time() - $t->getTimeStamp()) > 300)
    throw new Exception('This signing was created more than 5 minutes ago!');

 */
function inkdit_verify_signing($query_string, $private_key) {
  $pieces = explode('&confirmation=', $query_string, 2);

  $data            = $pieces[0];
  $validation_code = $pieces[1];

  if(inkdit_validation_code($private_key, $data) != $validation_code)
    return;

  parse_str($query_string, $params);

  $contract_url = 'https://inkdit.com/c/' . $params['contract_id'];

  return array(
                'contract_id'  => $params['contract_id'],
                'contract_url' => $contract_url,
                'signed_at'    => $params['signed_at']
              );
}

function inkdit_validation_code($private_key, $query_string) {
  return hash_hmac('sha1', $query_string, $private_key);
}

function _inkdit_offer_url($offer_url, $private_key, $query_string) {
  $validation_code = inkdit_validation_code($private_key, $query_string);
  return $offer_url . "/" . $validation_code . "?" . $query_string;
}

function inkdit_build_query($user_opts, $inputs) {
  $opts = $user_opts;
  $opts['inputs'] = $inputs;
  return http_build_query($opts, '', '&');
}

?>
