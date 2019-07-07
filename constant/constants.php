<?php

/* 以下常量可以适当修改，需同时修改描述文字 */
define('DEFAULT_PASSWORD_LENGTH', 16);
define('MINIMUM_PASSWORD_LENGTH', 8);
define('MAXIMUM_PASSWORD_LENGTH', 128);

/**
 * 下载大文件时，一次读取的字节数。
 */
define('CHUNK_SIZE', 64 * 1024);

/**
 * 为垃圾回收程序提供一个参考的 Session 过期时间。
 */
define('GC_MAXLIFETIME', 86400);

/**
 * 为客户端提供 Cookie 的过期时间。
 */
define('COOKIE_LIFETIME', 86400);