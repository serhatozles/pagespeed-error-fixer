# Server Configuration

## Apache
- Add to your .htaccess file for **Lavarage Browser Cache** like following code;
```
<IfModule mod_expires.c>
ExpiresActive On
ExpiresByType image/jpg "access plus 1 year"
ExpiresByType image/jpeg "access plus 1 year"
ExpiresByType image/gif "access plus 1 year"
ExpiresByType image/png "access plus 1 year"
ExpiresByType text/css "access plus 1 month"
ExpiresByType application/pdf "access plus 1 month"
ExpiresByType text/x-javascript "access plus 1 month"
ExpiresByType application/x-shockwave-flash "access plus 1 month"
ExpiresByType image/x-icon "access plus 1 year"
ExpiresDefault "access plus 2 days"
</IfModule>
```
- Add to your .htaccess file for **GZip and Deflate Compression** like following code;
```
<IfModule mod_deflate.c>
SetOutputFilter DEFLATE
AddDefaultCharset UTF-8
SetEnvIfNoCase Request_URI .(?:gif|jpe?g|png)$ no-gzip dont-vary
SetEnvIfNoCase Request_URI .(?:exe|t?gz|zip|bz2|sit|rar)$ no-gzip dont-vary
<IfModule mod_headers.c>
Header append Vary User-Agent
</IfModule>
</IfModule>
```