<?php
namespace serhatozles\pagespeed;

/*
 * @author: Serhat ÖZLEŞ
 * @email: serhatozles@gmail.com
 */

// GLOBAL VARIABLES
define("BACKUP_FOLDER", getcwd() . DIRECTORY_SEPARATOR . 'backup');

class PageSpeed
{

	public $backup = true;
	public $discardImageDiffSize = true;
	public $console = false;
	public $url = null;
	public $baseUrl = false;
	public $mobile = false;
	public $googleUrl = 'https://developers.google.com/speed/pagespeed/insights/optimizeContents?url={url}&strategy={mobile}';

	public function getUrl()
	{
		$url = $this->googleUrl;
		$url = str_replace('{url}', urlencode($this->url), $url);
		$url = str_replace('{mobile}', $this->mobile === false ? 'desktop' : 'mobile', $url);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$headers = [
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
			'Accept-Language: tr-TR,tr;q=0.8,en-US;q=0.6,en;q=0.4	',
			'Cache-Control: no-cache',
			'Upgrade-Insecure-Requests: 1', //Your referrer address
		];

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);

		$out = curl_exec($ch);

		preg_match('/HREF="(.*?)"/si', $out, $getUrl);

		return html_entity_decode($getUrl[1]);
	}

	public function fixUrl()
	{

		if ($this->console === false) {
			echo 'it doesn\'t work without console.';
			exit;
		}

		$randName = uniqid();

		$saveFile = getcwd() . DIRECTORY_SEPARATOR . $randName . '.zip';
		$extractPath = getcwd() . DIRECTORY_SEPARATOR . $randName;

		$this->fileSave($this->getUrl(), $saveFile);

		$this->baseUrl = $this->baseUrl === false ? $this->url : $this->baseUrl;

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

				preg_match_all('/((?:css|js|image)[a-zA-Z0-9\/._\-\+]+)\: ((?:http).*)/mi', $manifest, $manifestList);

				if (count($manifestList[1]) > 0) {

					foreach ($manifestList[1] as $index => $path) {

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
							$manifestList[2][$index]);
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
					echo "couldn't get manifest list";
				}

			} else {
				echo "couldn't open $saveFile";
			}

		} else {
			echo 'file is not exist';
		}
	}

	public function fileSave($Source, $fileDir)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$headers = [
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
			'Accept-Language: tr-TR,tr;q=0.8,en-US;q=0.6,en;q=0.4	',
			'Cache-Control: no-cache',
			'Upgrade-Insecure-Requests: 1', //Your referrer address
		];

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $Source);

		$out = curl_exec($ch);

		$fp = fopen($fileDir, 'w');
		fwrite($fp, $out);
		fclose($fp);

		curl_close($ch);

	}

	public function rrmdir($dir)
	{
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir . "/" . $object) == "dir") $this->rrmdir($dir . "/" . $object); else unlink($dir . "/" . $object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
}

?>
