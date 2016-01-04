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

class BoxCurl {

	protected $baseURL = null;

	protected $path    = null;

	protected $ssl     = true;

	protected $handler = null;

	protected $options = array();

	protected $headers = array('User-Agent' => 'All-in-One WP Migration');

	public function __construct() {
		// Check the cURL extension is loaded
		if (!extension_loaded('curl')) {
			throw new Exception('Box Factory requires cURL extension');
		}

		// Default configuration
		$this->setOption(CURLOPT_HEADER, false);
		$this->setOption(CURLOPT_RETURNTRANSFER, true);
		$this->setOption(CURLOPT_CONNECTTIMEOUT, 30);

		// Enable SSL support
		$this->setOption(CURLOPT_SSL_VERIFYHOST, 2);
		$this->setOption(CURLOPT_SSLVERSION, 1);
		$this->setOption(CURLOPT_CAINFO, __DIR__ . '/../certs/trusted-certs.crt');
		$this->setOption(CURLOPT_CAPATH, __DIR__ . '/../certs/');

		// Limit vulnerability surface area.  Supported in cURL 7.19.4+
		if (defined('CURLOPT_PROTOCOLS')) {
			$this->setOption(CURLOPT_PROTOCOLS, CURLPROTO_HTTPS);
		}

		if (defined('CURLOPT_REDIR_PROTOCOLS')) {
			$this->setOption(CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTPS);
		}
	}

	/**
	 * Set access token
	 *
	 * @param  string	$value Resouse path
	 * @return BoxCurl
	 */
	public function setAccessToken($value) {
		$this->setHeader('Authorization', "Bearer $value");
		return $this;
	}

	/**
	 * Get access token
	 *
	 * @return string
	 */
	public function getAccessToken() {
		return $this->getHeader('Authorization');
	}

	/**
	 * Set SSL mode
	 *
	 * @param  boolean	$value SSL Mode
	 * @return BoxCurl
	 */
	public function setSSL($value) {
		$this->ssl = $value;
		return $this;
	}
	/**
	 * Get SSL Mode
	 *
	 * @return boolean
	 */
	public function getSSL() {
		return $this->ssl;
	}

	/**
	 * Set cURL base URL
	 *
	 * @param  string	$value Base URL
	 * @return BoxCurl
	 */
	public function setBaseURL($value) {
		$this->baseURL = $value;
		return $this;
	}

	/**
	 * Get cURL base URL
	 *
	 * @return string
	 */
	public function getBaseURL() {
		return $this->baseURL;
	}

	/**
	 * Set cURL path
	 *
	 * @param  string	$value Resource path
	 * @return BoxCurl
	 */
	public function setPath($value) {
		$this->path = $value;
		return $this;
	}

	/**
	 * Get cURL path
	 *
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Set cURL option
	 *
	 * @param  int		$name  cURL option name
	 * @param  mixed	$value cURL option value
	 * @return BoxCurl
	 */
	public function setOption($name, $value) {
		$this->options[$name] = $value;
		return $this;
	}

	/**
	 * Get cURL option
	 *
	 * @param  int		$name cURL option name
	 * @return mixed
	 */
	public function getOption($name) {
		return $this->options[$name];
	}

	/**
	 * Set cURL header
	 *
	 * @param  string	$name  cURL header name
	 * @param  string	$value cURL header value
	 * @return BoxCurl
	 */
	public function setHeader($name, $value) {
		$this->headers[$name] = $value;
		return $this;
	}

	/**
	 * Get cURL header
	 *
	 * @param  string	$name cURL header name
	 * @return string
	 */
	public function getHeader($name) {
		return $this->headers[$name];
	}

	/**
	 * Make cURL request
	 *
	 * @return array
	 */
	public function makeRequest() {
		// cURL handler
		$this->handler = curl_init($this->getBaseURL() . $this->getPath());

		// Apply cURL headers
		$httpHeaders = array();
		foreach ($this->headers as $name => $value) {
			$httpHeaders[] = "$name: $value";
		}

		// Set headers
		$this->setOption(CURLOPT_HTTPHEADER, $httpHeaders);

		// SSL verify peer
		$this->setOption(CURLOPT_SSL_VERIFYPEER, $this->getSSL());

		// Apply cURL options
		foreach ($this->options as $name => $value) {
			curl_setopt($this->handler, $name, $value);
		}

		// HTTP request
		$response = curl_exec($this->handler);
		if ($response === false) {
			throw new Exception('Error executing HTTP request: ' . curl_error($this->handler));
		}

		// HTTP headers
		if ($this->getOption(CURLOPT_HEADER)) {
			return $this->httpParseHeaders($response);
		}

		return json_decode($response, true);
	}

	/**
	 * Destroy cURL handler
	 *
	 * @return void
	 */
	public function __destruct()
	{
		if ($this->handler !== null) {
			curl_close($this->handler);
		}
	}
}
