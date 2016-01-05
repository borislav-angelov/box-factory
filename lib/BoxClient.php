<?php
/**
 * Copyright (C) 2014-2015 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'BoxCurl.php';

class BoxClient {

	const API_URL             	= 'https://api.box.com/2.0';

	const UPLOAD_URL 			= 'https://upload.box.com/api/2.0';

	protected $accessToken = null;

	protected $ssl = null;

	public function __construct($accessToken, $ssl = true) {
		$this->accessToken = $accessToken;
		$this->ssl = $ssl;
	}

	public function listDrive() {
		$api = new BoxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setBaseURL(self::API_URL);
		$api->setSSL($this->ssl);
		$api->setPath('/folders/0');

		return $api->makeRequest();
	}

	public function listFolder($id) {
		$api = new BoxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setBaseURL(self::API_URL);
		$api->setSSL($this->ssl);
		$api->setPath("/folders/$id");

		return $api->makeRequest();
	}

	public function createFolder($name) {
		$api = new BoxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setBaseURL(self::API_URL);
		$api->setSSL($this->ssl);
		$api->setPath("/folders");
		$api->setOption(CURLOPT_POST, true);
		$api->setOption(CURLOPT_POSTFIELDS, json_encode(array(
			'name' => $name,
			'parent' => (object) array(
				'id' => 0,
			),
		)));

		return $api->makeRequest();
	}

	public function deleteFile($id) {
		$api = new BoxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setBaseURL(self::API_URL);
		$api->setSSL($this->ssl);
		$api->setPath("/files/$id");
		$api->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');

		return $api->makeRequest();
	}

	public function deleteFolder($id) {
		$api = new BoxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setBaseURL(self::API_URL);
		$api->setSSL($this->ssl);
		$api->setPath("/folders/$id");
		$api->setOption(CURLOPT_CUSTOMREQUEST, 'DELETE');

		return $api->makeRequest();
	}

	public function getAccountInfo() {
		$api = new BoxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setBaseURL(self::API_URL);
		$api->setSSL($this->ssl);
		$api->setPath("/users/me");

		return $api->makeRequest();
	}

	public function downloadFile($outStream, $id) {
		$api = new BoxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setBaseURL(self::API_URL);
		$api->setOption(CURLOPT_FILE, $outStream);
		$api->setOption(CURLOPT_FOLLOWLOCATION, true);
		$api->setPath("/files/$id/content");

		return $api->makeRequest();
	}

	public function uploadFile($pathname, $parentId) {
		$api = new BoxCurl;
		$api->setAccessToken($this->accessToken);
		$api->setBaseURL(self::UPLOAD_URL);
		$api->setPath('/files/content');
		$api->setOption(CURLOPT_POST, true);
		$api->setOption(CURLOPT_POSTFIELDS, array(
			'attributes' => json_encode(
				array(
					'name' => basename($pathname),
					'parent' => array(
						'id' => "$parentId",
					)
				)
			),
			'file' => "@{$pathname}",
		));

		return @$api->makeRequest();
	}


}
