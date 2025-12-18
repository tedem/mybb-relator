<?php

declare(strict_types=1);

/**
 * Relator Plugin for MyBB
 *
 * Adds rel="nofollow ugc noopener external" to outbound links in posts.
 *
 * @author Medet "tedem" Erdal <hello@tedem.dev>
 *
 * @version 1.1.0
 *
 * @see https://github.com/tedem/mybb-relator
 *
 * @license MIT
 */

// Disallow direct access to this file for security reasons
if (! \defined('IN_MYBB')) {
    exit('(-_*) This file cannot be accessed directly.');
}

// Check for minimum PHP version requirement
if (PHP_VERSION_ID < 80200) {
    exit('(T_T) PHP version is not compatible for this plugin! Minimum PHP 8.2 is required.');
}

// Constants
\define('TEDEM_RELATOR_ID', 'relator');
\define('TEDEM_RELATOR_NAME', ucfirst(TEDEM_RELATOR_ID));
\define('TEDEM_RELATOR_AUTHOR', 'tedem');
\define('TEDEM_RELATOR_VERSION', '1.1.0');

// Hooks
if (! \defined('IN_ADMINCP')) {
    $plugins->add_hook('postbit', 'relator_main');
}

/**
 * Returns the plugin information.
 *
 * @return array The plugin information.
 */
function relator_info(): array
{
    $description = <<<'HTML'
<div style="margin-top: 1em;">
    Adds rel="nofollow ugc noopener external" to outbound links in posts.
</div>
HTML;

    if (relator_donation_status()) {
        $description .= relator_donation();
    }

    return [
        'name'          => TEDEM_RELATOR_NAME,
        'description'   => $description,
        'website'       => 'https://tedem.dev',
        'author'        => TEDEM_RELATOR_AUTHOR,
        'authorsite'    => 'https://tedem.dev',
        'version'       => TEDEM_RELATOR_VERSION,
        'codename'      => TEDEM_RELATOR_AUTHOR . '_' . TEDEM_RELATOR_ID,
        'compatibility' => '18*, 19*',
    ];
}

/**
 * Installs the plugin.
 */
function relator_install(): void
{
    global $cache;

    // add cache
    $plugins = $cache->read(TEDEM_RELATOR_AUTHOR);

    $plugins[TEDEM_RELATOR_ID] = [
        'name'     => TEDEM_RELATOR_NAME,
        'author'   => TEDEM_RELATOR_AUTHOR,
        'version'  => TEDEM_RELATOR_VERSION,
        'donation' => 1,
    ];

    $cache->update(TEDEM_RELATOR_AUTHOR, $plugins);
}

/**
 * Checks if the plugin is installed.
 */
function relator_is_installed(): bool
{
    global $cache;

    // has cache
    $plugins = $cache->read(TEDEM_RELATOR_AUTHOR);

    return isset($plugins[TEDEM_RELATOR_ID]);
}

/**
 * Uninstalls the plugin.
 */
function relator_uninstall(): void
{
    global $db, $cache;

    // remove cache
    $plugins = $cache->read(TEDEM_RELATOR_AUTHOR);

    unset($plugins[TEDEM_RELATOR_ID]);

    $cache->update(TEDEM_RELATOR_AUTHOR, $plugins);

    if (\count($plugins) === 0) {
        $db->delete_query('datacache', "title='" . TEDEM_RELATOR_AUTHOR . "'");
    }
}

/**
 * Activates the plugin.
 */
function relator_activate(): void
{

}

/**
 * Deactivates the plugin.
 */
function relator_deactivate(): void
{

}

/**
 * Main function for the Relator plugin.
 *
 * This function processes the post content and adds the `rel` attribute with
 * specific values to all external links (links that do not start with the forum's base URL).
 *
 * @param array $post The post data array containing the 'message' key with the post content.
 *
 * @return array The modified post data array with updated 'message' content.
 */
function relator_main(array $post): array
{
    global $mybb;

    $contents = $post['message'];

    // Is everything okay?
    if (empty($contents) || ! class_exists('DOMDocument') || ! class_exists('DOMXPath')) {
        return $post;
    }

    $url = $mybb->settings['bburl'] ?? '';

    $contents = mb_convert_encoding($contents, 'HTML-ENTITIES', 'UTF-8');

    $dom = new DOMDocument();
    $dom->loadHTML('<div>' . $contents . '</div>', \LIBXML_HTML_NOIMPLIED | \LIBXML_HTML_NODEFDTD);

    $xpath = new DOMXPath($dom);
    $links = $xpath->query("//a[not(starts-with(@href, '" . $url . "'))]");

    $rel_attribute = 'rel';
    $rel_value = 'nofollow ugc noopener external';

    foreach ($links as $link) {
        $link->setAttribute($rel_attribute, $rel_value);
    }

    // Remove HTML `<div>` tag.
    $contents = mb_substr($dom->saveHTML(), 5, -7, 'UTF-8');

    $post['message'] = $contents;

    return $post;
}

/**
 * Generates a donation message with links to support the developer.
 *
 * This function creates a donation message that includes links to "Buy me a coffee" and "KO-FI"
 * for supporting the developer. It also includes a link to close the donation message.
 *
 * @global object $mybb The MyBB core object.
 *
 * @return string The HTML string containing the donation message.
 */
function relator_donation(): string
{
    global $mybb;

    relator_donation_edit();

    $BMC = '<a href="https://www.buymeacoffee.com/tedem"><b>Buy me a coffee</b></a>';
    $KOFI = '<a href="https://ko-fi.com/tedem"><b>KO-FI</b></a>';

    $close_link = 'index.php?module=config-plugins&' . TEDEM_RELATOR_AUTHOR . '-' . TEDEM_RELATOR_ID . '=deactivate-donation&my_post_key=' . $mybb->post_code;
    $close_button = ' &mdash; <a href="' . $close_link . '"><b>Close Donation</b></a>';

    $message = '<b>Donation:</b> Support for new plugins, themes, etc. via ' . $BMC . ' or ' . $KOFI . $close_button;

    return '<div style="margin-top: 1em;">' . $message . '</div>';
}

/**
 * Checks the donation status for the current user.
 *
 * This function reads the donation status from the cache and determines
 * if the user has made a donation.
 *
 * @global array $cache The global cache array.
 *
 * @return bool True if the user has made a donation, false otherwise.
 */
function relator_donation_status(): bool
{
    global $cache;

    $donation = $cache->read(TEDEM_RELATOR_AUTHOR);

    return isset($donation[TEDEM_RELATOR_ID]['donation']) && $donation[TEDEM_RELATOR_ID]['donation'] === 1;
}

/**
 * Handles the donation edit action.
 *
 * This function checks if the provided post key matches the expected post code.
 * If the post key is valid and the donation action is set to 'deactivate-donation',
 * it updates the plugin's donation status to inactive and updates the cache.
 * A success message is then flashed and the user is redirected to the plugins configuration page.
 *
 * @global array $mybb The MyBB core object containing request data.
 * @global object $cache The MyBB cache object used to read and update cache data.
 */
function relator_donation_edit(): void
{
    global $mybb;

    if ($mybb->get_input('my_post_key') === $mybb->post_code) {
        global $cache;

        $plugins = $cache->read(TEDEM_RELATOR_AUTHOR);

        if ($mybb->get_input(TEDEM_RELATOR_AUTHOR . '-' . TEDEM_RELATOR_ID) === 'deactivate-donation') {
            $plugins[TEDEM_RELATOR_ID]['donation'] = 0;

            $cache->update(TEDEM_RELATOR_AUTHOR, $plugins);

            flash_message('The donation message has been successfully closed.', 'success');
            admin_redirect('index.php?module=config-plugins');
        }
    }
}
