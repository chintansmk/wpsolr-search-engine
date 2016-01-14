<?php

namespace wpsolr\exceptions;


class WPSOLR_Exception extends \Exception {


	public function get_message() {
		return $this->message;
	}

}