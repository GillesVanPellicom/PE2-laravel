#!/bin/bash

# Run certificate update as root
if [ "$(id -u)" -eq 0 ]; then
    # Copy the certificate to the CA directory
    cp -f /tmp/mailserver.crt /usr/local/share/ca-certificates/mailserver.crt
    # Update the CA certificates
    update-ca-certificates
    # Set proper permissions
    chmod 644 /usr/local/share/ca-certificates/mailserver.crt
    chmod 644 /etc/ssl/certs/ca-certificates.crt
    # Try to remove the temp file, but ignore errors
    rm -f /tmp/mailserver.crt || true
fi

# Pass control to the original Sail entrypoint
exec /entrypoint.sh "$@"