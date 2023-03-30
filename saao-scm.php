<?php

/**
 * Plugin Name: SAAO SCM
 * Plugin URI: https://www.saao.ac.za/
 * Description: WordPress plugin for the SAAO's SCM sites.
 * Version: 0.1
 * Author: Christian Hettlage (SAAO/SALT)
 * Author URI: https://www.saao.ac.za/
 **/

require_once 'database.php';

/*
 * SETTINGS PAGE
 *
 * (based on https://themes.artbees.net/blog/custom-setting-page-in-wordpress/)
 */

add_action('admin_init', 'saao_scm_settings_init');

function saao_scm_settings_init()
{
    add_settings_section('saao_scm_database_settings', 'Database', 'saao_scm_database_settings', 'saao-scm');

    add_settings_field('saao_scm_database_host', 'Host', 'saao_scm_database_host_setting_markup', 'saao-scm', 'saao_scm_database_settings', array('label_for' => 'saao_scm_database_host'));

    add_settings_field('saao_scm_database_username', 'Username', 'saao_scm_database_username_setting_markup', 'saao-scm', 'saao_scm_database_settings', array('label_for' => 'saao_scm_database_username'));

    add_settings_field('saao_scm_database_password', 'Password', 'saao_scm_database_password_setting_markup', 'saao-scm', 'saao_scm_database_settings', array('label_for' => 'saao_scm_database_password'));

    add_settings_field('saao_scm_database_name', 'Database name', 'saao_scm_database_name_setting_markup', 'saao-scm', 'saao_scm_database_settings', array('label_for' => 'saao_scm_database_name'));

    register_setting('saao-scm', 'saao_scm_database_host');
    register_setting('saao-scm', 'saao_scm_database_username');
    register_setting('saao-scm', 'saao_scm_database_password');
    register_setting('saao-scm', 'saao_scm_database_name');
}

function saao_scm_database_settings()
{
    echo '<p>Please specify the connection details for the SCM database.</p>';
}

function saao_scm_database_host_setting_markup()
{
    ?>
    <input type="text" id="saao_scm_database_host" name="saao_scm_database_host"
           value="<?php echo get_option('saao_scm_database_host'); ?>">
    <?php
}

function saao_scm_database_username_setting_markup()
{
    ?>
    <input type="text" id="saao_scm_database_username" name="saao_scm_database_username"
           value="<?php echo get_option('saao_scm_database_username'); ?>">
    <?php
}

function saao_scm_database_password_setting_markup()
{
    ?>
    <input type="text" id="saao_scm_database_password" name="saao_scm_database_password"
           value="<?php echo get_option('saao_scm_database_password'); ?>">
    <?php
}

function saao_scm_database_name_setting_markup()
{
    ?>
    <input type="text" id="saao_scm_database_name" name="saao_scm_database_name"
           value="<?php echo get_option('saao_scm_database_name'); ?>">
    <?php
}

add_action('admin_menu', 'add_saao_scm_plugin_menu');

function add_saao_scm_plugin_menu()
{
    add_menu_page('SAAO SCM', 'SAAO SCM', 'administrator', 'saao-scm', 'saao_scm_settings_page', 'dashicons-money-alt', 65);
}

function saao_scm_settings_page()
{
    ?>
    <h1 class="wp-heading-inline">Settings</h1>
    <form method="POST" action="options.php">
        <?php
        settings_fields("saao-scm");
        do_settings_sections("saao-scm");
        submit_button();
        ?>
    </form>
    <?php
}

/**
 * GRAVITY FORMS
 */

$form_id = '_1';

add_action('gform_pre_submission' . $form_id, 'saao_scm_pre_submission_handler');

function saao_scm_pre_submission_handler($form)
{
    $year = date('Y');
    $sequential_number = saao_scm_get_sequential_number($year);

    $pdo = SCMDatabase::get_pdo();
    $stmt = $pdo->prepare("INSERT INTO RFQ (Year, Sequential_Number) VALUES (:year, :sequential_number)");
    $stmt->execute([':year' => $year, ':sequential_number' => $sequential_number]);
}

/**
 * Return the sequential number to use for a year.
 *
 * The returned value is the year's greatest sequential number in the database plus 1,
 * or 1 if there is no sequential number for the year yet.
 *
 * @param $year
 * @return int
 */
function saao_scm_get_sequential_number($year)
{
    $pdo = SCMDatabase::get_pdo();
    $sql = <<<SQL
SELECT MAX(Sequential_Number) AS max_sequential_number FROM RFQ WHERE Year = :year
SQL;
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':year' => $year]);
    $row = $stmt->fetch();
    $sequential_number = $row['max_sequential_number'] ? $row['max_sequential_number'] + 1 : 1;
    $stmt = null;
    return $sequential_number;
}
