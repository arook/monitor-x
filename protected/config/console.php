<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Monitor',
  
  'import'=>array(
		'application.models.*',
		'application.components.*',
    'ext.YiiMongoDbSuite.*',
	),

  // application components
	'components'=>array(
  /*
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=monitor',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'root',
			'charset' => 'utf8',
		),
   */
    'mongodb'=>array(
      'class' => 'EMongoDB',
      'connectionString' => 'mongodb://192.168.1.99',
      'dbName' => 'monitor',
      'fsyncFlag' => true,
      'safeFlag' => true,
      'useCursor' => false,
    ),
    'cache'=>array(
      'class'=>'system.caching.CFileCache',
    ),
	),

  'params'=>array(
    'fetch_frequency' => 2,  //freq per hour
  ),
);
