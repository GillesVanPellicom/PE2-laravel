# Mailserver key cmd
```
openssl req -x509 -newkey rsa:2048 -keyout certificates/mailserver.key -out certificates/mailserver.crt -days 3650 -nodes -subj "/CN=mailserver"
```
- req -x509: Creates a self-signed certificate.
- newkey rsa:2048: Generates a 2048-bit RSA key.
- keyout and -out: Saves the private key and certificate.
- days 3650: Valid for 10 year.
- nodes: No passphrase.
- subj "/CN=mailserver": Common Name (CN) matches the mail server’s hostname.