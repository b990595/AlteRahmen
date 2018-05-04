<?php
require_once '../db.config.php';

/**
 *
 */
return array(
    "paths" => array(
        "migrations" => "%%PHINX_CONFIG_DIR%%/migrations",
        "seeds" => "%%PHINX_CONFIG_DIR%%/db/seeds"
    ),
    "templates" => array(
        "file"=>"%%PHINX_CONFIG_DIR%%/JBMigrationTemplate.txt"
    ),
    "environments" => array(
        "default_migration_table" => "phinxlog",
        "default_database" => "legacy__db",
        "prod" => array(
            "adapter" => "mysql",
            "host" => FR_DBPHINX_HOST,
            "name" => "legacy__db",
            "user" => FR_DBPHINX_USER,
            "pass" => FR_DBPHINX_PASS,
            "port" => FR_DBPHINX_PORT,
            "charset" => "utf8",
            "collation" => "utf8_danish_ci"
        ),
        "test" => array(
            "adapter" => "mysql",
            "host" => FR_DBPHINX_HOST,
            "name" => "legacy__db",
            "user" => FR_DBPHINX_USER,
            "pass" => FR_DBPHINX_PASS,
            "port" => FR_DBPHINX_PORT,
            "charset" => "utf8",
            "collation" => "utf8_danish_ci"
        ),
        "dev-krj" => array(
            "adapter" => "mysql",
            "host" => FR_DBPHINX_HOST,
            "name" => "legacy__db",
            "user" => FR_DBPHINX_USER,
            "pass" => FR_DBPHINX_PASS,
            "port" => FR_DBPHINX_PORT,
            "charset" => "utf8",
            "collation" => "utf8_danish_ci"
        ),
        "dev-svi" => array(
            "adapter" => "mysql",
            "host" => FR_DBPHINX_HOST,
            "name" => "legacy__db",
            "user" => FR_DBPHINX_USER,
            "pass" => FR_DBPHINX_PASS,
            "port" => FR_DBPHINX_PORT,
            "charset" => "utf8",
            "collation" => "utf8_danish_ci"
        ),
        "dev-jmn" => array(
            "adapter" => "mysql",
            "host" => FR_DBPHINX_HOST,
            "name" => "legacy__db",
            "user" => FR_DBPHINX_USER,
            "pass" => FR_DBPHINX_PASS,
            "port" => FR_DBPHINX_PORT,
            "charset" => "utf8",
            "collation" => "utf8_danish_ci"
        ),
        "dev-beo" => array(
            "adapter" => "mysql",
            "host" => FR_DBPHINX_HOST,
            "name" => "legacy__db",
            "user" => FR_DBPHINX_USER,
            "pass" => FR_DBPHINX_PASS,
            "port" => FR_DBPHINX_PORT,
            "charset" => "utf8",
            "collation" => "utf8_danish_ci"
        ),
        "dev-mcm" => array(
            "adapter" => "mysql",
            "host" => FR_DBPHINX_HOST,
            "name" => "legacy__db",
            "user" => FR_DBPHINX_USER,
            "pass" => FR_DBPHINX_PASS,
            "port" => FR_DBPHINX_PORT,
            "charset" => "utf8",
            "collation" => "utf8_danish_ci"
        ),
        "dev-rane" => array(
            "adapter" => "mysql",
            "host" => FR_DBPHINX_HOST,
            "name" => "legacy__db",
            "user" => FR_DBPHINX_USER,
            "pass" => FR_DBPHINX_PASS,
            "port" => FR_DBPHINX_PORT,
            "charset" => "utf8",
            "collation" => "utf8_danish_ci"
        )

    ),
    "version_order" => "creation"
);
