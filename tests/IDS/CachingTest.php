<?php

/**
 * PHPIDS
 * Requirements: PHP5, SimpleXML
 *
 * Copyright (c) 2007 PHPIDS group (http://php-ids.org)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the license.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @package	PHPIDS tests
 * @version	SVN: $Id:CachingTest.php 515 2007-09-15 13:43:40Z christ1an $
 */

require_once 'PHPUnit/Framework/TestCase.php';
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/../../lib');
require_once 'IDS/Init.php';
require_once 'IDS/Caching/Factory.php';

class IDS_CachingTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->path = dirname(__FILE__) . '/../../lib/IDS/Config/Config.ini';
        $this->init = IDS_Init::init($this->path);
    }

	function testCachingNone() {
    	$this->init->config['IDS_Caching']['caching'] = 'none';
    	$this->assertFalse(IDS_Caching::factory($this->init->config['IDS_Caching'], 'storage'));
    }

    function testCachingFile() {
        $this->init->config['IDS_Caching']['caching'] = 'file';
        $this->init->config['IDS_Caching']['expiration_time'] = 0;
        $this->assertTrue(IDS_Caching::factory($this->init->config['IDS_Caching'], 'storage') instanceof IDS_Caching_File);
    }

    function testCachingFileSetCache() {
        $this->init->config['IDS_Caching']['caching'] = 'file';
        $this->init->config['IDS_Caching']['expiration_time'] = 0;
        $cache = IDS_Caching::factory($this->init->config['IDS_Caching'], 'storage');
        $cache = $cache->setCache(array(1,2,3,4));
        $this->assertTrue($cache instanceof IDS_Caching_File);
    }

    function testCachingFileGetCache() {
        $this->init->config['IDS_Caching']['caching'] = 'file';
        $this->init->config['IDS_Caching']['path'] =  dirname(__FILE__) . '/../../lib/IDS/tmp/default_filter.cache';
        $this->init->config['IDS_Caching']['expiration_time'] = 0;
        $cache = IDS_Caching::factory($this->init->config['IDS_Caching'], 'storage');
        $cache = $cache->setCache(array(1,2,3,4));
        $this->assertEquals($cache->getCache(), array(1,2,3,4));
    }

    function testCachingSession() {
        $this->init->config['IDS_Caching']['caching'] = 'session';
        $this->assertTrue(IDS_Caching::factory($this->init->config['IDS_Caching'], 'storage') instanceof IDS_Caching_Session);
    }

    function testCachingSessionSetCache() {
        $this->init->config['IDS_Caching']['caching'] = 'session';

        $cache = IDS_Caching::factory($this->init->config['IDS_Caching'], 'storage');
        $cache = $cache->setCache(array(1,2,3,4));
        $this->assertTrue($cache instanceof IDS_Caching_Session);
    }

    function testCachingSessionGetCache() {
        $this->init->config['IDS_Caching']['caching'] = 'session';

        $cache = IDS_Caching::factory($this->init->config['IDS_Caching'], 'storage');
        $cache = $cache->setCache(array(1,2,3,4));
        $this->assertEquals($cache->getCache(), array(1,2,3,4));
    }

    function testCachingSessionGetCacheDestroyed() {
        $this->init->config['IDS_Caching']['caching'] = 'session';

        $cache = IDS_Caching::factory($this->init->config['IDS_Caching'], 'storage');
        $cache = $cache->setCache(array(1,2,3,4));
        $_SESSION['PHPIDS']['storage'] = null;
        $this->assertFalse($cache->getCache());
    }

    function tearDown() {
    	@unlink(dirname(__FILE__) . '/../../lib/IDS/tmp/default_filter.cache');
    	@unlink(dirname(__FILE__) . '/../../lib/IDS/tmp/memcache.timestamp');
    }
}
