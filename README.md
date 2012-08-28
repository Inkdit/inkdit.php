# inkdit.php #

This is a PHP library for interacting with [Inkdit][].
Right now it focusses on using [hosted signing pages][hosted-signing-page].

## Hosted Signing Page ##

When the user does something that requires a contract to be signed, you
construct a URL that contains the information you want to pass through to the
contract.

    $offer_url   = '...'; // obtained from your contract page
    $private_key = '...'; // obtained from your contract page

    $url = inkdit_offer_url($offer_url,
      $private_key,
      array('email' => 'earl@example.org', 'redirect' => 'http://example.org/'),
      array('contract-input-1' => 'St. Louis'));

The user gets sent to `$url`. They sign the contract. Inkdit redirects
them to the URL given as the 'redirect' parameter, and appends a query
string.

    $query_string = $_SERVER['QUERY_STRING'];
    $result = inkdit_verify_signing($query_string, $private_key);

    // if the signing could not be validated, $result will be false.
    //
    // if the signing was validated, $result will be an array containing:
    //   contract_id:  the id of the newly created contract
    //   contract_url: the URL of the newly created contract
    //   signed_at:    the date and time that the contract was signed.

    // ensure that the signing was created recently (PHP 5.3+)
    $t = DateTime::createFromFormat(DateTime::ISO8601, $result['signed_at']);
    if((time() - $t) > 300)
      throw new Exception('This signing was created more than 5 minutes ago!');

At this point, the signing has been validated, you know that the contract has
been signed, and the user can continue with your workflow.

[Inkdit]: https://inkdit.com/
[hosted-signing-page]: https://inkdit.desk.com/customer/portal/articles/685178
