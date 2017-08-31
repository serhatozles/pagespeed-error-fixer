# pagespeed-error-fixer
it fix errors for now just css,js and images.


## Usage
### -Fix With Url
#### Parameters:
**URL** (string)**:** Website URL. Example: http://www.example.com/

**baseUrl** (boolean|string,default: false)**:** if wanna check sub page, you have write here something.

**mobile** (boolean,default: false)**:** PageSpeed check type. if false, pagespeed check on the desktop.

**Discard Different Size Images** (boolean,default: true)**:** PageSpeed resized some images sometimes. if you wanna discard them, use true.

**Backup** (boolean,default: true)**:** it will create a backup folder, than copy to there changed files.

C:\WTServer\WWW\test.proje is public folder of http://test.proje/
```
C:\WTServer\WWW\test.proje>php C:\WTServer\WWW\test.proje\pagespeed-error-fixer\bin\fixUrl
URL(required): http://test.proje/
Backup(default:true):
```
then it will request Google PageSpeed and Download optimized files, than will replace them.