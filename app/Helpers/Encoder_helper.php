<?php
function base64_safeurl($data=null,$action='e')
{
	if (!empty($data)) {
		switch ($action) {
			case 'e':
				$res=urlencode(base64_encode($data));
				break;
			case 'd':
				$res=base64_decode(urldecode($data));
				break;
			default:
				$res = false;
				break;
		}
		return $res;
	}
	return false;
}

?>