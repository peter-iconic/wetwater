<?php
$config = [
    "digest_alg" => "sha256",
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
];

$keyPair = openssl_pkey_new($config);

if ($keyPair === false) {
    echo "OpenSSL Error: " . openssl_error_string();
} else {
    echo "Key pair generated successfully!";
}