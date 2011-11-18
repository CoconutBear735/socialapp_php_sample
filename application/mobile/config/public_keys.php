<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['publickeys'] = array(
	'mixi.jp' => array(
	'signature_method' => 'RSA_SHA1',
	'publickey' => <<<EOD
-----BEGIN CERTIFICATE-----
MIICdzCCAeCgAwIBAgIJANCWpLIspxwbMA0GCSqGSIb3DQEBBQUAMDIxCzAJBgNV
BAYTAkpQMREwDwYDVQQKEwhtaXhpIEluYzEQMA4GA1UEAxMHbWl4aS5qcDAeFw0x
MDAzMjMwODE1NTlaFw0xMjAzMjIwODE1NTlaMDIxCzAJBgNVBAYTAkpQMREwDwYD
VQQKEwhtaXhpIEluYzEQMA4GA1UEAxMHbWl4aS5qcDCBnzANBgkqhkiG9w0BAQEF
AAOBjQAwgYkCgYEAtbq5Rns5IEktXldZ+37Fjlavnuc4JuwrD4F+4NQwVwVtR5yw
Vg10EanXWSGIAbUFx6hlppYOb0x/3PBMG80643LmXSJmvv4ViRUBl2Ys9Ie2L/D9
KVQXDWgJjxBGqo5MO6rA/Ip78kbiNbIQJUIJtbuJZWL3LMVe6mpIO2SUi1UCAwEA
AaOBlDCBkTAdBgNVHQ4EFgQU8bp8/6lmt5L8em6dZyoGciUUmuUwYgYDVR0jBFsw
WYAU8bp8/6lmt5L8em6dZyoGciUUmuWhNqQ0MDIxCzAJBgNVBAYTAkpQMREwDwYD
VQQKEwhtaXhpIEluYzEQMA4GA1UEAxMHbWl4aS5qcIIJANCWpLIspxwbMAwGA1Ud
EwQFMAMBAf8wDQYJKoZIhvcNAQEFBQADgYEApalbBgXxnLJW8fM6W7E7GAE4QZbE
lvYqvtQSxwacGYoqF2FW1zrBkmTB12LTddFU01pqDaeels3Ru5TNOnTIJemFWW0D
viKtu9GqsrOye6AZR+XA5Iy/vq3EV1TCGuDNmhJaHAiLeYuLbEqmvH7/l9xNsafH
IpqnsHwF1pm0bTY=
-----END CERTIFICATE-----
EOD
  )
);

/* End of file public_keys.php */
/* Location: ./application/config/public_keys.php */