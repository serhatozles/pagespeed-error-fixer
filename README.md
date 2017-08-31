# PageSpeed Error Fixer
it fix errors for now just css,js and images.
it works on console.


## Usage
-Fix With Url
--------------------------------------
#### Parameters:
- **URL** (string)**:** Website URL. Example: http://www.example.com/

- **Base Url** (boolean|string,default: false)**:** if wanna check sub page, you have write here something.

- **Clean Url Queries**(boolean,default:true)**:** 'example.css?v=2' it will remove ?v=2

- **Mobile** (boolean,default: false)**:** PageSpeed check type. if false, pagespeed check on the desktop.

- **Discard Different Size Images** (boolean,default: true)**:** PageSpeed resized some images sometimes. if you wanna discard them, use true.

- **Backup** (boolean,default: true)**:** it will create a backup folder, than copy to there changed files.

/var/www/vhosts/test.proje/httpdocs/ is public folder of http://test.proje/
```
[root@lnx httpdocs]# php pagespeed-error-fixer\bin\fixUrl
URL(required): http://test.proje/sub.html
Base Url(default:false, means same url parameter.): http://test.proje/
Clean Url Queries(default:true, example: 'example.css?v=2' it will remove ?v=2): 
Mobile(default:false): 
Discard Different Size Images(default:true): 
Backup(default:true): 
```
then it will request Google PageSpeed and Download optimized files, than will replace them.

-Fix Folder
--------------------------------------
#### Parameters:
- **Folder URL** (string)**:** Website URL. Example: http://www.example.com/

- **Folders** (string)**:** Folders name. Example: images,assets/files

- **Backup** (boolean,default: true)**:** it will create a backup folder, than copy to there changed files.

/var/www/vhosts/test.proje/httpdocs/ is public folder of http://test.proje/
```
[root@lnx images]# php pagespeed-error-fixer\bin\fixFolder
Folder URL(required): http://test.proje/images/
Folders(required): airports,sliders 
Backup(default:true): 
```
then it will request Google PageSpeed and Download optimized files, than will replace them.

-Fix Files
--------------------------------------
#### Parameters:
- **Folder URL** (string)**:** Website URL. Example: http://www.example.com/

- **Folders** (string)**:** Folders name. Example: images,assets/files

- **Backup** (boolean,default: true)**:** it will create a backup folder, than copy to there changed files.

/var/www/vhosts/test.proje/httpdocs/ is public folder of http://test.proje/
```
[root@lnx assets]# php pagespeed-error-fixer\bin\fixFiles
Folder URL(required): http://test.proje/assets/
Files(required): img/loading.gif,js/jquery.js,...
Backup(default:true): 
```
then it will request Google PageSpeed and Download optimized files, than will replace them.


## Result
![Screenshot](https://i.hizliresim.com/1g8GRb.jpg)