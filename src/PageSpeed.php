<?php
namespace serhatozles\pagespeed;

/*
 * @author: Serhat ÖZLEŞ
 * @email: serhatozles@gmail.com
 */

// GLOBAL VARIABLES
define("BACKUP_FOLDER", getcwd() . DIRECTORY_SEPARATOR . 'backup');
define("COOKIE_FILE", __DIR__ . DIRECTORY_SEPARATOR . 'cookie.txt');

class PageSpeed
{
	public $console = false;
	public $backup = true;
	public $mobile = false;
	public $googleUrl = 'https://developers.google.com/speed/pagespeed/insights/optimizeContents?url={url}&strategy={mobile}';
	public $googleRefererUrl = 'https://developers.google.com/speed/pagespeed/insights/?hl=tr&url={url}&tab={mobile}';

	public $discardImageDiffSize = true;
	public $url = null;
	public $baseUrl = false;
	public $cleanQueries = true;

	public $maxFileSize = 10485760;

	public $folders = [];
	public $extensions = ['js', 'css', 'png', 'jpg', 'jpeg', 'gif'];

	public function fixUrl()
	{

		if ($this->console === false) {
			echo 'it doesn\'t work without console.';
			exit;
		}

		$randName = uniqid();

		$saveFile = getcwd() . DIRECTORY_SEPARATOR . $randName . '.zip';

		$this->fileSave($this->getUrl(), $saveFile);

		$this->baseUrl = $this->baseUrl === false ? $this->url : $this->baseUrl;

		$this->doIt($saveFile);
	}

	public function doIt($saveFile)
	{

		$randName = explode('.', basename($saveFile))[0];

		$extractPath = getcwd() . DIRECTORY_SEPARATOR . $randName;

		if (is_file($saveFile)) {

			$zip = new \ZipArchive();
			$res = $zip->open($saveFile);
			if ($res === true) {
				$zip->extractTo($extractPath);
				$zip->close();

				// deleting zip file.
				unlink($saveFile);

				// get MANIFEST
				$manifest = file_get_contents($extractPath . DIRECTORY_SEPARATOR . 'MANIFEST');

				preg_match_all('/((?:css|js|image)[a-zA-Z0-9\/._\-\+]+)\: ((?:' . preg_quote($this->baseUrl, '/') . ').*)/mi', $manifest, $manifestList);

				if (count($manifestList[1]) > 0) {

					foreach ($manifestList[1] as $index => $path) {

						$oldPathWay = $manifestList[2][$index];

						if ($this->cleanQueries !== false) {
							$oldPathWay = strtok($oldPathWay, '?');
						}

						$oldPathWay = urldecode($oldPathWay);

						$cleanNewPath = $randName . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
						$cleanOldPath = str_replace(
							[
								$this->baseUrl,
								'/',
							],
							[
								'',
								DIRECTORY_SEPARATOR,
							],
							$oldPathWay);
						$newPath = getcwd() . DIRECTORY_SEPARATOR . $cleanNewPath;
						$oldPath = getcwd() . DIRECTORY_SEPARATOR . $cleanOldPath;

						if (!file_exists(dirname($oldPath)))
							mkdir(dirname($oldPath), 0777, true);

						$discard = false;

						list($newW, $newH) = getimagesize($newPath);
						if (is_file($oldPath) && $this->discardImageDiffSize !== false) {
							list($oldW, $oldH) = getimagesize($oldPath);
							if ($newW !== $oldW || $newH !== $oldH) $discard = true;
						}

						// backup.
						if ($this->backup && is_file($oldPath) && $discard === false) {

							$backupFolder = str_replace(getcwd(), \BACKUP_FOLDER, $oldPath);

							if (!file_exists(dirname($backupFolder)))
								mkdir(dirname($backupFolder), 0777, true);

							if (!copy($oldPath, $backupFolder)) {
								echo "$oldPath can not copied...\n";
							}
						}

						if ($discard === false) {
							if (!copy($newPath, $oldPath)) {
								echo "$cleanNewPath can not copied...\n";
							} else {
								echo $cleanNewPath . ": $cleanOldPath\r\n";
							}
						} else {
							echo "discard: before: {$oldW}x{$oldH} - after: {$newW}x{$newH} - file: $cleanOldPath\r\n";
						}

					}

					// remove extracted folder.
					$this->rrmdir($extractPath);

				} else {
					echo "$randName: already optimized\r\n";
				}

			} else {
				echo "couldn't open $saveFile";
			}

		} else {
			echo 'file is not exist';
		}
	}

	public function fixFolder()
	{
		if ($this->console === false) {
			echo 'it doesn\'t work without console.';
			exit;
		}

		$files = $this->findFolder($this->extensions);

		return $this->fixFiles($files);
	}

	public function fixFiles($files)
	{
		if (empty($this->url)) {
			echo 'we need folder location url.';
			exit;
		}

		$this->baseUrl = $this->url;

		$headHTML = [];
		$bodyHTML = [];

		$result = [];

		$headHTMLInner = '';
		$bodyHTMLInner = '';
		$fileSizeCounter = 0;
		$fileCounter = 0;

		foreach ($files as $fileDir) {

			$fileCounter++;

			$filename = basename($fileDir);
			$filesize = stat(iconv('UTF-8', 'ISO-8859-1', $fileDir))['size'];
			$pathInfo = pathinfo($filename);
			$webUrl = str_replace([getcwd() . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], [$this->url, '/'], $fileDir);

			if (($fileSizeCounter + $filesize) >= $this->maxFileSize || $fileCounter >= 10) {
				$headHTML[] = $headHTMLInner;
				$bodyHTML[] = $bodyHTMLInner;
				$headHTMLInner = '';
				$bodyHTMLInner = '';
				$fileSizeCounter = 0;
				$fileCounter = 1;
			}

			if ($filesize < $this->maxFileSize) {
				$fileSizeCounter += $filesize;

				if ($pathInfo['extension'] === 'js') {
					$headHTMLInner .= '<script src="' . $webUrl . '"></script>' . "\r\n";
				}
				if ($pathInfo['extension'] === 'css') {
					$headHTMLInner .= '<link href="' . $webUrl . '" rel="stylesheet">' . "\r\n";
				}
				if (in_array($pathInfo['extension'], ['png', 'jpg', 'jpeg', 'gif'])) {
					list($imgW, $imgH) = getimagesize($fileDir);
					$bodyHTMLInner .= '<img src="' . $webUrl . '" width="' . $imgW . '" height="' . $imgH . '"/>' . "\r\n";
				}
			}

		}

		// for the last time.
		$headHTML[] = $headHTMLInner;
		$bodyHTML[] = $bodyHTMLInner;

		foreach ($headHTML as $key => $content) {

			$randName = uniqid();

			$createdHTML = <<<EOL
	<html>
		<head>
$content<style>
				img {
					display: block!important;
					float:left!important;
				}
			</style></head>
		<body>$bodyHTML[$key]</body>
	</html>
EOL;

			$htmlFileName = getcwd() . DIRECTORY_SEPARATOR . $randName . '.html';

			file_put_contents($htmlFileName, $createdHTML);

			$this->url = $this->baseUrl . $randName . '.html';

			$saveFile = getcwd() . DIRECTORY_SEPARATOR . $randName . '.zip';

			$this->fileSave($this->getUrl(), $saveFile);

			unlink($htmlFileName);

			$this->doIt($saveFile);

		}

		return $result;
	}

	public function getUrl()
	{
		$url = $this->googleUrl;
		$url = str_replace('{url}', urlencode($this->url), $url);
		$url = str_replace('{mobile}', $this->mobile === false ? 'desktop' : 'mobile', $url);
		$referer = $this->googleRefererUrl;
		$referer = str_replace('{url}', urlencode($this->url), $referer);
		$referer = str_replace('{mobile}', $this->mobile === false ? 'desktop' : 'mobile', $referer);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$headers = [
			'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36 OPR/47.0.2631.71',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
			'Accept-Language: tr-TR,tr;q=0.8,en-US;q=0.6,en;q=0.4',
			'Cache-Control: no-cache',
			'Upgrade-Insecure-Requests: 1', //Your referrer address
			'referer: ' . $referer,
		];

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_COOKIEJAR, \COOKIE_FILE);  //could be empty, but cause problems on some hosts
		curl_setopt($ch, CURLOPT_COOKIEFILE, \COOKIE_FILE);  //could be empty, but cause problems on some hosts
		curl_setopt($ch, CURLOPT_URL, $url);

		$out = curl_exec($ch);

		preg_match('/HREF="(.*?)"/si', $out, $getUrl);

		return html_entity_decode($getUrl[1]);
	}

	public function fileSave($Source, $fileDir)
	{
		$referer = $this->googleRefererUrl;
		$referer = str_replace('{url}', urlencode($this->url), $referer);
		$referer = str_replace('{mobile}', $this->mobile === false ? 'desktop' : 'mobile', $referer);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$headers = [
			'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36 OPR/47.0.2631.71',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
			'Accept-Language: tr-TR,tr;q=0.8,en-US;q=0.6,en;q=0.4',
			'Cache-Control: no-cache',
			'Upgrade-Insecure-Requests: 1', //Your referrer address
			'referer: ' . $referer,
		];

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_COOKIEJAR, \COOKIE_FILE);  //could be empty, but cause problems on some hosts
		curl_setopt($ch, CURLOPT_COOKIEFILE, \COOKIE_FILE);  //could be empty, but cause problems on some hosts
		curl_setopt($ch, CURLOPT_URL, $Source);

		$out = curl_exec($ch);

		$fp = fopen($fileDir, 'w');
		fwrite($fp, $out);
		fclose($fp);

		curl_close($ch);

	}

	public function rrmdir($path)
	{
		try {
			$iterator = new \DirectoryIterator($path);
			foreach ($iterator as $fileinfo) {
				if ($fileinfo->isDot()) continue;
				if ($fileinfo->isDir()) {
					if ($this->rrmdir($fileinfo->getPathname()))
						@rmdir($fileinfo->getPathname());
				}
				if ($fileinfo->isFile()) {
					@unlink($fileinfo->getPathname());
				}
			}
		} catch (\Exception $e) {
			return false;
		}

		return true;
	}

	public function rsearch($folder, $pattern)
	{
		$dir = new \RecursiveDirectoryIterator($folder);
		$ite = new \RecursiveIteratorIterator($dir);
		$files = new \RegexIterator($ite, $pattern, \RegexIterator::GET_MATCH);
		$fileList = [];
		foreach ($files as $file) {
			$fileList = array_merge($fileList, $file);
		}

		return $fileList;
	}

	public function findFolder($extensions)
	{

		$folders = $this->folders;

		array_walk($folders, function (&$value) {
			$value = preg_quote(getcwd() . DIRECTORY_SEPARATOR . $value, '/');
		});

		return $this->rsearch(getcwd(), '/^(?:' . implode('|', $folders) . ').*\.(?:' . implode('|', $extensions) . ')$/');
	}
}

?>
