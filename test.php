<?php

// documentation of this process and these example values are taken from
// https://inkdit.desk.com/customer/portal/articles/685178

require 'inkdit.php';

// == URL generation
$offer_url    = "https://inkdit.com/ofr/x0123456789abcdef";
$private_key  = "cab005e";
$query_string = "redirect=http://example.net/contract-signed&inputs[contract-input-1]=St.%20Louis&email=earl@example.org";

$code = inkdit_validation_code($private_key, $query_string);

assert("'$code' == '68b56b6044f0ea95aff6b06112c32ae9caabed80'");

$url = _inkdit_offer_url($offer_url, $private_key, $query_string);

assert("'$url' == '$offer_url/$code?$query_string'");

// == Response verification
$query_string = 'contract_id=x1234&signed_at=2012-08-14T00:00:00Z&confirmation=a508066c2b02d95e9e67521c7baf6587a975d154';

$result = inkdit_verify_signing($query_string, $private_key);

assert($result);

assert($result['contract_id']  == 'x1234');
assert($result['contract_url'] == 'https://inkdit.com/c/x1234');
assert($result['signed_at']    == '2012-08-14T00:00:00Z');

print "If no warnings were printed then we succeeded :)\n";

?>
