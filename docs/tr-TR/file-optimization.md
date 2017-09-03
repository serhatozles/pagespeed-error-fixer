# PageSpeed Hata Giderici
Şimdilik sadece css,js ve resim hatalarını giderebilir.
Şimdilik sadece konsol üzerinden çalışmaktadır.


## Kullanımı
-Url Kullanarak Optimize Et (şuralarda çalışabilir: Local Server ya da Web Server)
--------------------------------------
#### Parametreler:
- **URL** (string)**:** Website URL. Example: http://www.example.com/

- **Base Url** (boolean|string,default: false)**:** Eğer alt bir sayfadaki hataları gidermek isterseniz, buraya base url yi yazmanız gerekir.

- **Clean Url Queries**(boolean,default:true)**:** 'example.css?v=2' bunları siler: ?v=2

- **Mobile** (boolean,default: false)**:** PageSpeed kontrol türü. Eğer false ise, masaüstü modunda kontrol eder.

- **Discard Different Size Images** (boolean,default: true)**:** PageSpeed bazen sayfa içerisine göre resimleri yeniden boyutlandırabilir. Eğer bunların atlanmasını istiyorsanız true şeklinde bırakın.

- **Backup** (boolean,default: true)**:** Bir yedekleme klasörü oluşturarak optimize edilen dosyayı klasöre kopyalar.

/var/www/vhosts/test.proje/httpdocs/ , http://test.proje/ domainin ana klasörüdür.
```
[root@lnx httpdocs]# php pagespeed-error-fixer\bin\fixUrl
URL(required): http://test.proje/sub.html
Base Url(default:false, means same url parameter.): http://test.proje/
Clean Url Queries(default:true, example: 'example.css?v=2' it will remove ?v=2): 
Mobile(default:false): 
Discard Different Size Images(default:true): 
Backup(default:true): 
```
bundan sonra Google PageSpeed'i kullanır ve indirerek dosyaları optimize eder.

-Klasörü Optimize Et (şuralarda çalışabilir: Web Server)
--------------------------------------

#### Parametreler:
Klasördeki dosya listesini çekerek bir html dosyası oluşturur ve bunu pagespeed'e gönderir.

- **Folder URL** (string)**:** Website URL. Örnek: http://www.example.com/

- **Folders** (string)**:** Klasör isimleri. Örnek: images,assets/files

- **Backup** (boolean,default: true)**:** Bir yedekleme klasörü oluşturarak optimize edilen dosyayı klasöre kopyalar.

/var/www/vhosts/test.proje/httpdocs/ , http://test.proje/ domainin ana klasörüdür.
```
[root@lnx images]# php pagespeed-error-fixer\bin\fixFolder
Folder URL(required): http://test.proje/images/
Folders(required): airports,sliders 
Backup(default:true): 
```
bundan sonra Google PageSpeed'i kullanır ve indirerek dosyaları optimize eder.

-Dosyaları Optimize Et (şuralarda çalışabilir: Web Server)
--------------------------------------
#### Parametreler:
Dosyalar için bir html dosyası oluşturur ve bunu pagespeed'e gönderir.

- **Folder URL** (string)**:** Website URL. Örnek: http://www.example.com/

- **Folders** (string)**:** Folders name. Örnek: images,assets/files

- **Backup** (boolean,default: true)**:** Bir yedekleme klasörü oluşturarak optimize edilen dosyayı klasöre kopyalar.

/var/www/vhosts/test.proje/httpdocs/ , http://test.proje/ domainin ana klasörüdür.
```
[root@lnx assets]# php pagespeed-error-fixer\bin\fixFiles
Folder URL(required): http://test.proje/assets/
Files(required): img/loading.gif,js/jquery.js,...
Backup(default:true): 
```
bundan sonra Google PageSpeed'i kullanır ve indirerek dosyaları optimize eder.


## Sonuç
![Screenshot](https://i.hizliresim.com/1g8GRb.jpg)