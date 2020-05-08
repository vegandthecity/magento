<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Session\SaveHandler;

/**
 * Php native session save handler
 */
class Native extends \SessionHandler
{
    /**
     * Workaround for php7 session_regenerate_id error
     * @see https://bugs.php.net/bug.php?id=71187
     *
     * @param string $sessionId
     * @return string
     */
    public function read($sessionId)
    {
    	// 2020-05-08 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		// «SessionHandler::read(): The session id is too long or contains illegal characters,
		// valid characters are a-z, A-Z, 0-9 and '-,'
		// in vendor/magento/framework/Session/SaveHandler/Native.php on line 22»:
		// https://github.com/vegandthecity/magento/issues/39
    	try {
			return (string)parent::read($sessionId);
		}
		catch (\Exception $e) {
    		df_log_l($this, [
    			'message' => $e->getMessage()
    			,'sessionId' => $sessionId
				,'trace' => $e->getTraceAsString()
			], 'session');
    		throw $e;
		}
    }
}
